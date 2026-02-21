<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Slider } from '@/components/ui/slider';
import { LCircleMarker, LMap, LTileLayer } from '@vue-leaflet/vue-leaflet';
import * as exifr from 'exifr';
import type { LeafletMouseEvent } from 'leaflet';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import 'leaflet/dist/leaflet.css';

interface GeocodeItem {
    display_name: string;
    lat: string;
    lon: string;
}

const imageFile = ref<File | null>(null);
const imagePreviewUrl = ref<string | null>(null);
const processedImageUrl = ref<string | null>(null);
const isDragging = ref(false);
const isGenerating = ref(false);
const errorMessage = ref('');

const searchQuery = ref('');
const shortAddress = ref('');
const fullAddress = ref('');
const latitude = ref<number | null>(null);
const longitude = ref<number | null>(null);
const latitudeInput = ref('');
const longitudeInput = ref('');
const textScale = ref(0.6);
const textScaleSlider = computed<number[]>({
    get: () => [textScale.value],
    set: (value) => {
        textScale.value = value[0] ?? 1;
    },
});
const dateTimeValue = ref(formatForDateTimeInput(new Date()));
const isMapReady = ref(false);

const mapZoom = ref(13);
const defaultCenter: [number, number] = [-8.654, 116.324];
const hasValidCoordinates = computed<boolean>(() => {
    if (latitude.value === null || longitude.value === null) {
        return false;
    }

    return isValidLatitude(latitude.value) && isValidLongitude(longitude.value);
});

const mapCenter = computed<[number, number]>(() => {
    if (hasValidCoordinates.value && latitude.value !== null && longitude.value !== null) {
        return [latitude.value, longitude.value];
    }

    return defaultCenter;
});

const onDragOver = (event: DragEvent): void => {
    event.preventDefault();
    isDragging.value = true;
};

const onDragLeave = (): void => {
    isDragging.value = false;
};

const onDrop = (event: DragEvent): void => {
    event.preventDefault();
    isDragging.value = false;

    const file = event.dataTransfer?.files?.[0];

    if (!file) {
        return;
    }

    void processUploadedFile(file);
};

const onSelectImage = (event: Event): void => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        return;
    }

    void processUploadedFile(file);
    target.value = '';
};

const processUploadedFile = async (file: File): Promise<void> => {
    errorMessage.value = '';

    if (!file.type.startsWith('image/')) {
        errorMessage.value = 'File harus berupa gambar.';
        return;
    }

    revokeUrl(imagePreviewUrl.value);
    revokeUrl(processedImageUrl.value);

    imageFile.value = file;
    imagePreviewUrl.value = URL.createObjectURL(file);
    processedImageUrl.value = null;

    await readExifData(file);
};

const readExifData = async (file: File): Promise<void> => {
    try {
        const metadata = await exifr.parse(file, { gps: true });

        if (!metadata) {
            return;
        }

        if (typeof metadata.latitude === 'number' && typeof metadata.longitude === 'number') {
            setCoordinates(metadata.latitude, metadata.longitude);
            mapZoom.value = 16;
            await reverseGeocode(metadata.latitude, metadata.longitude);
        }

        const exifDate = metadata.DateTimeOriginal ?? metadata.CreateDate ?? metadata.ModifyDate ?? metadata.DateTimeDigitized ?? null;

        if (exifDate instanceof Date) {
            dateTimeValue.value = formatForDateTimeInput(exifDate);
        }
    } catch {
        errorMessage.value = 'Metadata EXIF tidak terbaca. Kamu tetap bisa isi lokasi dan waktu manual.';
    }
};

const searchLocation = async (): Promise<void> => {
    if (!searchQuery.value.trim()) {
        return;
    }

    errorMessage.value = '';

    try {
        const endpoint = new URL('https://nominatim.openstreetmap.org/search');
        endpoint.searchParams.set('q', searchQuery.value);
        endpoint.searchParams.set('format', 'json');
        endpoint.searchParams.set('limit', '1');

        const response = await fetch(endpoint.toString(), {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('search_failed');
        }

        const data = (await response.json()) as GeocodeItem[];
        const firstResult = data[0];

        if (!firstResult) {
            errorMessage.value = 'Lokasi tidak ditemukan.';
            return;
        }

        const foundLatitude = Number.parseFloat(firstResult.lat);
        const foundLongitude = Number.parseFloat(firstResult.lon);

        setCoordinates(foundLatitude, foundLongitude);
        fullAddress.value = firstResult.display_name;
        shortAddress.value = firstResult.display_name.split(',').slice(0, 2).join(', ').trim();
        mapZoom.value = 16;
    } catch {
        errorMessage.value = 'Gagal mencari lokasi. Coba lagi beberapa saat.';
    }
};

const reverseGeocode = async (lat: number, lon: number): Promise<void> => {
    try {
        const endpoint = new URL('https://nominatim.openstreetmap.org/reverse');
        endpoint.searchParams.set('lat', String(lat));
        endpoint.searchParams.set('lon', String(lon));
        endpoint.searchParams.set('format', 'jsonv2');

        const response = await fetch(endpoint.toString(), {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('reverse_failed');
        }

        const data = (await response.json()) as { display_name?: string };
        const addressText = data.display_name ?? '';

        if (addressText) {
            fullAddress.value = addressText;
            shortAddress.value = addressText.split(',').slice(0, 2).join(', ').trim();
        }
    } catch {
        errorMessage.value = 'Koordinat berhasil dipilih, tapi alamat otomatis gagal dimuat.';
    }
};

const onMapClick = async (event: LeafletMouseEvent): Promise<void> => {
    const pickedLatitude = event.latlng.lat;
    const pickedLongitude = event.latlng.lng;

    setCoordinates(pickedLatitude, pickedLongitude);
    await reverseGeocode(pickedLatitude, pickedLongitude);
};

const setCoordinates = (lat: number, lon: number): void => {
    latitude.value = lat;
    longitude.value = lon;
    latitudeInput.value = lat.toFixed(6);
    longitudeInput.value = lon.toFixed(6);
};

watch(
    [latitudeInput, longitudeInput],
    ([latText, lonText]) => {
        const parsedLatitude = Number.parseFloat(latText);
        const parsedLongitude = Number.parseFloat(lonText);

        if (isValidLatitude(parsedLatitude) && isValidLongitude(parsedLongitude)) {
            latitude.value = parsedLatitude;
            longitude.value = parsedLongitude;
            return;
        }

        if (!latText.trim() && !lonText.trim()) {
            latitude.value = null;
            longitude.value = null;
        }
    },
    { flush: 'sync' },
);

const generateOverlayImage = async (): Promise<void> => {
    if (!imageFile.value || !imagePreviewUrl.value) {
        errorMessage.value = 'Upload gambar dulu sebelum memproses.';
        return;
    }

    isGenerating.value = true;
    errorMessage.value = '';

    try {
        const bitmap = await createImageBitmap(imageFile.value);
        const canvas = document.createElement('canvas');
        canvas.width = bitmap.width;
        canvas.height = bitmap.height;
        const ctx = canvas.getContext('2d');

        if (!ctx) {
            throw new Error('canvas_context_unavailable');
        }

        ctx.drawImage(bitmap, 0, 0);

        const baseFont = Math.max(20, Math.round(canvas.width * 0.028 * textScale.value));
        const lineHeight = Math.round(baseFont * 1.35);
        const padding = Math.round(baseFont * 0.8);

        const dateText = formatDateForOverlayUtc8(dateTimeValue.value);
        const latLonText =
            latitude.value !== null && longitude.value !== null
                ? `Lat : ${latitude.value.toFixed(6)}° Lon : ${longitude.value.toFixed(6)}°`
                : 'Lat : -° Lon : -°';
        const shortAddressText = shortAddress.value || '-';
        const fullAddressText = fullAddress.value || '-';
        const lines = [shortAddressText, fullAddressText, latLonText, `Waktu: ${dateText}`];

        ctx.font = `600 ${baseFont}px "Segoe UI", sans-serif`;
        const maxTextWidth = Math.max(...lines.map((line) => ctx.measureText(line).width));
        const mapThumbWidth = Math.round(baseFont * 4.8);
        const mapThumbHeight = lineHeight * lines.length;
        const gap = Math.round(baseFont * 0.8);
        const boxWidth = Math.min(canvas.width - padding * 2, Math.round(maxTextWidth + padding * 2 + mapThumbWidth + gap));
        const boxHeight = lineHeight * lines.length + padding * 2;
        const x = padding;
        const y = canvas.height - boxHeight - padding;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.72)';
        ctx.fillRect(x, y, boxWidth, boxHeight);

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = Math.max(2, Math.round(baseFont * 0.08));
        ctx.strokeRect(x, y, boxWidth, boxHeight);

        ctx.fillStyle = '#ffffff';
        const mapX = x + padding;
        const mapY = y + padding;
        await drawMiniMapPanel(ctx, mapX, mapY, mapThumbWidth, mapThumbHeight, latitude.value, longitude.value);

        const textStartX = mapX + mapThumbWidth + gap;
        lines.forEach((line, index) => {
            const textY = y + padding + lineHeight * (index + 0.82);
            ctx.fillText(line, textStartX, textY, boxWidth - (textStartX - x) - padding);
        });

        revokeUrl(processedImageUrl.value);
        processedImageUrl.value = canvas.toDataURL('image/jpeg', 0.95);
    } catch {
        errorMessage.value = 'Gagal memproses gambar. Coba file lain.';
    } finally {
        isGenerating.value = false;
    }
};

const downloadResult = (): void => {
    if (!processedImageUrl.value) {
        return;
    }

    const anchor = document.createElement('a');
    anchor.href = processedImageUrl.value;
    anchor.download = `overlay-${Date.now()}.jpg`;
    anchor.click();
};

function revokeUrl(url: string | null): void {
    if (url) {
        URL.revokeObjectURL(url);
    }
}

function formatForDateTimeInput(value: Date): string {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');
    const hours = String(value.getHours()).padStart(2, '0');
    const minutes = String(value.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function formatDateForOverlayUtc8(value: string): string {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    const formatted = new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Asia/Makassar',
    }).format(date);

    return `${formatted} UTC+08:00`;
}

function isValidLatitude(value: number): boolean {
    return Number.isFinite(value) && value >= -90 && value <= 90;
}

function isValidLongitude(value: number): boolean {
    return Number.isFinite(value) && value >= -180 && value <= 180;
}

async function drawMiniMapPanel(
    ctx: CanvasRenderingContext2D,
    x: number,
    y: number,
    width: number,
    height: number,
    lat: number | null,
    lon: number | null,
): Promise<void> {
    ctx.save();

    if (lat !== null && lon !== null && isValidLatitude(lat) && isValidLongitude(lon)) {
        const zoom = 15;
        const tile = latLonToTile(lat, lon, zoom);
        const tileUrl = `https://tile.openstreetmap.org/${zoom}/${tile.x}/${tile.y}.png`;

        try {
            const tileImage = await loadImage(tileUrl);
            ctx.drawImage(tileImage, x, y, width, height);

            const pinX = x + tile.fractionX * width;
            const pinY = y + tile.fractionY * height;
            ctx.fillStyle = '#0284c7';
            ctx.beginPath();
            ctx.arc(pinX, pinY, 8, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(pinX, pinY, 3, 0, Math.PI * 2);
            ctx.fill();
        } catch {
            ctx.fillStyle = '#f1f5f9';
            ctx.fillRect(x, y, width, height);
        }
    } else {
        ctx.fillStyle = '#f1f5f9';
        ctx.fillRect(x, y, width, height);
    }

    ctx.strokeStyle = '#cbd5e1';
    ctx.lineWidth = 1;
    ctx.strokeRect(x, y, width, height);

    ctx.restore();
}

function latLonToTile(lat: number, lon: number, zoom: number): { x: number; y: number; fractionX: number; fractionY: number } {
    const latRad = (lat * Math.PI) / 180;
    const n = 2 ** zoom;
    const xFloat = ((lon + 180) / 360) * n;
    const yFloat = ((1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2) * n;

    const x = Math.floor(xFloat);
    const y = Math.floor(yFloat);

    return {
        x,
        y,
        fractionX: xFloat - x,
        fractionY: yFloat - y,
    };
}

function loadImage(src: string): Promise<HTMLImageElement> {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.crossOrigin = 'anonymous';
        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error('image_load_failed'));
        image.src = src;
    });
}

onBeforeUnmount(() => {
    revokeUrl(imagePreviewUrl.value);
    revokeUrl(processedImageUrl.value);
});

onMounted(() => {
    isMapReady.value = true;
});
</script>

<template>
    <div class="mx-auto max-w-6xl space-y-6 px-4 py-8">
        <Card>
            <CardHeader>
                <CardTitle class="text-2xl text-blue-600">Unggah Gambar</CardTitle>
            </CardHeader>
            <CardContent>
                <label
                    class="block cursor-pointer rounded-xl border-2 border-dashed p-10 text-center transition"
                    :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-white'"
                    @dragover="onDragOver"
                    @dragleave="onDragLeave"
                    @drop="onDrop"
                >
                    <input type="file" accept="image/*" class="hidden" @change="onSelectImage" />
                    <p class="text-lg text-slate-600">Drag & drop gambar di sini atau klik area ini</p>
                    <Button type="button" class="mt-3 cursor-pointer">Pilih Gambar</Button>
                </label>
            </CardContent>
        </Card>

        <Card v-if="imagePreviewUrl">
            <CardHeader>
                <CardTitle class="text-2xl text-blue-600">Pilih Lokasi</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="mb-4">
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="w-full rounded-md border border-slate-300 bg-white px-3 py-2"
                        placeholder="Cari lokasi lalu tekan Enter..."
                        @keyup.enter="searchLocation"
                    />
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="overflow-hidden rounded-lg border border-slate-300">
                        <LMap
                            v-if="isMapReady"
                            :zoom="mapZoom"
                            :center="mapCenter"
                            :use-global-leaflet="false"
                            style="height: 330px"
                            @click="onMapClick"
                        >
                            <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base" name="OpenStreetMap" />
                            <LCircleMarker
                                v-if="hasValidCoordinates && latitude !== null && longitude !== null"
                                :lat-lng="[latitude, longitude]"
                                :radius="8"
                                color="#2563eb"
                                fill-color="#2563eb"
                                :fill-opacity="0.8"
                            />
                        </LMap>
                        <div v-else class="flex h-82.5 items-center justify-center bg-slate-200 text-sm text-slate-600">Menyiapkan peta...</div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Alamat Singkat</label>
                            <input v-model="shortAddress" type="text" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                            <input v-model="fullAddress" type="text" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Latitude</label>
                            <input v-model="latitudeInput" type="text" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Longitude</label>
                            <input v-model="longitudeInput" type="text" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2" />
                        </div>
                    </div>
                </div>
                <p v-if="(latitudeInput || longitudeInput) && !hasValidCoordinates" class="mt-2 text-sm font-medium text-amber-700">
                    Koordinat tidak valid. Latitude harus -90 s/d 90, longitude harus -180 s/d 180.
                </p>

                <div class="mt-5">
                    <label class="mb-1 block text-lg font-medium text-slate-700">Tanggal & Waktu</label>
                    <input v-model="dateTimeValue" type="datetime-local" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2" />
                </div>

                <div class="mt-5">
                    <Slider v-model="textScaleSlider" :min="0.6" :max="2" :step="0.1" class="w-full" />
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <Button class="cursor-pointer" type="button" :disabled="isGenerating" @click="generateOverlayImage">
                        {{ isGenerating ? 'Memproses...' : 'Tambahkan Lokasi ke Gambar' }}
                    </Button>
                    <Button v-if="processedImageUrl" class="cursor-pointer" type="button" variant="secondary" @click="downloadResult"
                        >Unduh Hasil</Button
                    >
                </div>

                <p v-if="errorMessage" class="mt-3 text-sm font-medium text-red-600">
                    {{ errorMessage }}
                </p>
            </CardContent>
        </Card>

        <section v-if="imagePreviewUrl" class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg text-slate-700">Asli</CardTitle>
                </CardHeader>
                <CardContent>
                    <img :src="imagePreviewUrl" alt="Original upload" class="w-full rounded-lg object-contain" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg text-slate-700">Hasil</CardTitle>
                </CardHeader>
                <CardContent>
                    <img v-if="processedImageUrl" :src="processedImageUrl" alt="Overlay result" class="w-full rounded-lg object-contain" />
                    <div
                        v-else
                        class="flex h-full min-h-56 items-center justify-center rounded-lg border border-dashed border-slate-300 text-slate-500"
                    >
                        Hasil overlay akan tampil di sini
                    </div>
                </CardContent>
            </Card>
        </section>
    </div>
</template>

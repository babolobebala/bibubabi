<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Toaster } from '@/components/ui/sonner';
import { Link } from '@inertiajs/vue3';
import { LCircleMarker, LMap, LTileLayer } from '@vue-leaflet/vue-leaflet';
import * as exifr from 'exifr';
import type { LeafletMouseEvent } from 'leaflet';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import 'leaflet/dist/leaflet.css';

interface OverlayTextSection {
    text: string;
    fontSize: number;
    fontWeight: number;
}

interface PreparedOverlaySection extends OverlayTextSection {
    lines: string[];
    lineHeight: number;
}

interface FakeExifPayload {
    make: string;
    model: string;
    software: string;
    dateTime: string;
    latitude: number | null;
    longitude: number | null;
    gpsDateStamp: string;
    gpsTimeStamp: [number, number, number];
}

const imageFile = ref<File | null>(null);
const imagePreviewUrl = ref<string | null>(null);
const processedImageUrl = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);
const originalExifIdentity = ref<{ make: string; model: string; software: string }>({
    make: 'Unknown',
    model: 'Unknown',
    software: 'Unknown',
});
const isDragging = ref(false);
const isGenerating = ref(false);

const shortAddress = ref('');
const fullAddress = ref('');
const latitude = ref<number | null>(null);
const longitude = ref<number | null>(null);
const latitudeInput = ref('');
const longitudeInput = ref('');
const textScale = ref(1);
const dateTimeValue = ref(formatForDateTimeInput(new Date()));
const isMapReady = ref(false);

const mapZoom = ref(13);
const PRESET_LATITUDE = -8.774062814832998;
const PRESET_LONGITUDE = 116.82512379247896;
const PRESET_RANDOM_DELTA = 0.000058;
const defaultCenter: [number, number] = [-8.7404, 116.8388];
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

const openFilePicker = (): void => {
    fileInputRef.value?.click();
};

const applyPresetLocation = async (): Promise<void> => {
    const randomizedLatitude = clampValue(PRESET_LATITUDE + randomOffset(PRESET_RANDOM_DELTA), -90, 90);
    const randomizedLongitude = clampValue(PRESET_LONGITUDE + randomOffset(PRESET_RANDOM_DELTA), -180, 180);

    setCoordinates(randomizedLatitude, randomizedLongitude);
    mapZoom.value = 16;
    await reverseGeocode(randomizedLatitude, randomizedLongitude);
};

const processUploadedFile = async (file: File): Promise<void> => {
    if (!file.type.startsWith('image/')) {
        showError('File harus berupa gambar.');
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

        originalExifIdentity.value = {
            make: pickExifString(metadata, ['Make', 'make'], 'Unknown'),
            model: pickExifString(metadata, ['Model', 'model'], 'Unknown'),
            software: pickExifString(metadata, ['Software', 'software'], 'Unknown'),
        };

        const exifDate = metadata.DateTimeOriginal ?? metadata.CreateDate ?? metadata.ModifyDate ?? metadata.DateTimeDigitized ?? null;

        if (exifDate instanceof Date) {
            dateTimeValue.value = formatForDateTimeInput(exifDate);
        }
    } catch {
        showError('Metadata EXIF tidak terbaca. Kamu tetap bisa isi lokasi dan waktu manual.');
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
        showError('Koordinat berhasil dipilih, tapi alamat otomatis gagal dimuat.');
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
        showError('Upload gambar dulu sebelum memproses.');
        return;
    }

    isGenerating.value = true;

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

        const minDimension = Math.min(canvas.width, canvas.height);
        const baseFont = Math.round(clampValue(minDimension * 0.035 * textScale.value, 14, 46));
        const detailFont = Math.max(12, Math.round(baseFont * 0.7));
        const metaFont = Math.max(12, Math.round(baseFont * 0.68));
        const panelPadding = Math.round(clampValue(baseFont * 0.72, 10, 34));
        const horizontalMargin = Math.round(clampValue(minDimension * 0.028, 12, 44));

        const dateText = formatDateForOverlayUtc8(dateTimeValue.value);
        const latLonText =
            latitude.value !== null && longitude.value !== null
                ? `Lat : ${latitude.value.toFixed(6)}° Lon : ${longitude.value.toFixed(6)}°`
                : 'Lat : -° Lon : -°';
        const shortAddressText = shortAddress.value || '-';
        const fullAddressText = fullAddress.value || '-';

        const panelMaxWidth = canvas.width - horizontalMargin * 2;
        const panelMinWidth = Math.min(300, panelMaxWidth);
        const panelWidth = Math.round(clampValue(canvas.width * 0.86, panelMinWidth, panelMaxWidth));
        let mapThumbWidth = Math.round(clampValue(panelWidth * 0.24, 68, 220));
        const mapGap = Math.round(clampValue(baseFont * 0.65, 10, 24));

        const minimumTextWidth = 150;
        let textAreaWidth = panelWidth - panelPadding * 2 - mapThumbWidth - mapGap;
        if (textAreaWidth < minimumTextWidth) {
            mapThumbWidth = Math.max(56, panelWidth - panelPadding * 2 - mapGap - minimumTextWidth);
            textAreaWidth = panelWidth - panelPadding * 2 - mapThumbWidth - mapGap;
        }

        const sections: OverlayTextSection[] = [
            { text: shortAddressText, fontSize: Math.round(baseFont * 1.05), fontWeight: 700 },
            { text: fullAddressText, fontSize: detailFont, fontWeight: 500 },
            { text: latLonText, fontSize: metaFont, fontWeight: 600 },
            { text: `Waktu: ${dateText}`, fontSize: metaFont, fontWeight: 600 },
        ];

        const preparedSections: PreparedOverlaySection[] = sections.map((section) => {
            ctx.font = `${section.fontWeight} ${section.fontSize}px "Segoe UI", sans-serif`;

            return {
                ...section,
                lines: wrapTextByWidth(ctx, section.text, textAreaWidth),
                lineHeight: Math.round(section.fontSize * 1.28),
            };
        });

        const sectionSpacing = Math.round(clampValue(baseFont * 0.38, 4, 12));
        const textContentHeight = preparedSections.reduce((total, section, index) => {
            const sectionHeight = section.lines.length * section.lineHeight;
            const spacing = index < preparedSections.length - 1 ? sectionSpacing : 0;

            return total + sectionHeight + spacing;
        }, 0);

        const contentHeight = Math.max(textContentHeight, Math.round(mapThumbWidth * 1.15));
        const mapThumbHeight = contentHeight;
        const boxHeight = contentHeight + panelPadding * 2;
        const x = horizontalMargin;
        const y = canvas.height - horizontalMargin - boxHeight;

        ctx.fillStyle = 'rgba(15, 23, 42, 0.72)';
        ctx.fillRect(x, y, panelWidth, boxHeight);

        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = Math.max(2, Math.round(baseFont * 0.08));
        ctx.strokeRect(x, y, panelWidth, boxHeight);

        const mapX = x + panelPadding;
        const mapY = y + panelPadding;
        await drawMiniMapPanel(ctx, mapX, mapY, mapThumbWidth, mapThumbHeight, latitude.value, longitude.value);

        const textStartX = mapX + mapThumbWidth + mapGap;
        let cursorY = mapY;
        ctx.textBaseline = 'top';
        preparedSections.forEach((section, sectionIndex) => {
            ctx.font = `${section.fontWeight} ${section.fontSize}px "Segoe UI", sans-serif`;
            ctx.fillStyle = '#ffffff';
            section.lines.forEach((line) => {
                ctx.fillText(line, textStartX, cursorY, textAreaWidth);
                cursorY += section.lineHeight;
            });

            if (sectionIndex < preparedSections.length - 1) {
                cursorY += sectionSpacing;
            }
        });

        const jpegBlob = await canvasToJpegBlob(canvas, 0.95);
        const fakeExifPayload = buildFakeExifPayload(
            dateTimeValue.value,
            latitude.value,
            longitude.value,
            originalExifIdentity.value.make,
            originalExifIdentity.value.model,
            originalExifIdentity.value.software,
        );
        const jpegWithFakeExifBlob = await injectFakeExifMetadata(jpegBlob, fakeExifPayload);

        revokeUrl(processedImageUrl.value);
        processedImageUrl.value = URL.createObjectURL(jpegWithFakeExifBlob);
    } catch {
        showError('Gagal memproses gambar. Coba file lain.');
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

        try {
            ctx.beginPath();
            ctx.rect(x, y, width, height);
            ctx.clip();

            const tileSize = 256;
            const centerX = x + width / 2;
            const centerY = y + height / 2;
            const n = 2 ** zoom;
            const tileOffsets = [-1, 0, 1];
            const drawJobs: Promise<void>[] = [];

            tileOffsets.forEach((dx) => {
                tileOffsets.forEach((dy) => {
                    const tileX = normalizeTileX(tile.x + dx, n);
                    const tileY = tile.y + dy;
                    if (tileY < 0 || tileY >= n) {
                        return;
                    }

                    const tileUrl = `https://tile.openstreetmap.org/${zoom}/${tileX}/${tileY}.png`;
                    const drawX = centerX + (dx - tile.fractionX) * tileSize;
                    const drawY = centerY + (dy - tile.fractionY) * tileSize;
                    const job = loadImage(tileUrl).then((image) => {
                        ctx.drawImage(image, drawX, drawY, tileSize, tileSize);
                    });
                    drawJobs.push(job);
                });
            });

            await Promise.all(drawJobs);

            const pinX = centerX;
            const pinY = centerY;
            const outerRadius = clampValue(width * 0.055, 4, 9);
            ctx.fillStyle = '#0284c7';
            ctx.beginPath();
            ctx.arc(pinX, pinY, outerRadius, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(pinX, pinY, Math.max(2, outerRadius * 0.38), 0, Math.PI * 2);
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

function normalizeTileX(value: number, modulo: number): number {
    return ((value % modulo) + modulo) % modulo;
}

function clampValue(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}

function wrapTextByWidth(ctx: CanvasRenderingContext2D, text: string, maxWidth: number): string[] {
    if (!text.trim()) {
        return ['-'];
    }

    if (ctx.measureText(text).width <= maxWidth) {
        return [text];
    }

    const words = text.split(/\s+/);
    const lines: string[] = [];
    let currentLine = '';

    words.forEach((word) => {
        const candidate = currentLine ? `${currentLine} ${word}` : word;
        if (ctx.measureText(candidate).width <= maxWidth) {
            currentLine = candidate;
            return;
        }

        if (currentLine) {
            lines.push(currentLine);
            currentLine = '';
        }

        if (ctx.measureText(word).width <= maxWidth) {
            currentLine = word;
            return;
        }

        const brokenWordParts = breakLongWordByWidth(ctx, word, maxWidth);
        lines.push(...brokenWordParts.slice(0, -1));
        currentLine = brokenWordParts[brokenWordParts.length - 1] ?? '';
    });

    if (currentLine) {
        lines.push(currentLine);
    }

    return lines.length ? lines : ['-'];
}

function breakLongWordByWidth(ctx: CanvasRenderingContext2D, text: string, maxWidth: number): string[] {
    const parts: string[] = [];
    let current = '';

    for (const char of text) {
        const candidate = current + char;
        if (ctx.measureText(candidate).width <= maxWidth) {
            current = candidate;
            continue;
        }

        if (current) {
            parts.push(current);
        }
        current = char;
    }

    if (current) {
        parts.push(current);
    }

    return parts.length ? parts : [text];
}

function canvasToJpegBlob(canvas: HTMLCanvasElement, quality: number): Promise<Blob> {
    return new Promise((resolve, reject) => {
        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    reject(new Error('canvas_blob_failed'));
                    return;
                }

                resolve(blob);
            },
            'image/jpeg',
            quality,
        );
    });
}

async function injectFakeExifMetadata(blob: Blob, payload: FakeExifPayload): Promise<Blob> {
    const bytes = new Uint8Array(await blob.arrayBuffer());
    const isJpeg = bytes.length > 3 && bytes[0] === 0xff && bytes[1] === 0xd8;
    if (!isJpeg) {
        return blob;
    }

    const exifSegment = buildExifApp1Segment(payload);
    const output = new Uint8Array(bytes.length + exifSegment.length);

    output.set(bytes.subarray(0, 2), 0);
    output.set(exifSegment, 2);
    output.set(bytes.subarray(2), 2 + exifSegment.length);

    return new Blob([output], { type: 'image/jpeg' });
}

function buildFakeExifPayload(
    dateTimeInput: string,
    latitude: number | null,
    longitude: number | null,
    make: string,
    model: string,
    software: string,
): FakeExifPayload {
    const sourceDate = parseDateInput(dateTimeInput);
    const utc8 = toTimeZoneDateParts(sourceDate, 'Asia/Makassar');
    const dateTime = `${utc8.year}:${pad2(utc8.month)}:${pad2(utc8.day)} ${pad2(utc8.hour)}:${pad2(utc8.minute)}:${pad2(utc8.second)}`;
    const gpsDateStamp = `${utc8.year}:${pad2(utc8.month)}:${pad2(utc8.day)}`;
    const gpsTimeStamp: [number, number, number] = [utc8.hour, utc8.minute, utc8.second];

    return {
        make,
        model,
        software,
        dateTime,
        latitude,
        longitude,
        gpsDateStamp,
        gpsTimeStamp,
    };
}

function pickExifString(metadata: Record<string, unknown>, keys: string[], fallback: string): string {
    for (const key of keys) {
        const value = metadata[key];
        if (typeof value === 'string' && value.trim()) {
            return value.trim();
        }
    }

    return fallback;
}

function buildExifApp1Segment(payload: FakeExifPayload): Uint8Array {
    const tiff = buildTiffBytes(payload);
    const exifBody = [0x45, 0x78, 0x69, 0x66, 0x00, 0x00, ...tiff];
    const segmentLength = exifBody.length + 2;
    const app1 = new Uint8Array(exifBody.length + 4);
    app1[0] = 0xff;
    app1[1] = 0xe1;
    app1[2] = (segmentLength >> 8) & 0xff;
    app1[3] = segmentLength & 0xff;
    app1.set(exifBody, 4);

    return app1;
}

function buildTiffBytes(payload: FakeExifPayload): number[] {
    const bytes: number[] = [];
    const tiffBaseOffset = 6;

    const writeByte = (value: number): void => {
        bytes.push(value & 0xff);
    };
    const writeUint16LE = (value: number): void => {
        writeByte(value & 0xff);
        writeByte((value >> 8) & 0xff);
    };
    const writeUint32LE = (value: number): void => {
        writeByte(value & 0xff);
        writeByte((value >> 8) & 0xff);
        writeByte((value >> 16) & 0xff);
        writeByte((value >> 24) & 0xff);
    };
    const setUint32LE = (index: number, value: number): void => {
        bytes[index] = value & 0xff;
        bytes[index + 1] = (value >> 8) & 0xff;
        bytes[index + 2] = (value >> 16) & 0xff;
        bytes[index + 3] = (value >> 24) & 0xff;
    };
    const appendAscii = (value: string): number => {
        const offset = bytes.length - tiffBaseOffset;
        for (const char of value) {
            writeByte(char.charCodeAt(0));
        }
        writeByte(0x00);

        return offset;
    };
    const appendRationals = (values: Array<[number, number]>): number => {
        const offset = bytes.length - tiffBaseOffset;
        values.forEach(([num, den]) => {
            writeUint32LE(num >>> 0);
            writeUint32LE(den >>> 0);
        });

        return offset;
    };

    const writeEntry = (tag: number, type: number, count: number): number => {
        writeUint16LE(tag);
        writeUint16LE(type);
        writeUint32LE(count);
        const valuePos = bytes.length;
        writeUint32LE(0);

        return valuePos;
    };

    writeByte(0x49);
    writeByte(0x49);
    writeUint16LE(42);
    writeUint32LE(8);

    writeUint16LE(6);
    const makePos = writeEntry(0x010f, 2, payload.make.length + 1);
    const modelPos = writeEntry(0x0110, 2, payload.model.length + 1);
    const softwarePos = writeEntry(0x0131, 2, payload.software.length + 1);
    const dateTimePos = writeEntry(0x0132, 2, payload.dateTime.length + 1);
    const exifIfdPointerPos = writeEntry(0x8769, 4, 1);
    const gpsIfdPointerPos = writeEntry(0x8825, 4, 1);
    writeUint32LE(0);

    setUint32LE(makePos, appendAscii(payload.make));
    setUint32LE(modelPos, appendAscii(payload.model));
    setUint32LE(softwarePos, appendAscii(payload.software));
    setUint32LE(dateTimePos, appendAscii(payload.dateTime));

    const exifIfdOffset = bytes.length - tiffBaseOffset;
    setUint32LE(exifIfdPointerPos, exifIfdOffset);

    writeUint16LE(2);
    const dateTimeOriginalPos = writeEntry(0x9003, 2, payload.dateTime.length + 1);
    const dateTimeDigitizedPos = writeEntry(0x9004, 2, payload.dateTime.length + 1);
    writeUint32LE(0);

    setUint32LE(dateTimeOriginalPos, appendAscii(payload.dateTime));
    setUint32LE(dateTimeDigitizedPos, appendAscii(payload.dateTime));

    const gpsIfdOffset = bytes.length - tiffBaseOffset;
    setUint32LE(gpsIfdPointerPos, gpsIfdOffset);

    const safeLat = payload.latitude ?? 0;
    const safeLon = payload.longitude ?? 0;
    const latRef = safeLat >= 0 ? 'N' : 'S';
    const lonRef = safeLon >= 0 ? 'E' : 'W';
    const latDms = decimalToDms(Math.abs(safeLat));
    const lonDms = decimalToDms(Math.abs(safeLon));

    writeUint16LE(6);
    const latRefPos = writeEntry(0x0001, 2, 2);
    const latPos = writeEntry(0x0002, 5, 3);
    const lonRefPos = writeEntry(0x0003, 2, 2);
    const lonPos = writeEntry(0x0004, 5, 3);
    const gpsDatePos = writeEntry(0x001d, 2, payload.gpsDateStamp.length + 1);
    const gpsTimePos = writeEntry(0x0007, 5, 3);
    writeUint32LE(0);

    setUint32LE(latRefPos, latRef.charCodeAt(0));
    setUint32LE(lonRefPos, lonRef.charCodeAt(0));
    setUint32LE(latPos, appendRationals(latDms));
    setUint32LE(lonPos, appendRationals(lonDms));
    setUint32LE(gpsDatePos, appendAscii(payload.gpsDateStamp));
    setUint32LE(
        gpsTimePos,
        appendRationals([
            [payload.gpsTimeStamp[0], 1],
            [payload.gpsTimeStamp[1], 1],
            [payload.gpsTimeStamp[2], 1],
        ]),
    );

    return bytes;
}

function decimalToDms(value: number): Array<[number, number]> {
    const degrees = Math.floor(value);
    const minutesFloat = (value - degrees) * 60;
    const minutes = Math.floor(minutesFloat);
    const secondsFloat = (minutesFloat - minutes) * 60;
    const secondsNumerator = Math.round(secondsFloat * 10000);

    return [
        [degrees, 1],
        [minutes, 1],
        [secondsNumerator, 10000],
    ];
}

function parseDateInput(value: string): Date {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return new Date();
    }

    return date;
}

function toTimeZoneDateParts(
    value: Date,
    timeZone: string,
): { year: number; month: number; day: number; hour: number; minute: number; second: number } {
    const formatter = new Intl.DateTimeFormat('en-US', {
        timeZone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });

    const parts = formatter.formatToParts(value);
    const get = (type: Intl.DateTimeFormatPartTypes): number => {
        const part = parts.find((item) => item.type === type)?.value ?? '0';
        return Number.parseInt(part, 10);
    };

    return {
        year: get('year'),
        month: get('month'),
        day: get('day'),
        hour: get('hour'),
        minute: get('minute'),
        second: get('second'),
    };
}

function pad2(value: number): string {
    return String(value).padStart(2, '0');
}

function randomOffset(maxAbs: number): number {
    return (Math.random() * 2 - 1) * maxAbs;
}

function showError(message: string): void {
    toast.error('Terjadi kesalahan', {
        description: message,
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
    <div>
        <Toaster rich-colors position="bottom-right" />

        <div class="border-b border-border px-4 py-4 sm:px-6">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="font-semibold text-foreground">Navigasi</span>
                <span class="mx-1 text-border">|</span>
                <Link href="/app" class="cursor-pointer text-muted-foreground transition hover:text-primary">Home</Link>
                <span class="text-border">›</span>
                <Link href="/app/tools" class="cursor-pointer text-muted-foreground transition hover:text-primary">Tools</Link>
                <span class="text-border">›</span>
                <span class="font-semibold text-foreground">Geotagging Gambar</span>
            </div>
        </div>

        <div class="mx-auto max-w-7xl space-y-4 px-3 py-4 sm:space-y-5 sm:px-4 sm:py-6">
            <Card>
            <CardHeader>
                <CardTitle class="text-lg sm:text-xl">Unggah Gambar</CardTitle>
            </CardHeader>
            <CardContent>
                <label
                    class="block cursor-pointer rounded-xl border-2 border-dashed p-6 text-center transition sm:p-8"
                    :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-white'"
                    @dragover="onDragOver"
                    @dragleave="onDragLeave"
                    @drop="onDrop"
                >
                    <input ref="fileInputRef" type="file" accept="image/*" class="hidden" @change="onSelectImage" />
                    <p class="text-md text-muted-foreground">Drag & drop gambar di sini atau klik area ini</p>
                    <Button type="button" class="mt-3 cursor-pointer" @click.prevent="openFilePicker">Pilih Gambar</Button>
                </label>
            </CardContent>
            </Card>

            <Card v-if="imagePreviewUrl">
                <CardHeader>
                    <CardTitle class="text-lg sm:text-xl">Pilih Lokasi</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div>
                        <Button type="button" class="cursor-pointer" @click="applyPresetLocation"> Kantor BPS </Button>
                    </div>
                    <div class="grid items-start gap-3 lg:grid-cols-[1.1fr_1fr]">
                        <div class="h-62.5 overflow-hidden rounded-lg border border-slate-300 sm:h-75 lg:h-85">
                            <LMap
                                v-if="isMapReady"
                                :zoom="mapZoom"
                                :center="mapCenter"
                                :use-global-leaflet="false"
                                style="height: 100%; width: 100%"
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
                            <div v-else class="flex h-full items-center justify-center bg-slate-200 text-sm text-slate-600">
                                Menyiapkan peta...
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Alamat Singkat</label>
                                <input
                                    v-model="shortAddress"
                                    type="text"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Alamat Lengkap</label>
                                <input
                                    v-model="fullAddress"
                                    type="text"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm"
                                />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Latitude</label>
                                <input
                                    v-model="latitudeInput"
                                    type="text"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm"
                                />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Longitude</label>
                                <input
                                    v-model="longitudeInput"
                                    type="text"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal & Waktu</label>
                                <input
                                    v-model="dateTimeValue"
                                    type="datetime-local"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <Button
                                    class="h-10 w-full cursor-pointer px-5 sm:w-auto"
                                    type="button"
                                    :disabled="isGenerating"
                                    @click="generateOverlayImage"
                                >
                                    {{ isGenerating ? 'Memproses...' : 'Tambahkan Lokasi ke Gambar' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                    <p v-if="(latitudeInput || longitudeInput) && !hasValidCoordinates" class="mt-2 text-sm font-medium text-amber-700">
                        Koordinat tidak valid. Latitude harus -90 s/d 90, longitude harus -180 s/d 180.
                    </p>
                </CardContent>
            </Card>

            <section v-if="imagePreviewUrl">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2">
                            <CardTitle class="text-lg sm:text-xl">Preview Hasil</CardTitle>
                            <Button v-if="processedImageUrl" class="cursor-pointer" type="button" variant="default" @click="downloadResult">
                                Unduh Hasil
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="flex h-70 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 sm:h-90 lg:h-105"
                        >
                            <img v-if="processedImageUrl" :src="processedImageUrl" alt="Overlay result" class="h-full w-full object-contain" />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center rounded-lg border border-dashed border-slate-300 text-slate-500"
                            >
                                Hasil overlay akan tampil di sini
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>
        </div>
    </div>
</template>

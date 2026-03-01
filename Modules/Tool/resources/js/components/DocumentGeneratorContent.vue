<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed, onBeforeUnmount, ref } from 'vue';
import { toast } from 'vue-sonner';

interface DocumentFormState {
    nama_petugas: string;
    anggaran_membiayai: string;
    tujuan: string;
    anggaran_diperiksa: string;
    jadwal: string;
    ringkasan_hasil: string;
    pejabat_dikunjungi: string;
}

interface ImageState {
    file: File | null;
    previewUrl: string | null;
}

type ImageTarget = 'foto_satu' | 'foto_dua';

const form = ref<DocumentFormState>({
    nama_petugas: '',
    anggaran_membiayai: '',
    tujuan: '',
    anggaran_diperiksa: '',
    jadwal: '',
    ringkasan_hasil: '',
    pejabat_dikunjungi: '',
});

const fotoSatu = ref<ImageState>({ file: null, previewUrl: null });
const fotoDua = ref<ImageState>({ file: null, previewUrl: null });
const isGenerating = ref(false);

const requiredFields: Array<keyof DocumentFormState> = [
    'nama_petugas',
    'anggaran_membiayai',
    'tujuan',
    'anggaran_diperiksa',
    'jadwal',
    'ringkasan_hasil',
    'pejabat_dikunjungi',
];

const summaryPreviewParagraphs = computed<string[]>(() => toParagraphs(form.value.ringkasan_hasil));
const officialsPreviewParagraphs = computed<string[]>(() => toParagraphs(form.value.pejabat_dikunjungi));

const canGenerate = computed<boolean>(() => {
    if (isGenerating.value) {
        return false;
    }

    return requiredFields.every((field) => form.value[field].trim().length > 0);
});

onBeforeUnmount(() => {
    revokePreview(fotoSatu.value.previewUrl);
    revokePreview(fotoDua.value.previewUrl);
});

async function generateDocument(): Promise<void> {
    if (!canGenerate.value) {
        toast.error('Semua field wajib diisi dulu.');
        return;
    }

    const csrfToken = getCsrfToken();

    if (!csrfToken) {
        toast.error('CSRF token tidak ditemukan.');
        return;
    }

    isGenerating.value = true;

    try {
        const formData = new FormData();

        for (const field of requiredFields) {
            formData.append(field, form.value[field].trim());
        }

        formData.append('has_foto_satu', fotoSatu.value.file ? '1' : '0');
        formData.append('has_foto_dua', fotoDua.value.file ? '1' : '0');

        if (fotoSatu.value.file) {
            formData.append('foto_satu', fotoSatu.value.file, fotoSatu.value.file.name);
        }

        if (fotoDua.value.file) {
            formData.append('foto_dua', fotoDua.value.file, fotoDua.value.file.name);
        }

        const response = await fetch('/app/tools/generator-dokumen/generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json, application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            },
            body: formData,
            credentials: 'same-origin',
        });

        if (!response.ok) {
            const message = await extractResponseError(response);
            throw new Error(message);
        }

        const blob = await response.blob();
        downloadBlob(blob, buildFileName());
        toast.success('Dokumen berhasil digenerate.');
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Gagal generate dokumen.');
    } finally {
        isGenerating.value = false;
    }
}

async function onSelectImage(event: Event, target: ImageTarget): Promise<void> {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (!file) {
        return;
    }

    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
        toast.error('Foto harus berformat JPG atau PNG.');
        input.value = '';
        return;
    }

    const imageState = imageStateFor(target);

    revokePreview(imageState.value.previewUrl);
    imageState.value = {
        file,
        previewUrl: URL.createObjectURL(file),
    };
}

function clearImage(target: ImageTarget): void {
    const imageState = imageStateFor(target);

    revokePreview(imageState.value.previewUrl);
    imageState.value = { file: null, previewUrl: null };
}

function imageStateFor(target: ImageTarget): typeof fotoSatu {
    return target === 'foto_satu' ? fotoSatu : fotoDua;
}

function getCsrfToken(): string | null {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? null;
}

async function extractResponseError(response: Response): Promise<string> {
    const contentType = response.headers.get('content-type') ?? '';

    if (contentType.includes('application/json')) {
        const payload = (await response.json()) as {
            message?: string;
            errors?: Record<string, string[]>;
        };

        const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;

        return firstError ?? payload.message ?? `Gagal generate dokumen (${response.status}).`;
    }

    return `Gagal generate dokumen (${response.status}).`;
}

function buildFileName(): string {
    const petugasSlug = form.value.nama_petugas
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    return `laporan-perjalanan-dinas-${petugasSlug || 'hasil'}.docx`;
}

function downloadBlob(blob: Blob, fileName: string): void {
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement('a');

    anchor.href = url;
    anchor.download = fileName;
    anchor.click();

    window.setTimeout(() => {
        URL.revokeObjectURL(url);
    }, 1000);
}

function toParagraphs(value: string): string[] {
    return value
        .split(/\n+/)
        .map((paragraph) => paragraph.trim())
        .filter((paragraph) => paragraph.length > 0);
}

function revokePreview(url: string | null): void {
    if (url) {
        URL.revokeObjectURL(url);
    }
}
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
        <Card class="py-0">
            <CardHeader class="border-b border-border">
                <CardTitle class="text-lg sm:text-xl">Generator Dokumen DOCX</CardTitle>
                <CardDescription>
                    Template sudah fix di module `Tool`. Text dan gambar diproses penuh lewat backend `PhpWord`.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4 p-4 sm:p-5">
                <div class="rounded-xl border border-dashed border-border bg-muted/40 px-4 py-3">
                    <p class="text-sm font-medium text-foreground">Template aktif</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        `Modules/Tool/resources/assets/kosongan.docx`
                    </p>
                    <p class="mt-2 text-xs text-success">Template siap digunakan.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="nama_petugas">Nama Petugas</label>
                        <input
                            id="nama_petugas"
                            v-model="form.nama_petugas"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="jadwal">Jadwal</label>
                        <input
                            id="jadwal"
                            v-model="form.jadwal"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="anggaran_membiayai">Anggaran yang Membiayai</label>
                        <input
                            id="anggaran_membiayai"
                            v-model="form.anggaran_membiayai"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="anggaran_diperiksa">Anggaran yang Diperiksa</label>
                        <input
                            id="anggaran_diperiksa"
                            v-model="form.anggaran_diperiksa"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="tujuan">Tujuan</label>
                    <textarea
                        id="tujuan"
                        v-model="form.tujuan"
                        rows="3"
                        class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="ringkasan_hasil">Ringkasan Hasil</label>
                    <textarea
                        id="ringkasan_hasil"
                        v-model="form.ringkasan_hasil"
                        rows="8"
                        class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="pejabat_dikunjungi">Pejabat dan Tempat yang Dikunjungi</label>
                    <textarea
                        id="pejabat_dikunjungi"
                        v-model="form.pejabat_dikunjungi"
                        rows="4"
                        class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="foto_satu">Foto Dokumentasi 1</label>
                        <input
                            id="foto_satu"
                            type="file"
                            accept="image/png,image/jpeg"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                            @change="(event) => onSelectImage(event, 'foto_satu')"
                        />
                        <Button v-if="fotoSatu.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage('foto_satu')">
                            Hapus Foto 1
                        </Button>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="foto_dua">Foto Dokumentasi 2</label>
                        <input
                            id="foto_dua"
                            type="file"
                            accept="image/png,image/jpeg"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                            @change="(event) => onSelectImage(event, 'foto_dua')"
                        />
                        <Button v-if="fotoDua.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage('foto_dua')">
                            Hapus Foto 2
                        </Button>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-muted-foreground">
                        Generator ini memakai `PhpWord` penuh, dengan dua slot dokumentasi terpisah untuk foto yang dipilih.
                    </p>
                    <Button type="button" class="cursor-pointer" :disabled="!canGenerate" @click="generateDocument">
                        {{ isGenerating ? 'Generating...' : 'Generate DOCX' }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <Card class="overflow-hidden py-0">
            <CardHeader class="border-b border-border bg-muted/30">
                <CardTitle class="text-lg sm:text-xl">Live Preview</CardTitle>
                <CardDescription>
                    Preview ini mendekati susunan dokumen. Hasil akhir `.docx` tetap mengikuti template Word.
                </CardDescription>
            </CardHeader>
            <CardContent class="bg-[radial-gradient(circle_at_top,_hsl(var(--muted))_0%,_transparent_55%)] p-4 sm:p-5">
                <div class="mx-auto max-w-[820px] rounded-[28px] border border-border bg-card p-5 shadow-sm sm:p-7">
                    <div class="space-y-5">
                        <div class="text-center">
                            <h2 class="text-lg font-bold tracking-wide text-foreground sm:text-xl">LAPORAN PERJALANAN DINAS</h2>
                        </div>

                        <div class="overflow-hidden rounded-2xl border-2 border-foreground/80">
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] border-b border-foreground/80 bg-muted/30 text-sm">
                                <div class="border-r border-foreground/80 px-3 py-2 font-semibold">Nama Petugas</div>
                                <div class="px-3 py-2">{{ form.nama_petugas || '-' }}</div>
                            </div>
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] border-b border-foreground/80 text-sm">
                                <div class="border-r border-foreground/80 px-3 py-2 font-semibold">Anggaran/Kegiatan yang Membiayai</div>
                                <div class="px-3 py-2">{{ form.anggaran_membiayai || '-' }}</div>
                            </div>
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] border-b border-foreground/80 bg-muted/20 text-sm">
                                <div class="border-r border-foreground/80 px-3 py-2 font-semibold">Tujuan</div>
                                <div class="px-3 py-2 whitespace-pre-line">{{ form.tujuan || '-' }}</div>
                            </div>
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] border-b border-foreground/80 text-sm">
                                <div class="border-r border-foreground/80 px-3 py-2 font-semibold">Anggaran/Kegiatan yang Diperiksa</div>
                                <div class="px-3 py-2">{{ form.anggaran_diperiksa || '-' }}</div>
                            </div>
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.5fr)] text-sm">
                                <div class="border-r border-foreground/80 px-3 py-2 font-semibold">Jadwal</div>
                                <div class="px-3 py-2">{{ form.jadwal || '-' }}</div>
                            </div>
                        </div>

                        <section class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide text-foreground">URUTAN KEGIATAN (RINGKASAN HASIL)</h3>
                            <div class="space-y-3 text-sm leading-6 text-foreground">
                                <p v-if="summaryPreviewParagraphs.length === 0" class="italic text-muted-foreground">
                                    Ringkasan hasil akan muncul di sini.
                                </p>
                                <p v-for="(paragraph, index) in summaryPreviewParagraphs" :key="`summary-${index}`" class="text-justify">
                                    {{ paragraph }}
                                </p>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide text-foreground">PEJABAT DAN TEMPAT YANG DIKUNJUNGI</h3>
                            <div class="space-y-2 text-sm leading-6 text-foreground">
                                <p v-if="officialsPreviewParagraphs.length === 0" class="italic text-muted-foreground">
                                    Daftar pejabat dan tempat yang dikunjungi akan muncul di sini.
                                </p>
                                <p v-for="(paragraph, index) in officialsPreviewParagraphs" :key="`official-${index}`">
                                    {{ paragraph }}
                                </p>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide text-foreground">DOKUMENTASI</h3>
                            <div class="space-y-3">
                                <div class="overflow-hidden rounded-2xl border border-border bg-muted/25 p-3">
                                    <img
                                        v-if="fotoSatu.previewUrl"
                                        :src="fotoSatu.previewUrl"
                                        alt="Preview dokumentasi 1"
                                        class="h-48 w-full rounded-xl object-cover"
                                    />
                                    <div v-else class="grid h-48 place-items-center rounded-xl border border-dashed border-border text-xs text-muted-foreground">
                                        Foto dokumentasi 1 belum dipilih
                                    </div>
                                </div>
                                <div class="overflow-hidden rounded-2xl border border-border bg-muted/25 p-3">
                                    <img
                                        v-if="fotoDua.previewUrl"
                                        :src="fotoDua.previewUrl"
                                        alt="Preview dokumentasi 2"
                                        class="h-48 w-full rounded-xl object-cover"
                                    />
                                    <div v-else class="grid h-48 place-items-center rounded-xl border border-dashed border-border text-xs text-muted-foreground">
                                        Foto dokumentasi 2 belum dipilih
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

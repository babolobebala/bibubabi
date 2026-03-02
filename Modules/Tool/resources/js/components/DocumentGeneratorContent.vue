<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { normalizeImageFile } from '../../../../Shared/resources/js/lib/heic';

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
const quickFillPreset = {
    nama_petugas: 'Fatih Mahawisesa',
    tujuan: 'Pengawasan Lapangan Sakernas Februari 2026 di Kecamatan Sekongkang',
    jadwal: '2026-01-27',
    anggaran_membiayai: 'GG 2905 BMA 004 005 A 524113',
    anggaran_diperiksa: 'GG 2905 BMA 004 005 A 524113',
} as const;

const requiredFields: Array<keyof DocumentFormState> = [
    'nama_petugas',
    'anggaran_membiayai',
    'tujuan',
    'anggaran_diperiksa',
    'jadwal',
    'ringkasan_hasil',
    'pejabat_dikunjungi',
];
const preserveWhitespaceFields: Array<keyof DocumentFormState> = ['ringkasan_hasil', 'pejabat_dikunjungi'];

const canGenerate = computed<boolean>(() => {
    if (isGenerating.value) {
        return false;
    }

    return requiredFields.every((field) => form.value[field].trim().length > 0);
});

const jadwalDisplay = computed<string>(() => formatIndonesianDate(form.value.jadwal));

onBeforeUnmount(() => {
    revokePreview(fotoSatu.value.previewUrl);
    revokePreview(fotoDua.value.previewUrl);
});

onMounted(() => {
    syncTextareaHeights();
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
            if (field === 'jadwal') {
                formData.append(field, jadwalDisplay.value);
                continue;
            }

            if (preserveWhitespaceFields.includes(field)) {
                formData.append(field, form.value[field]);
                continue;
            }

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
    const selectedFile = input.files?.[0] ?? null;

    if (!selectedFile) {
        return;
    }

    let file = selectedFile;

    try {
        const normalizedImage = await normalizeImageFile(file);
        file = normalizedImage.file;

        if (normalizedImage.convertedFromHeic) {
            toast.success('File HEIC berhasil dikonversi ke JPG.');
        }
    } catch (error) {
        console.error(error);

        if (error instanceof Error && error.message === 'unsupported_image_type') {
            toast.error('Foto harus berformat JPG, PNG, atau HEIC.');
        } else {
            toast.error('File HEIC gagal dikonversi. Coba pilih file lain.');
        }

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

function applyQuickFill(): void {
    form.value.nama_petugas = quickFillPreset.nama_petugas;
    form.value.tujuan = quickFillPreset.tujuan;
    form.value.jadwal = quickFillPreset.jadwal;
    form.value.anggaran_membiayai = quickFillPreset.anggaran_membiayai;
    form.value.anggaran_diperiksa = quickFillPreset.anggaran_diperiksa;
    syncTextareaHeights();
}

function handleParagraphTab(event: KeyboardEvent): void {
    if (event.key !== 'Tab') {
        return;
    }

    event.preventDefault();

    const target = event.target;

    if (!(target instanceof HTMLTextAreaElement)) {
        return;
    }

    const { selectionStart, selectionEnd, value } = target;

    if (event.shiftKey) {
        const lineStart = value.lastIndexOf('\n', Math.max(0, selectionStart - 1)) + 1;
        const currentLinePrefix = value.slice(lineStart, Math.min(lineStart + 1, value.length));

        if (currentLinePrefix !== '\t') {
            return;
        }

        const nextValue = value.slice(0, lineStart) + value.slice(lineStart + 1);
        target.value = nextValue;
        target.setSelectionRange(Math.max(lineStart, selectionStart - 1), Math.max(lineStart, selectionEnd - 1));
        target.dispatchEvent(new Event('input', { bubbles: true }));

        return;
    }

    const nextValue = value.slice(0, selectionStart) + '\t' + value.slice(selectionEnd);
    target.value = nextValue;
    target.setSelectionRange(selectionStart + 1, selectionStart + 1);
    target.dispatchEvent(new Event('input', { bubbles: true }));
}

function autoResizeTextarea(event: Event): void {
    const target = event.target;

    if (!(target instanceof HTMLTextAreaElement)) {
        return;
    }

    resizeTextarea(target);
}

function syncTextareaHeights(): void {
    void nextTick(() => {
        document.querySelectorAll<HTMLTextAreaElement>('textarea[data-autoresize="true"]').forEach((textarea) => {
            resizeTextarea(textarea);
        });
    });
}

function resizeTextarea(textarea: HTMLTextAreaElement): void {
    textarea.style.height = 'auto';
    textarea.style.height = `${textarea.scrollHeight}px`;
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

function revokePreview(url: string | null): void {
    if (url) {
        URL.revokeObjectURL(url);
    }
}

function formatIndonesianDate(value: string): string {
    if (value.trim() === '') {
        return '';
    }

    const date = new Date(`${value}T00:00:00`);

    if (Number.isNaN(date.getTime())) {
        return value.trim();
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
}
</script>

<template>
    <div class="mx-auto w-full max-w-5xl">
        <Card class="overflow-hidden border-border/70 py-0 shadow-sm">
            <CardHeader class="border-b border-border bg-[linear-gradient(135deg,hsl(var(--muted))_0%,hsl(var(--background))_65%)]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl space-y-1.5">
                        <CardTitle class="text-xl sm:text-2xl">Generator Dokumen DOCX</CardTitle>
                        <CardDescription class="text-sm leading-6">
                            Isi data perjalanan dinas dan unggah dokumentasi. Template tetap diambil dari module `Tool` dan hasil akhir diproses penuh
                            lewat backend `PhpWord`.
                        </CardDescription>
                        <div class="pt-2">
                            <Button type="button" variant="outline" class="cursor-pointer" @click="applyQuickFill"> Auto Fill Sakernas </Button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-border/80 bg-background/80 px-4 py-3 backdrop-blur">
                        <p class="text-xs font-semibold tracking-[0.24em] text-muted-foreground uppercase">Template aktif</p>
                        <p class="mt-2 text-sm font-medium text-foreground">kosongan.docx</p>
                        <p class="mt-1 text-xs text-muted-foreground">Modules/Tool/resources/assets/kosongan.docx</p>
                    </div>
                </div>
            </CardHeader>

            <CardContent class="space-y-8 p-4 sm:p-6 lg:p-8">
                <section class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold tracking-[0.2em] text-muted-foreground uppercase">Informasi Umum</h3>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-border/80">
                        <div class="hidden md:grid md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.9fr)_minmax(0,1.15fr)_minmax(0,1.75fr)]">
                            <div class="border-r border-b border-border/80 bg-muted/30 p-4">
                                <label class="text-sm font-medium text-foreground" for="nama_petugas">1. Nama Petugas</label>
                            </div>
                            <div class="border-r border-b border-border/80 p-3">
                                <input
                                    id="nama_petugas"
                                    v-model="form.nama_petugas"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>
                            <div class="border-r border-b border-border/80 bg-muted/30 p-4">
                                <label class="text-sm leading-6 font-medium text-foreground" for="anggaran_membiayai">
                                    4. Anggaran/Kegiatan yang Membiayai
                                </label>
                            </div>
                            <div class="border-b border-border/80 p-3">
                                <input
                                    id="anggaran_membiayai"
                                    v-model="form.anggaran_membiayai"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>

                            <div class="border-r border-b border-border/80 bg-muted/30 p-4">
                                <label class="text-sm font-medium text-foreground" for="tujuan">2. Tujuan</label>
                            </div>
                            <div class="border-r border-b border-border/80 p-3">
                                <textarea
                                    id="tujuan"
                                    v-model="form.tujuan"
                                    rows="1"
                                    data-autoresize="true"
                                    class="block w-full resize-none overflow-hidden rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                    @input="autoResizeTextarea"
                                />
                            </div>
                            <div class="border-r border-b border-border/80 bg-muted/30 p-4">
                                <label class="text-sm leading-6 font-medium text-foreground" for="anggaran_diperiksa">
                                    5. Anggaran/Kegiatan yang Diperiksa
                                </label>
                            </div>
                            <div class="border-b border-border/80 p-3">
                                <input
                                    id="anggaran_diperiksa"
                                    v-model="form.anggaran_diperiksa"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>

                            <div class="border-r border-border/80 bg-muted/30 p-4">
                                <label class="text-sm font-medium text-foreground" for="jadwal">3. Jadwal</label>
                            </div>
                            <div class="border-r border-border/80 p-3">
                                <input
                                    id="jadwal"
                                    v-model="form.jadwal"
                                    type="date"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                                <p class="mt-2 text-xs text-muted-foreground">
                                    {{ jadwalDisplay || 'Pilih tanggal dari kalender.' }}
                                </p>
                            </div>
                            <div class="border-r border-border/80 bg-muted/30 p-4">
                                <span class="text-sm font-medium text-foreground">6. Tanda Tangan Petugas</span>
                            </div>
                            <div class="p-3">
                                <div
                                    class="grid h-full min-h-[58px] place-items-center rounded-lg border border-dashed border-border text-xs text-muted-foreground"
                                >
                                    Disiapkan di template dokumen
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 md:hidden">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground" for="nama_petugas_mobile">1. Nama Petugas</label>
                                <input
                                    id="nama_petugas_mobile"
                                    v-model="form.nama_petugas"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground" for="anggaran_membiayai_mobile"
                                    >4. Anggaran/Kegiatan yang Membiayai</label
                                >
                                <input
                                    id="anggaran_membiayai_mobile"
                                    v-model="form.anggaran_membiayai"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground" for="tujuan_mobile">2. Tujuan</label>
                                <textarea
                                    id="tujuan_mobile"
                                    v-model="form.tujuan"
                                    rows="1"
                                    data-autoresize="true"
                                    class="block w-full resize-none overflow-hidden rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                    @input="autoResizeTextarea"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground" for="anggaran_diperiksa_mobile"
                                    >5. Anggaran/Kegiatan yang Diperiksa</label
                                >
                                <input
                                    id="anggaran_diperiksa_mobile"
                                    v-model="form.anggaran_diperiksa"
                                    type="text"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-foreground" for="jadwal_mobile">3. Jadwal</label>
                                <input
                                    id="jadwal_mobile"
                                    v-model="form.jadwal"
                                    type="date"
                                    class="block w-full rounded-lg border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors focus:border-foreground/30"
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ jadwalDisplay || 'Pilih tanggal dari kalender.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="border-t border-border/70"></div>

                <section class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold tracking-[0.2em] text-muted-foreground uppercase">Isi Dokumen</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="ringkasan_hasil">Ringkasan Hasil</label>
                            <textarea
                                id="ringkasan_hasil"
                                v-model="form.ringkasan_hasil"
                                rows="1"
                                data-autoresize="true"
                                class="block w-full resize-none overflow-hidden rounded-xl border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors [tab-size:4] focus:border-foreground/30"
                                @input="autoResizeTextarea"
                                @keydown="handleParagraphTab"
                            />
                            <p class="text-xs text-muted-foreground">Tekan `Tab` untuk membuat paragraf menjorok ke dalam.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="pejabat_dikunjungi">Pejabat dan Tempat yang Dikunjungi</label>
                            <textarea
                                id="pejabat_dikunjungi"
                                v-model="form.pejabat_dikunjungi"
                                rows="1"
                                data-autoresize="true"
                                class="block w-full resize-none overflow-hidden rounded-xl border border-input bg-background px-3 py-2.5 text-sm text-foreground transition-colors [tab-size:4] focus:border-foreground/30"
                                @input="autoResizeTextarea"
                                @keydown="handleParagraphTab"
                            />
                            <p class="text-xs text-muted-foreground">Tekan `Tab` untuk membuat paragraf menjorok ke dalam.</p>
                        </div>
                    </div>
                </section>

                <div class="border-t border-border/70"></div>

                <section class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold tracking-[0.2em] text-muted-foreground uppercase">Dokumentasi</h3>
                            <p class="mt-1 text-xs text-muted-foreground">Dua slot foto akan dimasukkan ke dokumen hasil generate.</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="foto_satu">Foto Dokumentasi 1</label>
                            <input
                                id="foto_satu"
                                type="file"
                                accept="image/*"
                                class="block w-full cursor-pointer rounded-xl border border-input bg-background px-3 py-2.5 text-sm text-foreground file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium"
                                @change="(event) => onSelectImage(event, 'foto_satu')"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ fotoSatu.file ? fotoSatu.file.name : 'Belum ada file dipilih.' }}
                            </p>
                            <Button v-if="fotoSatu.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage('foto_satu')">
                                Hapus Foto 1
                            </Button>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="foto_dua">Foto Dokumentasi 2</label>
                            <input
                                id="foto_dua"
                                type="file"
                                accept="image/*"
                                class="block w-full cursor-pointer rounded-xl border border-input bg-background px-3 py-2.5 text-sm text-foreground file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium"
                                @change="(event) => onSelectImage(event, 'foto_dua')"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ fotoDua.file ? fotoDua.file.name : 'Belum ada file dipilih.' }}
                            </p>
                            <Button v-if="fotoDua.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage('foto_dua')">
                                Hapus Foto 2
                            </Button>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col gap-4 border-t border-border pt-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-2xl text-xs leading-5 text-muted-foreground">
                        Generator ini memakai `PhpWord` penuh. Pastikan seluruh field wajib terisi sebelum mengunduh hasil `.docx`.
                    </p>
                    <Button type="button" size="lg" class="cursor-pointer px-6" :disabled="!canGenerate" @click="generateDocument">
                        {{ isGenerating ? 'Generating...' : 'Generate DOCX' }}
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

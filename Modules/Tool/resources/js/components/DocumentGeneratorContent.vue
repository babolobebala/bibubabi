<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import Docxtemplater from 'docxtemplater';
import PizZip from 'pizzip';
import { computed, onMounted, ref } from 'vue';
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

const isGenerating = ref(false);
const isTemplateReady = ref(false);
const isTemplateLoading = ref(true);
const templateBuffer = ref<ArrayBuffer | null>(null);
const form = ref<DocumentFormState>({
    nama_petugas: '',
    anggaran_membiayai: '',
    tujuan: '',
    anggaran_diperiksa: '',
    jadwal: '',
    ringkasan_hasil: '',
    pejabat_dikunjungi: '',
});

const requiredFields: Array<keyof DocumentFormState> = [
    'nama_petugas',
    'anggaran_membiayai',
    'tujuan',
    'anggaran_diperiksa',
    'jadwal',
    'ringkasan_hasil',
    'pejabat_dikunjungi',
];

const placeholderNames = [...requiredFields];

const canGenerate = computed<boolean>(() => {
    if (!isTemplateReady.value || isGenerating.value) {
        return false;
    }

    return requiredFields.every((field) => form.value[field].trim().length > 0);
});

const summaryPreviewParagraphs = computed<string[]>(() => toParagraphs(form.value.ringkasan_hasil));
const officialsPreviewParagraphs = computed<string[]>(() => toParagraphs(form.value.pejabat_dikunjungi));
const templateUrl = '/app/tools/generator-dokumen/template';

onMounted(() => {
    void loadTemplate();
});

async function generateDocument(): Promise<void> {
    if (!isTemplateReady.value || !templateBuffer.value) {
        toast.error('Template bawaan belum siap dipakai.');
        return;
    }

    const missingField = requiredFields.find((field) => form.value[field].trim().length === 0);

    if (missingField) {
        toast.error('Semua field wajib diisi dulu.');
        return;
    }

    isGenerating.value = true;

    try {
        const zip = new PizZip(templateBuffer.value.slice(0));
        const originalDocumentXml = zip.file('word/document.xml')?.asText();

        if (!originalDocumentXml) {
            throw new Error('word/document.xml tidak ditemukan di template.');
        }

        zip.file('word/document.xml', normalizeSplitPlaceholders(originalDocumentXml, placeholderNames));

        const doc = new Docxtemplater(zip, {
            linebreaks: true,
            paragraphLoop: true,
        });

        doc.render({
            nama_petugas: form.value.nama_petugas.trim(),
            anggaran_membiayai: form.value.anggaran_membiayai.trim(),
            tujuan: form.value.tujuan.trim(),
            anggaran_diperiksa: form.value.anggaran_diperiksa.trim(),
            jadwal: form.value.jadwal.trim(),
            ringkasan_hasil: form.value.ringkasan_hasil.trim(),
            pejabat_dikunjungi: form.value.pejabat_dikunjungi.trim(),
        });

        const outputBlob = doc.getZip().generate({
            type: 'blob',
            mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        });

        downloadBlob(outputBlob, buildFileName());
        toast.success('Dokumen berhasil digenerate.');
    } catch (error) {
        console.error(error);
        toast.error(extractErrorMessage(error, 'Gagal generate dokumen.'));
    } finally {
        isGenerating.value = false;
    }
}

async function loadTemplate(): Promise<void> {
    isTemplateLoading.value = true;
    isTemplateReady.value = false;

    try {
        const response = await fetch(templateUrl, {
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`Template gagal dimuat (${response.status}).`);
        }

        const contentType = response.headers.get('content-type') ?? '';

        if (!contentType.includes('wordprocessingml.document')) {
            throw new Error('Response template bukan file DOCX.');
        }

        const buffer = await response.arrayBuffer();
        const zip = new PizZip(buffer);
        const xml = zip.file('word/document.xml')?.asText();

        if (!xml) {
            throw new Error('word/document.xml tidak ditemukan di template.');
        }

        templateBuffer.value = buffer;
        isTemplateReady.value = true;
    } catch (error) {
        console.error(error);
        templateBuffer.value = null;
        isTemplateReady.value = false;
        toast.error(extractErrorMessage(error, 'Template bawaan gagal dimuat.'));
    } finally {
        isTemplateLoading.value = false;
    }
}

function normalizeSplitPlaceholders(xml: string, placeholderKeys: string[]): string {
    let normalizedXml = xml;

    for (const key of placeholderKeys) {
        normalizedXml = normalizePlaceholder(normalizedXml, key);
    }

    return normalizedXml;
}

function normalizePlaceholder(xml: string, key: string): string {
    const escapedKey = escapeRegExp(key);
    const runPattern = `<w:r[^>]*>(?:<w:rPr[\\s\\S]*?<\\/w:rPr>)?(?:<w:t[^>]*>\\{<\\/w:t>|<w:t[^>]*>${escapedKey}<\\/w:t>|<w:t[^>]*>\\}<\\/w:t>)<\\/w:r>`;
    const proofPattern = `<w:proofErr[^>]*/>`;
    const placeholderPattern = new RegExp(
        `${runPattern}(?:${proofPattern})?${runPattern}(?:${proofPattern})?${runPattern}`,
        'g',
    );

    return xml.replace(placeholderPattern, `<w:r><w:t>{${key}}</w:t></w:r>`);
}

function escapeRegExp(value: string): string {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function buildFileName(): string {
    const baseName = 'laporan-perjalanan-dinas';
    const petugasSlug = form.value.nama_petugas
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    return `${baseName}-${petugasSlug || 'hasil'}.docx`;
}

function extractErrorMessage(error: unknown, fallback: string): string {
    if (error instanceof Error && error.message.trim().length > 0) {
        return error.message;
    }

    return fallback;
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
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
        <Card class="py-0">
            <CardHeader class="border-b border-border">
                <CardTitle class="text-lg sm:text-xl">Generator Dokumen DOCX</CardTitle>
                <CardDescription>
                    Template sudah fix di module `Tool`, jadi user tinggal isi form lalu unduh hasilnya. Belum memakai database.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4 p-4 sm:p-5">
                <div class="rounded-xl border border-dashed border-border bg-muted/40 px-4 py-3">
                    <p class="text-sm font-medium text-foreground">Template aktif</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        `Modules/Tool/resources/assets/kosongan.docx`
                    </p>
                    <p class="mt-2 text-xs" :class="isTemplateReady ? 'text-success' : 'text-destructive'">
                        {{ isTemplateLoading ? 'Memuat template...' : isTemplateReady ? 'Template siap digunakan.' : 'Template belum siap.' }}
                    </p>
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

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-muted-foreground">
                        Placeholder template Word yang pecah akan dirapikan otomatis sebelum generate.
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
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-dashed border-border bg-muted/35 p-6 text-center text-xs text-muted-foreground">
                                    Foto dokumentasi disisipkan manual setelah `.docx` digenerate.
                                </div>
                                <div class="rounded-2xl border border-dashed border-border bg-muted/35 p-6 text-center text-xs text-muted-foreground">
                                    Slot dokumentasi kedua juga masih manual.
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

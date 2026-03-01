<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    AlignmentType,
    BorderStyle,
    Document,
    ImageRun,
    Packer,
    Paragraph,
    Table,
    TableCell,
    TableRow,
    TextRun,
    VerticalAlign,
    WidthType,
} from 'docx';
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
    revokeImagePreview(fotoSatu.value.previewUrl);
    revokeImagePreview(fotoDua.value.previewUrl);
});

async function onSelectImage(event: Event, target: typeof fotoSatu | typeof fotoDua): Promise<void> {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    if (!file) {
        clearImage(target);
        return;
    }

    if (!isSupportedImage(file)) {
        toast.error('Foto harus berformat JPG, JPEG, atau PNG.');
        input.value = '';
        return;
    }

    revokeImagePreview(target.value.previewUrl);

    target.value = {
        file,
        previewUrl: URL.createObjectURL(file),
    };

    input.value = '';
}

function clearImage(target: typeof fotoSatu | typeof fotoDua): void {
    revokeImagePreview(target.value.previewUrl);
    target.value = { file: null, previewUrl: null };
}

async function generateDocument(): Promise<void> {
    if (!canGenerate.value) {
        toast.error('Semua field wajib diisi dulu.');
        return;
    }

    isGenerating.value = true;

    try {
        const documentationTable = await buildDocumentationTable();
        const document = new Document({
            sections: [
                {
                    properties: {},
                    children: [
                        new Paragraph({
                            alignment: AlignmentType.CENTER,
                            spacing: { after: 220 },
                            children: [
                                new TextRun({
                                    text: 'LAPORAN PERJALANAN DINAS',
                                    bold: true,
                                    size: 28,
                                }),
                            ],
                        }),
                        buildInformationTable(),
                        new Paragraph({
                            spacing: { before: 260, after: 120 },
                            alignment: AlignmentType.CENTER,
                            children: [new TextRun({ text: 'URUTAN KEGIATAN (RINGKASAN HASIL)', bold: true, size: 24 })],
                        }),
                        ...buildParagraphsFromText(form.value.ringkasan_hasil),
                        new Paragraph({
                            spacing: { before: 260, after: 120 },
                            alignment: AlignmentType.CENTER,
                            children: [new TextRun({ text: 'PEJABAT DAN TEMPAT YANG DIKUNJUNGI', bold: true, size: 24 })],
                        }),
                        ...buildParagraphsFromText(form.value.pejabat_dikunjungi),
                        new Paragraph({
                            spacing: { before: 260, after: 120 },
                            alignment: AlignmentType.CENTER,
                            children: [new TextRun({ text: 'DOKUMENTASI', bold: true, size: 24 })],
                        }),
                        documentationTable,
                    ],
                },
            ],
        });

        const blob = await Packer.toBlob(document);
        downloadBlob(blob, buildFileName());
        toast.success('Dokumen DOCX berhasil digenerate.');
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Gagal generate dokumen DOCX.');
    } finally {
        isGenerating.value = false;
    }
}

function buildInformationTable(): Table {
    return new Table({
        width: {
            size: 100,
            type: WidthType.PERCENTAGE,
        },
        rows: [
            buildInformationRow('Nama Petugas', form.value.nama_petugas),
            buildInformationRow('Anggaran/Kegiatan yang Membiayai', form.value.anggaran_membiayai),
            buildInformationRow('Tujuan', form.value.tujuan),
            buildInformationRow('Anggaran/Kegiatan yang Diperiksa', form.value.anggaran_diperiksa),
            buildInformationRow('Jadwal', form.value.jadwal),
        ],
    });
}

function buildInformationRow(label: string, value: string): TableRow {
    return new TableRow({
        children: [
            new TableCell({
                width: {
                    size: 32,
                    type: WidthType.PERCENTAGE,
                },
                verticalAlign: VerticalAlign.CENTER,
                borders: tableBorders(),
                children: [
                    new Paragraph({
                        children: [new TextRun({ text: label, bold: true })],
                    }),
                ],
            }),
            new TableCell({
                width: {
                    size: 68,
                    type: WidthType.PERCENTAGE,
                },
                verticalAlign: VerticalAlign.CENTER,
                borders: tableBorders(),
                children: buildParagraphsFromText(value),
            }),
        ],
    });
}

async function buildDocumentationTable(): Promise<Table> {
    const imageOneCell = await buildDocumentationCell(fotoSatu.value.file, 'Dokumentasi 1');
    const imageTwoCell = await buildDocumentationCell(fotoDua.value.file, 'Dokumentasi 2');

    return new Table({
        width: {
            size: 100,
            type: WidthType.PERCENTAGE,
        },
        rows: [
            new TableRow({
                children: [imageOneCell, imageTwoCell],
            }),
        ],
    });
}

async function buildDocumentationCell(file: File | null, label: string): Promise<TableCell> {
    if (!file) {
        return new TableCell({
            width: { size: 50, type: WidthType.PERCENTAGE },
            borders: tableBorders(),
            children: [
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { before: 800, after: 800 },
                    children: [new TextRun({ text: `${label} belum dipilih`, italics: true })],
                }),
            ],
        });
    }

    const imageType = getImageType(file);
    const buffer = await file.arrayBuffer();

    return new TableCell({
        width: { size: 50, type: WidthType.PERCENTAGE },
        borders: tableBorders(),
        children: [
            new Paragraph({
                alignment: AlignmentType.CENTER,
                spacing: { before: 120, after: 120 },
                children: [
                    new ImageRun({
                        type: imageType,
                        data: buffer,
                        transformation: {
                            width: 250,
                            height: 190,
                        },
                    }),
                ],
            }),
        ],
    });
}

function buildParagraphsFromText(value: string): Paragraph[] {
    const paragraphs = toParagraphs(value);

    if (paragraphs.length === 0) {
        return [new Paragraph({ children: [new TextRun('')] })];
    }

    return paragraphs.map(
        (paragraph) =>
            new Paragraph({
                spacing: { after: 140 },
                alignment: AlignmentType.JUSTIFIED,
                children: [new TextRun({ text: paragraph })],
            }),
    );
}

function tableBorders() {
    return {
        top: { style: BorderStyle.SINGLE, size: 1, color: '000000' },
        bottom: { style: BorderStyle.SINGLE, size: 1, color: '000000' },
        left: { style: BorderStyle.SINGLE, size: 1, color: '000000' },
        right: { style: BorderStyle.SINGLE, size: 1, color: '000000' },
    };
}

function toParagraphs(value: string): string[] {
    return value
        .split(/\n+/)
        .map((paragraph) => paragraph.trim())
        .filter((paragraph) => paragraph.length > 0);
}

function isSupportedImage(file: File): boolean {
    return ['image/jpeg', 'image/jpg', 'image/png'].includes(file.type);
}

function getImageType(file: File): 'jpg' | 'png' {
    return file.type === 'image/png' ? 'png' : 'jpg';
}

function buildFileName(): string {
    const petugasSlug = form.value.nama_petugas
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    return `laporan-perjalanan-dinas-docx-${petugasSlug || 'hasil'}.docx`;
}

function downloadBlob(blob: Blob, fileName: string): void {
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.download = fileName;
    anchor.click();
    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}

function revokeImagePreview(url: string | null): void {
    if (url) {
        URL.revokeObjectURL(url);
    }
}
</script>

<template>
    <div class="grid gap-4 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
        <Card class="py-0">
            <CardHeader class="border-b border-border">
                <CardTitle class="text-lg sm:text-xl">Generator DOCX dari Kode</CardTitle>
                <CardDescription>
                    Versi ini tidak memakai template Word. Dokumen dibangun langsung dari kode, jadi foto dokumentasi bisa ikut dimasukkan.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4 p-4 sm:p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="docx_nama_petugas">Nama Petugas</label>
                        <input
                            id="docx_nama_petugas"
                            v-model="form.nama_petugas"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="docx_jadwal">Jadwal</label>
                        <input
                            id="docx_jadwal"
                            v-model="form.jadwal"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="docx_anggaran_membiayai">Anggaran yang Membiayai</label>
                        <input
                            id="docx_anggaran_membiayai"
                            v-model="form.anggaran_membiayai"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground" for="docx_anggaran_diperiksa">Anggaran yang Diperiksa</label>
                        <input
                            id="docx_anggaran_diperiksa"
                            v-model="form.anggaran_diperiksa"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="docx_tujuan">Tujuan</label>
                    <textarea
                        id="docx_tujuan"
                        v-model="form.tujuan"
                        rows="3"
                        class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="docx_ringkasan_hasil">Ringkasan Hasil</label>
                    <textarea
                        id="docx_ringkasan_hasil"
                        v-model="form.ringkasan_hasil"
                        rows="7"
                        class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="docx_pejabat_dikunjungi">Pejabat dan Tempat yang Dikunjungi</label>
                    <textarea
                        id="docx_pejabat_dikunjungi"
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
                            @change="(event) => onSelectImage(event, fotoSatu)"
                        />
                        <Button v-if="fotoSatu.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage(fotoSatu)">
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
                            @change="(event) => onSelectImage(event, fotoDua)"
                        />
                        <Button v-if="fotoDua.file" type="button" variant="outline" class="cursor-pointer" @click="clearImage(fotoDua)">
                            Hapus Foto 2
                        </Button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="button" class="cursor-pointer" :disabled="!canGenerate" @click="generateDocument">
                        {{ isGenerating ? 'Generating...' : 'Generate DOCX + Foto' }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <Card class="overflow-hidden py-0">
            <CardHeader class="border-b border-border bg-muted/30">
                <CardTitle class="text-lg sm:text-xl">Live Preview</CardTitle>
                <CardDescription>
                    Preview ini mengikuti layout yang akan dibangun oleh library `docx`.
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
                                <p v-for="(paragraph, index) in summaryPreviewParagraphs" :key="`docx-summary-${index}`" class="text-justify">
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
                                <p v-for="(paragraph, index) in officialsPreviewParagraphs" :key="`docx-official-${index}`">
                                    {{ paragraph }}
                                </p>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <h3 class="text-center text-sm font-bold tracking-wide text-foreground">DOKUMENTASI</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
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

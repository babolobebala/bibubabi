<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

type GoogleDriveFileItem = {
    id: string;
    name: string;
    mime_type: string | null;
    size: string | number | null;
    web_view_link: string | null;
    web_content_link: string | null;
    created_at: string | null;
    updated_at: string | null;
};

const props = defineProps<{
    canConnectGoogleDrive: boolean;
    hasGoogleRefreshToken: boolean;
    googleDriveFolderId: string | null;
}>();

const selectedFile = ref<File | null>(null);
const uploadName = ref('');
const shouldMakePublic = ref(true);
const isLoading = ref(false);
const isUploading = ref(false);
const fileItems = ref<GoogleDriveFileItem[]>([]);
const editingFileId = ref<string | null>(null);
const editingName = ref('');
const actionFileId = ref<string | null>(null);

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? null;

const connectionState = computed(() => {
    if (props.hasGoogleRefreshToken) {
        return {
            label: 'Connected',
            variant: 'default' as const,
            description: 'Google Drive sudah terhubung. Pegawai lain cukup login ke aplikasi ini.',
        };
    }

    return {
        label: 'Needs OAuth',
        variant: 'outline' as const,
        description: 'Super admin perlu menjalankan koneksi Google Drive satu kali untuk mengambil refresh token.',
    };
});

onMounted(() => {
    void loadFiles();
});

async function loadFiles(): Promise<void> {
    isLoading.value = true;

    try {
        const response = await fetch('/google-drive/files?page_size=50', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(await extractResponseError(response));
        }

        const payload = (await response.json()) as {
            items?: GoogleDriveFileItem[];
        };

        fileItems.value = Array.isArray(payload.items) ? payload.items : [];
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Gagal memuat daftar file Google Drive.');
    } finally {
        isLoading.value = false;
    }
}

function onSelectFile(event: Event): void {
    const target = event.target as HTMLInputElement;
    selectedFile.value = target.files?.[0] ?? null;

    if (selectedFile.value && uploadName.value.trim() === '') {
        uploadName.value = selectedFile.value.name;
    }
}

async function uploadFile(): Promise<void> {
    if (!selectedFile.value) {
        toast.error('Pilih file dulu sebelum upload.');
        return;
    }

    if (!csrfToken) {
        toast.error('CSRF token tidak ditemukan.');
        return;
    }

    isUploading.value = true;

    try {
        const formData = new FormData();
        formData.append('file', selectedFile.value, selectedFile.value.name);

        if (uploadName.value.trim() !== '') {
            formData.append('name', uploadName.value.trim());
        }

        formData.append('make_public', shouldMakePublic.value ? '1' : '0');

        const response = await fetch('/google-drive/files', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error(await extractResponseError(response));
        }

        await loadFiles();
        resetUploadForm();
        toast.success('File berhasil diunggah ke Google Drive.');
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Upload ke Google Drive gagal.');
    } finally {
        isUploading.value = false;
    }
}

function startRename(file: GoogleDriveFileItem): void {
    editingFileId.value = file.id;
    editingName.value = file.name;
}

function cancelRename(): void {
    editingFileId.value = null;
    editingName.value = '';
}

async function saveRename(fileId: string): Promise<void> {
    if (!csrfToken) {
        toast.error('CSRF token tidak ditemukan.');
        return;
    }

    if (editingName.value.trim() === '') {
        toast.error('Nama file tidak boleh kosong.');
        return;
    }

    actionFileId.value = fileId;

    try {
        const response = await fetch(`/google-drive/files/${fileId}`, {
            method: 'PATCH',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                name: editingName.value.trim(),
            }),
        });

        if (!response.ok) {
            throw new Error(await extractResponseError(response));
        }

        await loadFiles();
        cancelRename();
        toast.success('Nama file berhasil diperbarui.');
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Rename file gagal.');
    } finally {
        actionFileId.value = null;
    }
}

async function deleteFile(fileId: string): Promise<void> {
    if (!csrfToken) {
        toast.error('CSRF token tidak ditemukan.');
        return;
    }

    const confirmed = window.confirm('Hapus file ini dari Google Drive?');

    if (!confirmed) {
        return;
    }

    actionFileId.value = fileId;

    try {
        const response = await fetch(`/google-drive/files/${fileId}`, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(await extractResponseError(response));
        }

        await loadFiles();
        toast.success('File berhasil dihapus dari Google Drive.');
    } catch (error) {
        console.error(error);
        toast.error(error instanceof Error ? error.message : 'Delete file gagal.');
    } finally {
        actionFileId.value = null;
    }
}

async function copyPublicLink(file: GoogleDriveFileItem): Promise<void> {
    const link = file.web_view_link ?? file.web_content_link;

    if (!link) {
        toast.error('File ini belum punya link yang bisa disalin.');
        return;
    }

    try {
        await navigator.clipboard.writeText(link);
        toast.success('Link file berhasil disalin.');
    } catch (error) {
        console.error(error);
        toast.error('Gagal menyalin link file.');
    }
}

function resetUploadForm(): void {
    selectedFile.value = null;
    uploadName.value = '';
    shouldMakePublic.value = true;

    const input = document.getElementById('google-drive-upload-input');
    if (input instanceof HTMLInputElement) {
        input.value = '';
    }
}

async function extractResponseError(response: Response): Promise<string> {
    const contentType = response.headers.get('content-type') ?? '';

    if (contentType.includes('application/json')) {
        const payload = (await response.json()) as {
            message?: string;
            errors?: Record<string, string[]>;
        };

        const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;

        return firstError ?? payload.message ?? `Request gagal (${response.status}).`;
    }

    return `Request gagal (${response.status}).`;
}

function formatSize(size: string | number | null): string {
    const parsedSize = typeof size === 'string' ? Number.parseInt(size, 10) : size;

    if (!parsedSize || Number.isNaN(parsedSize)) {
        return '-';
    }

    if (parsedSize < 1024) {
        return `${parsedSize} B`;
    }

    if (parsedSize < 1024 * 1024) {
        return `${(parsedSize / 1024).toFixed(1)} KB`;
    }

    return `${(parsedSize / (1024 * 1024)).toFixed(1)} MB`;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
}
</script>

<template>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.12),_transparent_32%),linear-gradient(180deg,_var(--background)_0%,_color-mix(in_oklab,var(--background)_92%,white)_100%)]">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <Card class="overflow-hidden border-border/70 py-0 shadow-sm">
                <CardHeader class="border-b border-border bg-[linear-gradient(135deg,color-mix(in_oklab,var(--primary)_15%,white)_0%,var(--background)_68%)]">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl space-y-2">
                            <div class="flex items-center gap-3">
                                <Badge :variant="connectionState.variant">{{ connectionState.label }}</Badge>
                                <span class="text-xs font-medium tracking-[0.18em] text-muted-foreground uppercase">Google Drive Workspace</span>
                            </div>
                            <CardTitle class="text-2xl sm:text-3xl">Manajemen File Google Drive</CardTitle>
                            <CardDescription class="max-w-2xl text-sm leading-6">
                                Pegawai cukup login ke aplikasi ini. Semua operasi file dijalankan backend Laravel memakai akun Google Drive yang sudah
                                dihubungkan sekali oleh admin.
                            </CardDescription>
                        </div>

                        <div class="rounded-2xl border border-border/80 bg-background/80 p-4 backdrop-blur">
                            <p class="text-xs font-semibold tracking-[0.24em] text-muted-foreground uppercase">Target Folder</p>
                            <p class="mt-2 break-all text-sm font-medium text-foreground">
                                {{ googleDriveFolderId || 'Folder ID belum diisi di .env' }}
                            </p>
                        </div>
                    </div>
                </CardHeader>

                <CardContent class="grid gap-4 p-4 sm:p-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="rounded-2xl border border-border/80 bg-background/80 p-4">
                        <p class="text-sm font-semibold text-foreground">Status koneksi</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            {{ connectionState.description }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-dashed border-border/80 bg-accent/35 p-4">
                        <p class="text-sm font-semibold text-foreground">Aksi admin</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Jalankan koneksi Google sekali untuk mengambil `refresh token`, lalu semua device pegawai bisa memakai fitur ini tanpa login
                            Google lagi.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <Button v-if="canConnectGoogleDrive" as-child>
                                <a href="/auth/google/drive">Hubungkan Google Drive</a>
                            </Button>
                            <Button variant="outline" @click="loadFiles">Muat Ulang Daftar</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                <Card class="border-border/70 py-0 shadow-sm">
                    <CardHeader class="border-b border-border/70">
                        <CardTitle class="text-xl">Upload File Baru</CardTitle>
                        <CardDescription>Upload file ke folder `_BPS-KSB-SAKU` lalu opsional buat link baca publik.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 p-4 sm:p-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="google-drive-upload-input">File</label>
                            <input
                                id="google-drive-upload-input"
                                type="file"
                                class="block w-full cursor-pointer rounded-xl border border-input bg-background px-3 py-2.5 text-sm text-foreground file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium"
                                @change="onSelectFile"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ selectedFile ? selectedFile.name : 'Belum ada file dipilih.' }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground" for="google-drive-upload-name">Nama file di Drive</label>
                            <input
                                id="google-drive-upload-name"
                                v-model="uploadName"
                                type="text"
                                class="h-11 w-full rounded-xl border border-input bg-background px-3 text-sm text-foreground"
                                placeholder="Biarkan kosong untuk nama asli file"
                            />
                        </div>

                        <label class="flex items-start gap-3 rounded-xl border border-border/80 bg-muted/40 p-3">
                            <input
                                v-model="shouldMakePublic"
                                type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-input"
                            />
                            <span>
                                <span class="block text-sm font-medium text-foreground">Buat link publik</span>
                                <span class="block text-xs leading-5 text-muted-foreground">
                                    Jika aktif, file akan diberi permission `anyone can read`.
                                </span>
                            </span>
                        </label>

                        <div class="flex flex-wrap gap-3">
                            <Button :disabled="isUploading" @click="uploadFile">
                                {{ isUploading ? 'Mengunggah...' : 'Upload ke Google Drive' }}
                            </Button>
                            <Button variant="outline" :disabled="isUploading" @click="resetUploadForm">Reset</Button>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border/70 py-0 shadow-sm">
                    <CardHeader class="border-b border-border/70">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <CardTitle class="text-xl">Daftar File</CardTitle>
                                <CardDescription>Operasi rename dan delete dijalankan langsung dari backend Laravel.</CardDescription>
                            </div>
                            <Badge variant="secondary">{{ fileItems.length }} file</Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4 p-4 sm:p-6">
                        <div v-if="isLoading" class="grid gap-3">
                            <div v-for="index in 3" :key="index" class="h-24 animate-pulse rounded-2xl border border-border/70 bg-muted/50"></div>
                        </div>

                        <div v-else-if="fileItems.length === 0" class="rounded-2xl border border-dashed border-border/80 bg-background/70 p-6 text-center">
                            <p class="text-sm font-medium text-foreground">Belum ada file di folder Google Drive.</p>
                            <p class="mt-2 text-sm text-muted-foreground">Upload file pertama dari panel kiri untuk mulai memakai integrasi ini.</p>
                        </div>

                        <div v-else class="grid gap-3">
                            <div
                                v-for="file in fileItems"
                                :key="file.id"
                                class="rounded-2xl border border-border/80 bg-background/85 p-4 shadow-sm"
                            >
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div v-if="editingFileId === file.id" class="space-y-2">
                                            <input
                                                v-model="editingName"
                                                type="text"
                                                class="h-11 w-full rounded-xl border border-input bg-background px-3 text-sm text-foreground"
                                            />
                                            <div class="flex flex-wrap gap-2">
                                                <Button :disabled="actionFileId === file.id" @click="saveRename(file.id)">
                                                    {{ actionFileId === file.id ? 'Menyimpan...' : 'Simpan' }}
                                                </Button>
                                                <Button variant="outline" :disabled="actionFileId === file.id" @click="cancelRename">Batal</Button>
                                            </div>
                                        </div>

                                        <div v-else class="space-y-2">
                                            <p class="truncate text-base font-semibold text-foreground">{{ file.name }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                <Badge variant="outline">{{ file.mime_type || 'unknown' }}</Badge>
                                                <Badge variant="secondary">{{ formatSize(file.size) }}</Badge>
                                            </div>
                                        </div>

                                        <div class="grid gap-2 text-xs text-muted-foreground sm:grid-cols-2">
                                            <p><span class="font-medium text-foreground">Dibuat:</span> {{ formatDate(file.created_at) }}</p>
                                            <p><span class="font-medium text-foreground">Diubah:</span> {{ formatDate(file.updated_at) }}</p>
                                            <p class="sm:col-span-2 break-all">
                                                <span class="font-medium text-foreground">ID:</span> {{ file.id }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 lg:max-w-[16rem] lg:justify-end">
                                        <Button variant="outline" @click="startRename(file)">Rename</Button>
                                        <Button variant="outline" @click="copyPublicLink(file)">Copy Link</Button>
                                        <Button v-if="file.web_view_link" variant="outline" as-child>
                                            <a :href="file.web_view_link" target="_blank" rel="noreferrer">Buka</a>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            :disabled="actionFileId === file.id"
                                            @click="deleteFile(file.id)"
                                        >
                                            {{ actionFileId === file.id ? 'Menghapus...' : 'Hapus' }}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

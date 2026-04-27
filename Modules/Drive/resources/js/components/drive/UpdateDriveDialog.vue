<script setup lang="ts">
import { update } from '@/actions/Modules/Drive/Http/Controllers/DriveController';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackCombobox, TanStackInput } from '@/components/ui/form';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';
import type { DriveItem } from './drive-columns';

const props = defineProps<{
    open: boolean;
    drive: DriveItem | null;
    availableUsers: { value: string; label: string }[];
    availableRoles: { value: string; label: string }[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const updateDriveForm = useForm({
    defaultValues: {
        nama: '',
        link: '',
        jenis: 'personal' as 'personal' | 'tim',
        personal: '',
        tim: '',
        akses: 'edit' as 'edit' | 'view',
    },
    onSubmit: async ({ value, formApi }) => {
        if (!props.drive) return;
        return new Promise<void>((resolve) => {
            router.put(update.url(props.drive!.id), value, {
                onSuccess: () => {
                    emit('update:open', false);
                    formApi.reset();
                },
                onFinish: () => resolve(),
            });
        });
    },
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            updateDriveForm.reset();
            usePage().props.errors = {};
            if (props.drive) {
                updateDriveForm.setFieldValue('nama', props.drive.nama || '');
                updateDriveForm.setFieldValue('link', props.drive.link || '');
                updateDriveForm.setFieldValue('jenis', props.drive.jenis);
                updateDriveForm.setFieldValue('personal', props.drive.personal ? String(props.drive.personal) : '');
                updateDriveForm.setFieldValue('tim', props.drive.tim ? String(props.drive.tim) : '');
                updateDriveForm.setFieldValue('akses', props.drive.akses);
            }
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="sm:max-w-[425px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Ubah Drive</DialogTitle>
                <DialogDescription>Perbarui data link drive untuk sistem aplikasi.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="updateDriveForm.handleSubmit" class="space-y-4 py-4">
                <TanStackInput
                    :form="updateDriveForm"
                    name="nama"
                    label="Nama Drive *"
                    placeholder="Misal: Drive Utama Tim IT"
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Nama Drive wajib diisi' : undefined),
                    }"
                />

                <TanStackInput
                    :form="updateDriveForm"
                    name="link"
                    label="URL / Link Drive *"
                    placeholder="https://drive.google.com/..."
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Link wajib diisi' : undefined),
                    }"
                />

                <div class="grid grid-cols-2 gap-4">
                    <TanStackCombobox
                        :form="updateDriveForm"
                        name="jenis"
                        label="Jenis *"
                        :options="[
                            { value: 'personal', label: 'Personal' },
                            { value: 'tim', label: 'Tim' }
                        ]"
                    />

                    <TanStackCombobox
                        :form="updateDriveForm"
                        name="akses"
                        label="Akses *"
                        :options="[
                            { value: 'edit', label: 'Edit' },
                            { value: 'view', label: 'View' }
                        ]"
                    />
                </div>

                <updateDriveForm.Subscribe>
                    <template #default="state">
                        <TanStackCombobox
                            v-if="state.values.jenis === 'personal'"
                            :form="updateDriveForm"
                            name="personal"
                            label="Pilih Pemilik *"
                            :options="availableUsers"
                            placeholder="Cari nama atau NIP..."
                        />
                        <TanStackCombobox
                            v-else
                            :form="updateDriveForm"
                            name="tim"
                            label="Pilih Tim/Role *"
                            :options="availableRoles"
                            placeholder="Cari nama tim..."
                        />
                    </template>
                </updateDriveForm.Subscribe>

                <DialogFooter>
                    <updateDriveForm.Subscribe :selector="(state) => state.isSubmitting">
                        <template #default="isSubmitting">
                            <Button
                                type="button"
                                class="cursor-pointer"
                                variant="outline"
                                @click="emit('update:open', false)"
                                :disabled="isSubmitting"
                            >
                                Batal
                            </Button>
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting">Simpan Perubahan</Button>
                        </template>
                    </updateDriveForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

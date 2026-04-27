<script setup lang="ts">
import { store } from '@/actions/Modules/Drive/Http/Controllers/DriveController';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackCombobox, TanStackInput } from '@/components/ui/form';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
    availableUsers: { value: string; label: string }[];
    availableRoles: { value: string; label: string }[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const createDriveForm = useForm({
    defaultValues: {
        nama: '',
        link: '',
        jenis: 'personal' as 'personal' | 'tim',
        personal: '',
        tim: '',
        akses: 'edit' as 'edit' | 'view',
    },
    onSubmit: async ({ value, formApi }) => {
        return new Promise<void>((resolve) => {
            router.post(store.url(), value, {
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
            createDriveForm.reset();
            usePage().props.errors = {};
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="sm:max-w-[425px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Tambah Drive</DialogTitle>
                <DialogDescription>Buat link drive baru untuk sistem aplikasi.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="createDriveForm.handleSubmit" class="space-y-4 py-4">
                <TanStackInput
                    :form="createDriveForm"
                    name="nama"
                    label="Nama Drive *"
                    placeholder="Misal: Drive Utama Tim IT"
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Nama Drive wajib diisi' : undefined),
                    }"
                />

                <TanStackInput
                    :form="createDriveForm"
                    name="link"
                    label="URL / Link Drive *"
                    placeholder="https://drive.google.com/..."
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Link wajib diisi' : undefined),
                    }"
                />

                <div class="grid grid-cols-2 gap-4">
                    <TanStackCombobox
                        :form="createDriveForm"
                        name="jenis"
                        label="Jenis *"
                        :options="[
                            { value: 'personal', label: 'Personal' },
                            { value: 'tim', label: 'Tim' }
                        ]"
                    />

                    <TanStackCombobox
                        :form="createDriveForm"
                        name="akses"
                        label="Akses *"
                        :options="[
                            { value: 'edit', label: 'Edit' },
                            { value: 'view', label: 'View' }
                        ]"
                    />
                </div>

                <createDriveForm.Subscribe>
                    <template #default="state">
                        <TanStackCombobox
                            v-if="state.values.jenis === 'personal'"
                            :form="createDriveForm"
                            name="personal"
                            label="Pilih Pemilik *"
                            :options="availableUsers"
                            placeholder="Cari nama atau NIP..."
                        />
                        <TanStackCombobox
                            v-else
                            :form="createDriveForm"
                            name="tim"
                            label="Pilih Tim/Role *"
                            :options="availableRoles"
                            placeholder="Cari nama tim..."
                        />
                    </template>
                </createDriveForm.Subscribe>

                <DialogFooter>
                    <createDriveForm.Subscribe :selector="(state) => state.isSubmitting">
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
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting">Simpan Drive</Button>
                        </template>
                    </createDriveForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

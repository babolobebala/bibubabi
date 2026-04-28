<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackCombobox, TanStackInput, TanStackSelect } from '@/components/ui/form';
import { update } from '@/routes/drive';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { CheckCircle2, Edit, Eye, User, Users, XCircle } from 'lucide-vue-next';
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
        status: 'success' as 'success' | 'error',
        catatan: '',
    },
    onSubmit: async ({ value, formApi }) => {
        const driveId = props.drive?.id;
        if (!driveId) return;

        return new Promise<void>((resolve) => {
            router.put(update.url(driveId), value, {
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
        if (isOpen && props.drive) {
            updateDriveForm.reset({
                nama: props.drive.nama || '',
                link: props.drive.link || '',
                jenis: props.drive.jenis,
                personal: props.drive.personal ? String(props.drive.personal) : '',
                tim: props.drive.tim ? String(props.drive.tim) : '',
                akses: props.drive.akses,
                status: props.drive.status,
                catatan: props.drive.catatan || '',
            });
            usePage().props.errors = {};
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Update Drive</DialogTitle>
                <DialogDescription>Perbarui informasi link drive sistem.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="updateDriveForm.handleSubmit" class="space-y-4 py-4">
                <TanStackInput
                    :form="updateDriveForm"
                    name="nama"
                    label="Nama Drive*"
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Nama Drive wajib diisi' : undefined),
                    }"
                />

                <TanStackInput
                    :form="updateDriveForm"
                    name="link"
                    label="URL / Link Drive*"
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Link wajib diisi' : undefined),
                    }"
                />

                <div class="grid grid-cols-2 gap-4">
                    <TanStackSelect
                        :form="updateDriveForm"
                        name="jenis"
                        label="Jenis*"
                        :options="[
                            { value: 'personal', label: 'Personal', icon: User, iconClass: '!text-primary' },
                            { value: 'tim', label: 'Tim', icon: Users, iconClass: '!text-primary' },
                        ]"
                    />

                    <TanStackSelect
                        :form="updateDriveForm"
                        name="akses"
                        label="Akses*"
                        :options="[
                            { value: 'edit', label: 'Edit', icon: Edit, iconClass: '!text-primary' },
                            { value: 'view', label: 'View', icon: Eye, iconClass: '!text-slate-400' },
                        ]"
                    />
                </div>

                <updateDriveForm.Subscribe>
                    <template #default="state">
                        <TanStackCombobox
                            v-if="state.values.jenis === 'personal'"
                            :form="updateDriveForm"
                            name="personal"
                            label="Pilih Pemilik*"
                            :options="availableUsers"
                            placeholder="Cari nama..."
                        />
                        <TanStackCombobox
                            v-else
                            :form="updateDriveForm"
                            name="tim"
                            label="Pilih Tim/Role*"
                            :options="availableRoles"
                            placeholder="Cari nama tim..."
                        />
                    </template>
                </updateDriveForm.Subscribe>

                <TanStackSelect
                    :form="updateDriveForm"
                    name="status"
                    label="Status*"
                    :options="[
                        { value: 'success', label: 'Success', icon: CheckCircle2, iconClass: '!text-emerald-500' },
                        { value: 'error', label: 'Error', icon: XCircle, iconClass: '!text-red-500' },
                    ]"
                />

                <TanStackInput :form="updateDriveForm" name="catatan" label="Catatan" />

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

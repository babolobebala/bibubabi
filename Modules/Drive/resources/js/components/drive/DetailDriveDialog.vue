<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackCombobox, TanStackInput, TanStackSelect } from '@/components/ui/form';
import { useForm } from '@tanstack/vue-form';
import { CheckCircle2, Edit, Eye, User, Users, XCircle } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import type { DriveItem } from './drive-columns';

const props = defineProps<{
    open: boolean;
    drive: DriveItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const detailDriveForm = useForm({
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
    onSubmit: async () => {
        return;
    },
});

const personalOptions = computed(() => {
    if (!props.drive?.personal_user) {
        return [];
    }

    return [
        {
            value: String(props.drive.personal_user.id),
            label: props.drive.personal_user.nama,
        },
    ];
});

const timOptions = computed(() => {
    if (!props.drive?.tim_role) {
        return [];
    }

    return [
        {
            value: String(props.drive.tim_role.id),
            label: props.drive.tim_role.name,
        },
    ];
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen && props.drive) {
            detailDriveForm.reset({
                nama: props.drive.nama || '',
                link: props.drive.link || '',
                jenis: props.drive.jenis,
                personal: props.drive.personal ? String(props.drive.personal) : '',
                tim: props.drive.tim ? String(props.drive.tim) : '',
                akses: props.drive.akses,
                status: props.drive.status,
                catatan: props.drive.catatan || '',
            });
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Detail Drive</DialogTitle>
                <DialogDescription>Informasi link drive dalam mode baca saja.</DialogDescription>
            </DialogHeader>

            <form class="space-y-4 py-4">
                <div class="read-only-fields space-y-4 pointer-events-none">
                    <TanStackInput :form="detailDriveForm" name="nama" label="Nama Drive*" />

                    <TanStackInput :form="detailDriveForm" name="link" label="URL / Link Drive*" />

                    <div class="grid grid-cols-2 gap-4">
                        <TanStackSelect
                            :form="detailDriveForm"
                            name="jenis"
                            label="Jenis*"
                            :options="[
                                { value: 'personal', label: 'Personal', icon: User, iconClass: '!text-primary' },
                                { value: 'tim', label: 'Tim', icon: Users, iconClass: '!text-primary' },
                            ]"
                        />

                        <TanStackSelect
                            :form="detailDriveForm"
                            name="akses"
                            label="Akses*"
                            :options="[
                                { value: 'edit', label: 'Edit', icon: Edit, iconClass: '!text-primary' },
                                { value: 'view', label: 'View', icon: Eye, iconClass: '!text-slate-400' },
                            ]"
                        />
                    </div>

                    <detailDriveForm.Subscribe>
                        <template #default="state">
                            <TanStackCombobox
                                v-if="state.values.jenis === 'personal'"
                                :form="detailDriveForm"
                                name="personal"
                                label="Pilih Pemilik*"
                                :options="personalOptions"
                                placeholder="Cari nama..."
                            />
                            <TanStackCombobox
                                v-else
                                :form="detailDriveForm"
                                name="tim"
                                label="Pilih Tim/Role*"
                                :options="timOptions"
                                placeholder="Cari nama tim..."
                            />
                        </template>
                    </detailDriveForm.Subscribe>

                    <TanStackSelect
                        :form="detailDriveForm"
                        name="status"
                        label="Status*"
                        :options="[
                            { value: 'success', label: 'Success', icon: CheckCircle2, iconClass: '!text-emerald-500' },
                            { value: 'error', label: 'Error', icon: XCircle, iconClass: '!text-red-500' },
                        ]"
                    />

                    <TanStackInput :form="detailDriveForm" name="catatan" label="Catatan" />
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.read-only-fields :deep(input),
.read-only-fields :deep(button[role='combobox']) {
    background-color: hsl(var(--muted));
    color: hsl(var(--muted-foreground));
    border-color: hsl(var(--border));
    cursor: not-allowed;
}
</style>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackInput } from '@/components/ui/form';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const createRoleForm = useForm({
    defaultValues: {
        name: '',
        description: '',
    },
    onSubmit: async ({ value, formApi }) => {
        return new Promise<void>((resolve) => {
            router.post(route('admin.roles.store'), value, {
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
            createRoleForm.reset();
            usePage().props.errors = {};
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="sm:max-w-[425px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Tambah Role</DialogTitle>
                <DialogDescription>Buat role baru untuk sistem aplikasi.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="createRoleForm.handleSubmit" class="space-y-4 py-4">
                <TanStackInput
                    :form="createRoleForm"
                    name="name"
                    label="Nama Role *"
                    placeholder="Misal: admin, operator, dll..."
                    :validators="{
                        onChange: ({ value }: any) => (!value ? 'Nama Role wajib diisi' : undefined),
                    }"
                />

                <TanStackInput
                    :form="createRoleForm"
                    name="description"
                    label="Deskripsi"
                    placeholder="Jelaskan kegunaan role ini..."
                />

                <DialogFooter>
                    <createRoleForm.Subscribe :selector="(state) => state.isSubmitting">
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
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting">Simpan Role</Button>
                        </template>
                    </createRoleForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

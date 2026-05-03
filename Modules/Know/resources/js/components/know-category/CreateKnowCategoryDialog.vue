<script setup lang="ts">
import { storeCategory } from '@/actions/Modules/Know/Http/Controllers/KnowController';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackInput } from '@/components/ui/form';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const createCategoryForm = useForm({
    defaultValues: {
        nama: '',
    },
    onSubmit: async ({ value, formApi }) => {
        return new Promise<void>((resolve) => {
            router.post(
                storeCategory.url(),
                {
                    nama: value.nama.trim(),
                },
                {
                    onSuccess: () => {
                        emit('update:open', false);
                        formApi.reset();
                    },
                    onFinish: () => resolve(),
                },
            );
        });
    },
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            createCategoryForm.reset();
            usePage().props.errors = {};
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Tambah Kategori</DialogTitle>
                <DialogDescription>Tambahkan kategori knowledge baru.</DialogDescription>
            </DialogHeader>
            <form class="space-y-4 py-4" @submit.prevent="createCategoryForm.handleSubmit">
                <TanStackInput
                    :form="createCategoryForm"
                    name="nama"
                    label="Nama Kategori*"
                    placeholder="Contoh: Sosialisasi"
                    :validators="{
                        onChange: ({ value }: { value: string }) => (!value?.trim() ? 'Nama kategori wajib diisi' : undefined),
                    }"
                />

                <DialogFooter>
                    <createCategoryForm.Subscribe :selector="(state) => state.isSubmitting">
                        <template #default="isSubmitting">
                            <Button
                                type="button"
                                class="cursor-pointer"
                                variant="outline"
                                :disabled="isSubmitting"
                                @click="emit('update:open', false)"
                            >
                                Batal
                            </Button>
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting">
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Kategori' }}
                            </Button>
                        </template>
                    </createCategoryForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

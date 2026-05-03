<script setup lang="ts">
import { updateCategory } from '@/actions/Modules/Know/Http/Controllers/KnowController';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackInput } from '@/components/ui/form';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';
import type { KnowCategoryItem } from './category-columns';

const props = defineProps<{
    open: boolean;
    category: KnowCategoryItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const updateCategoryForm = useForm({
    defaultValues: {
        nama: '',
    },
    onSubmit: async ({ value, formApi }) => {
        if (!props.category) {
            return;
        }

        return new Promise<void>((resolve) => {
            router.put(
                updateCategory.url(props.category!.id),
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
            updateCategoryForm.reset();
            usePage().props.errors = {};

            if (props.category) {
                updateCategoryForm.setFieldValue('nama', props.category.nama ?? '');
            }
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Ubah Kategori</DialogTitle>
                <DialogDescription>Perbarui nama kategori knowledge.</DialogDescription>
            </DialogHeader>

            <form class="space-y-4 py-4" @submit.prevent="updateCategoryForm.handleSubmit">
                <TanStackInput
                    :form="updateCategoryForm"
                    name="nama"
                    label="Nama Kategori*"
                    placeholder="Contoh: Sosialisasi"
                    :validators="{
                        onChange: ({ value }: { value: string }) => (!value?.trim() ? 'Nama kategori wajib diisi' : undefined),
                    }"
                />

                <DialogFooter>
                    <updateCategoryForm.Subscribe :selector="(state) => state.isSubmitting">
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
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </Button>
                        </template>
                    </updateCategoryForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

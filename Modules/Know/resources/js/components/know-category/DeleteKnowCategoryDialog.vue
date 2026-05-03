<script setup lang="ts">
import { destroyCategory } from '@/actions/Modules/Know/Http/Controllers/KnowController';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { KnowCategoryItem } from './category-columns';

const props = defineProps<{
    open: boolean;
    category: KnowCategoryItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const isSubmitting = ref(false);

const handleDelete = () => {
    if (!props.category) {
        return;
    }

    isSubmitting.value = true;
    router.delete(destroyCategory.url(props.category.id), {
        onSuccess: () => {
            emit('update:open', false);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            usePage().props.errors = {};
            isSubmitting.value = false;
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(value) => !isSubmitting && emit('update:open', value)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle class="text-destructive">Hapus Kategori?</DialogTitle>
                <DialogDescription v-if="category">
                    Apakah Anda yakin ingin menghapus kategori
                    <span class="font-semibold text-foreground">{{ category.nama }}</span>?
                    <br />
                    Tindakan ini tidak dapat dibatalkan.

                    <div
                        v-if="usePage().props.errors.error"
                        class="mt-4 rounded-md border border-destructive/20 bg-destructive/10 p-3 text-sm text-destructive"
                    >
                        {{ usePage().props.errors.error }}
                    </div>
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="mt-4">
                <Button
                    type="button"
                    variant="outline"
                    class="cursor-pointer"
                    :disabled="isSubmitting"
                    @click="emit('update:open', false)"
                >
                    Batal
                </Button>
                <Button type="button" variant="destructive" class="cursor-pointer" :disabled="isSubmitting" @click="handleDelete">
                    {{ isSubmitting ? 'Menghapus...' : 'Ya, Hapus Kategori' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

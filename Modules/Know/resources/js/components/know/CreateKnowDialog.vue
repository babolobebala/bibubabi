<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackDatePicker, TanStackInput, TanStackTextarea } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/know';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const createKnowForm = useForm({
    defaultValues: {
        nama: '',
        deskripsi: '',
        pic: '',
        tanggal_pelaksanaan: '',
        linksText: '',
        categoriesText: '',
    },
    onSubmit: async ({ value, formApi }) => {
        return new Promise<void>((resolve) => {
            router.post(
                store.url(),
                {
                    nama: value.nama,
                    deskripsi: value.deskripsi || null,
                    pic: value.pic || null,
                    tanggal_pelaksanaan: value.tanggal_pelaksanaan || null,
                    link: value.linksText
                        .split('\n')
                        .map((item) => item.trim())
                        .filter(Boolean),
                    kategori: value.categoriesText
                        .split(',')
                        .map((item) => item.trim())
                        .filter(Boolean),
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
            createKnowForm.reset();
            usePage().props.errors = {};
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[640px]">
            <DialogHeader>
                <DialogTitle>Tambah Knowledge</DialogTitle>
                <DialogDescription>Buat knowledge baru untuk daftar knowledge aplikasi.</DialogDescription>
            </DialogHeader>

            <form class="space-y-4 py-4" @submit.prevent="createKnowForm.handleSubmit">
                <TanStackInput
                    :form="createKnowForm"
                    name="nama"
                    label="Nama Knowledge*"
                    placeholder="Contoh: Pelatihan Pengolahan Data"
                    :validators="{
                        onChange: ({ value }: { value: string }) => (!value ? 'Nama Knowledge wajib diisi' : undefined),
                    }"
                />

                <TanStackTextarea :form="createKnowForm" name="deskripsi" label="Deskripsi" placeholder="Deskripsi singkat knowledge..." />

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <TanStackInput :form="createKnowForm" name="pic" label="PIC" placeholder="Nama PIC" />
                    <TanStackDatePicker :form="createKnowForm" name="tanggal_pelaksanaan" label="Tanggal Pelaksanaan" />
                </div>

                <TanStackTextarea
                    :form="createKnowForm"
                    name="linksText"
                    label="Link (satu baris satu link)"
                    placeholder="https://example.com/file-1&#10;https://example.com/file-2"
                    error-key="link"
                />

                <createKnowForm.Field name="categoriesText">
                    <template #default="{ field }">
                        <div class="space-y-2">
                            <Label for="categoriesText">Kategori (pisahkan dengan koma)</Label>
                            <Input
                                id="categoriesText"
                                type="text"
                                placeholder="Pelatihan, Dashboard, SOP"
                                :model-value="field.state.value"
                                @blur="field.handleBlur"
                                @input="(e: Event) => field.handleChange((e.target as HTMLInputElement).value)"
                            />
                            <p v-if="usePage().props.errors?.kategori" class="text-xs text-destructive">{{ usePage().props.errors.kategori }}</p>
                        </div>
                    </template>
                </createKnowForm.Field>

                <DialogFooter>
                    <createKnowForm.Subscribe :selector="(state) => state.isSubmitting">
                        <template #default="isSubmitting">
                            <Button type="button" class="cursor-pointer" variant="outline" @click="emit('update:open', false)" :disabled="isSubmitting">
                                Batal
                            </Button>
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting">
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Knowledge' }}
                            </Button>
                        </template>
                    </createKnowForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

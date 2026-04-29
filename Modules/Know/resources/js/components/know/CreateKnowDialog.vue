<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackDatePicker, TanStackInput, TanStackTextarea } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/know';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { Plus, Trash2 } from 'lucide-vue-next';
import { watch } from 'vue';

interface LinkEntry {
    nama: string;
    link: string;
}

interface LinkFormField {
    state: {
        value: LinkEntry[] | undefined;
    };
    handleChange: (value: LinkEntry[]) => void;
}

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const page = usePage();

const emptyLinkEntry = (): LinkEntry => ({
    nama: '',
    link: '',
});

const updateLinkEntry = (field: LinkFormField, index: number, key: keyof LinkEntry, newValue: string): void => {
    const currentEntries = Array.isArray(field.state.value) ? [...field.state.value] : [];
    const currentEntry = currentEntries[index] ?? emptyLinkEntry();

    currentEntries[index] = {
        ...currentEntry,
        [key]: newValue,
    };

    field.handleChange(currentEntries);
};

const addLinkEntry = (field: LinkFormField): void => {
    const currentEntries = Array.isArray(field.state.value) ? [...field.state.value] : [];
    field.handleChange([...currentEntries, emptyLinkEntry()]);
};

const removeLinkEntry = (field: LinkFormField, index: number): void => {
    const currentEntries = Array.isArray(field.state.value) ? [...field.state.value] : [];
    const filteredEntries = currentEntries.filter((_, currentIndex) => currentIndex !== index);

    field.handleChange(filteredEntries);
};

const getLinkError = (index: number, key: keyof LinkEntry): string | undefined => {
    const errors = page.props.errors as Record<string, string | undefined>;

    return errors[`link.${index}.${key}`];
};

const createKnowForm = useForm({
    defaultValues: {
        nama: '',
        deskripsi: '',
        pic: '',
        tanggal_pelaksanaan: '',
        links: [emptyLinkEntry()],
        categoriesText: '',
    },
    onSubmit: async ({ value, formApi }) => {
        const links = value.links
            .map((item) => ({
                nama: item.nama.trim(),
                link: item.link.trim(),
            }))
            .filter((item) => item.nama !== '' || item.link !== '');

        return new Promise<void>((resolve) => {
            router.post(
                store.url(),
                {
                    nama: value.nama,
                    deskripsi: value.deskripsi || null,
                    pic: value.pic || null,
                    tanggal_pelaksanaan: value.tanggal_pelaksanaan || null,
                    link: links,
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
            page.props.errors = {};
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

                <createKnowForm.Field name="links">
                    <template #default="{ field }">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Label>Link</Label>
                                <Button type="button" size="sm" variant="outline" class="cursor-pointer gap-1.5" @click="addLinkEntry(field as LinkFormField)">
                                    <Plus class="h-4 w-4" />
                                    Tambah Link
                                </Button>
                            </div>

                            <div
                                v-for="(linkItem, index) in Array.isArray((field as LinkFormField).state.value) ? (field as LinkFormField).state.value : []"
                                :key="index"
                                class="space-y-2 rounded-md border p-3"
                            >
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_1fr_auto]">
                                    <div class="space-y-2">
                                        <Label :for="`link-nama-${index}`">Nama Link</Label>
                                        <Input
                                            :id="`link-nama-${index}`"
                                            type="text"
                                            placeholder="Contoh: Link Tree"
                                            :model-value="linkItem.nama"
                                            @input="(e: Event) => updateLinkEntry(field as LinkFormField, index, 'nama', (e.target as HTMLInputElement).value)"
                                        />
                                        <p v-if="getLinkError(index, 'nama')" class="text-xs text-destructive">{{ getLinkError(index, 'nama') }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label :for="`link-url-${index}`">Link</Label>
                                        <Input
                                            :id="`link-url-${index}`"
                                            type="text"
                                            placeholder="https://example.com"
                                            :model-value="linkItem.link"
                                            @input="(e: Event) => updateLinkEntry(field as LinkFormField, index, 'link', (e.target as HTMLInputElement).value)"
                                        />
                                        <p v-if="getLinkError(index, 'link')" class="text-xs text-destructive">{{ getLinkError(index, 'link') }}</p>
                                    </div>

                                    <div class="flex items-end">
                                        <Button
                                            type="button"
                                            size="icon"
                                            variant="ghost"
                                            class="cursor-pointer text-destructive hover:text-destructive"
                                            @click="removeLinkEntry(field as LinkFormField, index)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <p
                                v-if="(page.props.errors as Record<string, string | undefined>)?.link"
                                class="text-xs text-destructive"
                            >
                                {{ (page.props.errors as Record<string, string | undefined>).link }}
                            </p>
                        </div>
                    </template>
                </createKnowForm.Field>

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
                            <p v-if="page.props.errors?.kategori" class="text-xs text-destructive">{{ page.props.errors.kategori }}</p>
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

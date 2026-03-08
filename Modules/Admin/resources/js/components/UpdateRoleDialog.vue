<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackCombobox } from '@/components/ui/form';
import { router } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
    userId: number | null;
    userName: string;
    currentUserRoles: string[];
    availableRoles: string[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const roleForm = useForm({
    defaultValues: {
        roles: [] as string[],
    },
    onSubmit: async ({ value, formApi }) => {
        if (!props.userId) return;
        return new Promise<void>((resolve) => {
            router.put(`/app/admin/users/${props.userId}/role`, value, {
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
            roleForm.reset();
            roleForm.setFieldValue('roles', props.currentUserRoles.length ? [...props.currentUserRoles] : []);
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Ubah Role</DialogTitle>
                <DialogDescription> Konfigurasi role untuk {{ userName }}. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="roleForm.handleSubmit" class="space-y-4 py-4">
                <TanStackCombobox
                    :form="roleForm"
                    name="roles"
                    label="Pilih Role"
                    :options="availableRoles"
                    multiple
                />

                <DialogFooter>
                    <roleForm.Subscribe :selector="(state) => state.isSubmitting">
                        <template #default="isSubmitting">
                            <Button type="button" variant="outline" @click="emit('update:open', false)" :disabled="isSubmitting"> Batal </Button>
                            <Button type="submit" :disabled="isSubmitting"> Simpan Perubahan </Button>
                        </template>
                    </roleForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

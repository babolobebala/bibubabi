<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { TanStackInput } from '@/components/ui/form';
import { router } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const passwordForm = useForm({
    defaultValues: {
        password: '',
        password_confirmation: '',
    },
    onSubmit: async ({ value, formApi }) => {
        return new Promise<void>((resolve) => {
            router.put(`/app/profile/password`, value, {
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
            passwordForm.reset();
            router.clearErrors();
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="z-100">
            <DialogHeader>
                <DialogTitle>Ubah Password Saya</DialogTitle>
                <DialogDescription> Setel ulang password akun Anda. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="passwordForm.handleSubmit" class="space-y-4 py-4">
                <TanStackInput :form="passwordForm" name="password" type="password" label="Password Baru" placeholder="Masukkan password baru..." />

                <TanStackInput
                    :form="passwordForm"
                    name="password_confirmation"
                    type="password"
                    label="Konfirmasi Password Baru"
                    placeholder="Ketik ulang password..."
                    :validators="{
                        onChangeListenTo: ['password'],
                        onChange: ({ value, fieldApi }: any) => {
                            if (value !== fieldApi.form.getFieldValue('password')) {
                                return 'Konfirmasi password tidak cocok.';
                            }
                            return undefined;
                        },
                    }"
                />

                <DialogFooter>
                    <passwordForm.Subscribe :selector="(state) => state.isSubmitting">
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
                            <Button type="submit" class="cursor-pointer" :disabled="isSubmitting"> Simpan Password </Button>
                        </template>
                    </passwordForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

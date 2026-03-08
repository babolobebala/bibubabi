<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { watch } from 'vue';

const props = defineProps<{
    open: boolean;
    userId: number | null;
    userName: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const page = usePage();

const passwordForm = useForm({
    defaultValues: {
        password: '',
        password_confirmation: '',
    },
    onSubmit: async ({ value, formApi }) => {
        if (!props.userId) return;
        return new Promise<void>((resolve) => {
            router.put(`/app/admin/users/${props.userId}/password`, value, {
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
        }
    },
);
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Ubah Password</DialogTitle>
                <DialogDescription> Setel ulang password untuk {{ userName }}. </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="passwordForm.handleSubmit" class="space-y-4 py-4">
                <passwordForm.Field
                    name="password"
                    :validators="{
                        onChange: ({ value }) => (value.length < 8 ? 'Password minimal 8 karakter.' : undefined),
                    }"
                >
                    <template #default="{ field }">
                        <div class="space-y-2">
                            <Label for="password">Password Baru</Label>
                            <Input
                                id="password"
                                type="password"
                                v-model="field.state.value"
                                @blur="field.handleBlur"
                                @input="(e: Event) => field.handleChange((e.target as HTMLInputElement).value)"
                                placeholder="Minimal 8 karakter..."
                            />
                            <p v-if="field.state.meta.errors.length" class="text-xs text-destructive">
                                {{ field.state.meta.errors.join(', ') }}
                            </p>
                            <p v-else-if="page.props.errors?.password" class="text-xs text-destructive">
                                {{ page.props.errors.password }}
                            </p>
                        </div>
                    </template>
                </passwordForm.Field>

                <passwordForm.Field
                    name="password_confirmation"
                    :validators="{
                        onChangeListenTo: ['password'],
                        onChange: ({ value, fieldApi }) => {
                            if (value !== fieldApi.form.getFieldValue('password')) {
                                return 'Konfirmasi password tidak cocok.';
                            }
                            return undefined;
                        },
                    }"
                >
                    <template #default="{ field }">
                        <div class="space-y-2">
                            <Label for="password_confirmation">Konfirmasi Password Baru</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                v-model="field.state.value"
                                @blur="field.handleBlur"
                                @input="(e: Event) => field.handleChange((e.target as HTMLInputElement).value)"
                                placeholder="Ketik ulang password..."
                            />
                            <p v-if="field.state.meta.errors.length" class="text-xs text-destructive">
                                {{ field.state.meta.errors.join(', ') }}
                            </p>
                        </div>
                    </template>
                </passwordForm.Field>

                <DialogFooter>
                    <passwordForm.Subscribe :selector="(state) => state.isSubmitting">
                        <template #default="isSubmitting">
                            <Button type="button" variant="outline" @click="emit('update:open', false)" :disabled="isSubmitting"> Batal </Button>
                            <Button type="submit" :disabled="isSubmitting"> Simpan Password </Button>
                        </template>
                    </passwordForm.Subscribe>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

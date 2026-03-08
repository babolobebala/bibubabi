<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { router, usePage } from '@inertiajs/vue3';
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

const page = usePage();

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
                <roleForm.Field name="roles">
                    <template #default="{ field }">
                        <div class="space-y-3">
                            <Label>Pilih Role</Label>
                            <div class="mt-2 grid grid-cols-2 gap-3 rounded-md border bg-muted/10 p-4">
                                <div v-for="role in availableRoles" :key="role" class="flex items-center space-x-2">
                                    <Checkbox
                                        :id="`role-${role}`"
                                        :checked="field.state.value.includes(role)"
                                        @update:checked="
                                            (checked: boolean) => {
                                                const val = [...field.state.value];
                                                if (checked) {
                                                    val.push(role);
                                                } else {
                                                    const ix = val.indexOf(role);
                                                    if (ix > -1) val.splice(ix, 1);
                                                }
                                                field.handleChange(val);
                                            }
                                        "
                                    />
                                    <Label :for="`role-${role}`" class="cursor-pointer text-sm font-normal">{{ role }}</Label>
                                </div>
                            </div>
                            <p v-if="page.props.errors?.roles" class="text-xs text-destructive">
                                {{ page.props.errors.roles }}
                            </p>
                        </div>
                    </template>
                </roleForm.Field>

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

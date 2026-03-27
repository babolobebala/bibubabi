<script setup lang="ts">
import { updateQuickMenu } from '@/actions/Modules/Core/Http/Controllers/CoreController';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { router, usePage } from '@inertiajs/vue3';
import { useForm } from '@tanstack/vue-form';
import { Loader2, Settings } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import type { CoreModuleEntry } from '../lib/core-menu';

defineProps<{
    modules: CoreModuleEntry[];
}>();

const isOpen = ref(false);

const currentUserMenus = (usePage().props as any).auth?.user?.quick_menu_keys ?? [];

const updateQuickMenuForm = useForm({
    defaultValues: {
        quick_menu_keys: [...currentUserMenus] as string[],
    },
    onSubmit: async ({ value }) => {
        router.put(updateQuickMenu.url(), value, {
            preserveScroll: true,
            onSuccess: () => {
                isOpen.value = false;
            },
        });
    },
});

watch(isOpen, (newVal) => {
    if (newVal) {
        updateQuickMenuForm.reset();
        const freshKeys = (usePage().props as any).auth?.user?.quick_menu_keys ?? [];
        updateQuickMenuForm.setFieldValue('quick_menu_keys', [...freshKeys]);
    } else {
        usePage().props.errors = {};
    }
});

function toggleMenuSelection(key: string, isChecked: boolean) {
    const currentKeys = [...updateQuickMenuForm.getFieldValue('quick_menu_keys')];
    if (isChecked) {
        if (!currentKeys.includes(key)) currentKeys.push(key);
    } else {
        const index = currentKeys.indexOf(key);
        if (index > -1) currentKeys.splice(index, 1);
    }
    updateQuickMenuForm.setFieldValue('quick_menu_keys', currentKeys);
}
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" size="sm" class="gap-2">
                <Settings class="h-4 w-4" />
                <span>Kustomisasi Menu</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-[450px]">
            <DialogHeader>
                <DialogTitle>Kustomisasi Menu Cepat</DialogTitle>
                <DialogDescription> Pilih fitur dan layanan yang ingin Anda jadikan pintasan di halaman ini. </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="updateQuickMenuForm.handleSubmit" class="space-y-4">
                <updateQuickMenuForm.Field name="quick_menu_keys">
                    <template #default="{ field }">
                        <div class="rounded-md border p-1 border-muted overflow-hidden">
                            <Accordion type="multiple" collapsible class="w-full">
                                <AccordionItem 
                                    v-for="mod in modules" 
                                    :key="mod.menu.key" 
                                    :value="mod.menu.key"
                                    class="border-b-0"
                                >
                                    <AccordionTrigger class="px-4 py-3 hover:bg-muted/50 rounded hover:no-underline">
                                        <div class="flex items-center gap-3 text-left">
                                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg border bg-background shadow-sm">
                                                 <img v-if="mod.menu.iconImage" :src="`/` + mod.menu.iconImage" class="h-4 w-4" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold">{{ mod.menu.title }}</p>
                                            </div>
                                        </div>
                                    </AccordionTrigger>
                                    <AccordionContent class="px-4 pb-4 pt-1 bg-muted/20 border-t">
                                         <div v-if="mod.features && mod.features.length > 0" class="space-y-3 pt-3 pl-2 border-l-2 ml-4">
                                            <div v-for="menu in mod.features" :key="menu.key" class="flex items-center space-x-3 rounded p-2 transition hover:bg-muted/50">
                                                <Checkbox
                                                    :id="`chk-${menu.key}`"
                                                    :checked="field.state.value.includes(menu.key)"
                                                    @update:checked="toggleMenuSelection(menu.key, !!$event)"
                                                />
                                                <img v-if="menu.iconImage" :src="`/` + menu.iconImage" class="h-4 w-4 shrink-0" />
                                                <div class="flex-1 leading-none">
                                                    <label
                                                        :for="`chk-${menu.key}`"
                                                        class="text-sm font-medium leading-none cursor-pointer peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                    >
                                                        {{ menu.title }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="space-y-3 pt-3 pl-2 border-l-2 ml-4">
                                            <div class="flex items-start space-x-3 rounded p-2 transition hover:bg-muted/50">
                                                <Checkbox
                                                    :id="`chk-${mod.menu.key}`"
                                                    :checked="field.state.value.includes(mod.menu.key)"
                                                    @update:checked="toggleMenuSelection(mod.menu.key, !!$event)"
                                                    class="mt-1"
                                                />
                                                <div class="grid gap-1.5 leading-none">
                                                    <label
                                                        :for="`chk-${mod.menu.key}`"
                                                        class="text-sm font-medium leading-none cursor-pointer peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                    >
                                                        {{ mod.menu.title }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                            </Accordion>
                        </div>

                        <p v-if="usePage().props.errors?.quick_menu_keys" class="mt-1 text-xs text-destructive">
                            {{ usePage().props.errors.quick_menu_keys }}
                        </p>
                    </template>
                </updateQuickMenuForm.Field>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="isOpen = false"> Batal </Button>
                    <Button type="submit" :disabled="updateQuickMenuForm.state.isSubmitting">
                        <Loader2 v-if="updateQuickMenuForm.state.isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                        Simpan Perubahan
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

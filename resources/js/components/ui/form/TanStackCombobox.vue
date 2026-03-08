<script setup lang="ts">
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/vue3';
import { Check, ChevronDown, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    form: any;
    name: string;
    label: string;
    placeholder?: string;
    options: string[];
    validators?: any;
    multiple?: boolean;
}>();
</script>

<template>
    <props.form.Field :name="name" :validators="validators">
        <template #default="{ field }">
            <div class="space-y-2">
                <Label :for="name">
                    {{ label }}
                </Label>

                <Combobox
                    :model-value="field.state.value"
                    @update:model-value="(val) => field.handleChange(multiple ? ((val || []) as string[]) : (val as string))"
                    :multiple="multiple"
                >
                    <ComboboxAnchor class="relative w-full">
                        <ComboboxInput
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            :placeholder="placeholder || 'Pilih...'"
                        />
                        <ComboboxTrigger class="absolute inset-y-0 end-0 flex items-center justify-center px-3" tabindex="-1">
                            <ChevronDown class="h-4 w-4 opacity-50" />
                        </ComboboxTrigger>
                    </ComboboxAnchor>

                    <ComboboxList class="z-50 mt-1 max-h-60 w-(--reka-popper-anchor-width) overflow-auto rounded-md border bg-popover p-1 text-popover-foreground shadow-md outline-none data-[state=closed]:animate-out data-[state=open]:animate-in data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2">
                        <ComboboxEmpty class="py-6 text-center text-sm">Opsi tidak ditemukan.</ComboboxEmpty>
                        <ComboboxGroup>
                            <ComboboxItem
                                v-for="option in options"
                                :key="option"
                                :value="option"
                                class="relative flex w-full cursor-default items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none select-none data-[disabled]:opacity-50 data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground data-[disabled]:pointer-events-none"
                            >
                                <span>{{ option }}</span>
                                <ComboboxItemIndicator class="absolute right-2 flex h-3.5 w-3.5 items-center justify-center">
                                    <Check class="h-4 w-4" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </ComboboxGroup>
                    </ComboboxList>
                </Combobox>

                <!-- Selected Badges untuk Combobox Multiple -->
                <div v-if="multiple && field.state.value && field.state.value.length > 0" class="mt-3 flex flex-wrap gap-2">
                    <div
                        v-for="val in field.state.value"
                        :key="val"
                        class="flex items-center gap-1.5 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm"
                    >
                        <span class="font-medium">{{ val }}</span>
                        <button
                            type="button"
                            class="text-destructive hover:text-destructive/80 focus:outline-none"
                            @click="field.handleChange(field.state.value.filter((r: string) => r !== val))"
                        >
                            <XCircle class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- FORMAT ERROR -->
                <p v-if="field.state.meta.errors.length" class="mt-1 text-xs text-destructive">
                    {{ field.state.meta.errors.join(', ') }}
                </p>
                <p v-else-if="usePage().props.errors?.[name]" class="mt-1 text-xs text-destructive">
                    {{ usePage().props.errors[name] }}
                </p>
            </div>
        </template>
    </props.form.Field>
</template>

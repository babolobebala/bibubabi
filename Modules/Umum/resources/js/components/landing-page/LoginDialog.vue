<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { ref } from 'vue';
import SharedLoginContent from '../../../../../Shared/resources/js/components/public/LoginContent.vue';

const isOpen = ref(false);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button class="h-10 cursor-pointer rounded-full px-3.5 text-sm sm:h-8 sm:px-2.5 sm:text-xs" variant="default"> Login </Button>
        </DialogTrigger>

        <DialogContent class="overflow-hidden border-border/70 bg-background p-0 shadow-2xl sm:max-w-83 [&>button]:text-muted-foreground">
            <SharedLoginContent @success="isOpen = false" />
        </DialogContent>
    </Dialog>
</template>

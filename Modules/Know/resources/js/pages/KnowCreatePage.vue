<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getKnowPageBreadcrumbs } from '../lib/navigation';

const breadcrumbs = getKnowPageBreadcrumbs('create');
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader>
                <CardTitle class="text-lg sm:text-xl">Buat Knowledge</CardTitle>
            </CardHeader>
            <CardContent class="text-sm text-muted-foreground">
                Halaman form pembuatan knowledge. Form create bisa diimplementasikan di sini.
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>

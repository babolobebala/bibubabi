<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ShieldCheck, Users } from 'lucide-vue-next';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

interface RoleItem {
    id: number;
    name: string;
    users_count: number;
}

defineProps<{
    roles: RoleItem[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'roles');

function roleVariant(role: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (role === 'super_admin') return 'destructive';
    if (role === 'admin') return 'default';
    return 'secondary';
}
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card class="rounded-xl border-border py-0 shadow-sm">
            <CardHeader class="border-b border-border px-4 py-3">
                <CardTitle class="text-sm font-semibold">Daftar Role</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div v-if="roles.length === 0" class="px-4 py-8 text-center text-sm text-muted-foreground">
                    Belum ada role yang terdaftar.
                </div>
                <div v-else class="divide-y divide-border">
                    <div
                        v-for="role in roles"
                        :key="role.id"
                        class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-muted/40"
                    >
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-4 w-4 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-foreground">{{ role.name }}</p>
                                <Badge :variant="roleVariant(role.name)" class="text-[11px]">
                                    {{ role.name }}
                                </Badge>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-1.5 text-xs text-muted-foreground">
                            <Users class="h-3.5 w-3.5" />
                            <span>{{ role.users_count }} user</span>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>

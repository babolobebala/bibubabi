<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { media } from '@/lib/media';
import { Search, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

interface User {
    id: number;
    nama: string;
    nip: string;
    username: string;
    email_bps: string;
    jabatan: string;
    status_pegawai: string;
    url_foto: string | null;
    roles: string[];
}

const props = defineProps<{
    users: User[];
    roles: string[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'users');

const search = ref('');

const filteredUsers = computed(() => {
    const keyword = search.value.trim().toLowerCase();
    if (!keyword) return props.users;
    return props.users.filter(
        (u) =>
            u.nama?.toLowerCase().includes(keyword) ||
            u.nip?.toLowerCase().includes(keyword) ||
            u.username?.toLowerCase().includes(keyword) ||
            u.jabatan?.toLowerCase().includes(keyword),
    );
});

function getInitials(nama: string): string {
    return nama
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

function roleVariant(role: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (role === 'super_admin') return 'destructive';
    if (role === 'admin') return 'default';
    return 'secondary';
}
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex w-full max-w-sm items-center gap-2 rounded-xl border border-input bg-background px-3 py-2 shadow-sm">
                    <Search class="h-4 w-4 shrink-0 text-muted-foreground" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, NIP, jabatan..."
                        class="border-0 bg-transparent p-0 text-sm shadow-none focus-visible:ring-0"
                    />
                </div>
                <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <Users class="h-4 w-4" />
                    <span>{{ filteredUsers.length }} user</span>
                </div>
            </div>

            <Card class="rounded-xl border-border py-0 shadow-sm">
                <CardHeader class="border-b border-border px-4 py-3">
                    <CardTitle class="text-sm font-semibold">Daftar Pengguna</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div v-if="filteredUsers.length === 0" class="px-4 py-8 text-center text-sm text-muted-foreground">
                        Tidak ada user yang cocok dengan pencarian.
                    </div>
                    <div v-else class="divide-y divide-border">
                        <div
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="flex items-center gap-3 px-4 py-3 transition hover:bg-muted/40"
                        >
                            <Avatar class="h-10 w-10 shrink-0 border border-border">
                                <img
                                    v-if="user.url_foto"
                                    :src="media + user.url_foto"
                                    :alt="user.nama"
                                    class="h-full w-full object-cover"
                                />
                                <AvatarFallback class="bg-primary/10 text-xs font-semibold text-primary">
                                    {{ getInitials(user.nama ?? '?') }}
                                </AvatarFallback>
                            </Avatar>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-foreground">{{ user.nama }}</p>
                                <p class="truncate text-xs text-muted-foreground">{{ user.jabatan ?? '-' }} · {{ user.nip }}</p>
                            </div>

                            <div class="flex shrink-0 flex-wrap justify-end gap-1">
                                <Badge
                                    v-for="role in user.roles"
                                    :key="role"
                                    :variant="roleVariant(role)"
                                    class="text-[11px]"
                                >
                                    {{ role }}
                                </Badge>
                                <Badge v-if="user.roles.length === 0" variant="outline" class="text-[11px] text-muted-foreground">
                                    no role
                                </Badge>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </ModuleContentShell>
</template>

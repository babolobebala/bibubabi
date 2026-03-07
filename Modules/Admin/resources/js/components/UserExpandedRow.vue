<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import type { UserItem } from '../components/user-columns';

defineProps<{
    original: UserItem;
}>();

function roleVariant(role: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (role === 'super_admin') return 'destructive';
    if (role === 'admin') return 'default';
    return 'secondary';
}

function capitalize(str: string): string {
    return str
        .split(' ')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
        .join(' ');
}

function isAktif(status: string | null): boolean {
    if (!status) return false;
    const lower = status.toLowerCase();
    return lower.includes('aktif') && !lower.includes('tidak') && !lower.includes('non');
}
</script>

<template>
    <div class="grid grid-cols-3 gap-x-6 gap-y-3 py-1 text-sm">
        <!-- Nama -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Nama
            </p>
            <p class="mt-0.5 text-xs text-foreground">{{ original.nama ?? '-' }}</p>
        </div>

        <!-- NIP Lama -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                NIP
            </p>
            <p class="mt-0.5 font-mono text-xs text-foreground">{{ original.nip ?? '-' }}</p>
        </div>

        <!-- NIP Baru -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                NIP Baru
            </p>
            <p class="mt-0.5 font-mono text-xs text-foreground">{{ original.nip_baru ?? '-' }}</p>
        </div>

        <!-- Username -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Username
            </p>
            <p class="mt-0.5 text-xs text-foreground">{{ original.username ?? '-' }}</p>
        </div>

        <!-- Email BPS -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Email BPS
            </p>
            <p class="mt-0.5 truncate text-xs text-foreground">{{ original.email_bps ?? '-' }}</p>
        </div>

        <!-- Email Gmail -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Email Gmail
            </p>
            <p class="mt-0.5 truncate text-xs text-foreground">{{ original.email_gmail ?? '-' }}</p>
        </div>

        <!-- Golongan -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Golongan
            </p>
            <p class="mt-0.5 text-xs text-foreground">{{ original.golongan ?? '-' }}</p>
        </div>

        <!-- Jabatan -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Jabatan
            </p>
            <p class="mt-0.5 text-xs text-foreground">{{ original.jabatan ?? '-' }}</p>
        </div>

        <!-- Status -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Status
            </p>
            <div class="mt-1">
                <Badge
                    v-if="original.status_pegawai"
                    variant="outline"
                    :class="isAktif(original.status_pegawai)
                        ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px]'
                        : 'border-destructive/30 bg-destructive/10 text-destructive text-[10px]'"
                >
                    {{ capitalize(original.status_pegawai) }}
                </Badge>
                <span v-else class="text-xs text-muted-foreground">-</span>
            </div>
        </div>

        <!-- Role -->
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                Role
            </p>
            <div class="mt-1 flex flex-wrap gap-1">
                <Badge
                    v-for="role in original.roles"
                    :key="role"
                    :variant="roleVariant(role)"
                    class="text-[10px]"
                >
                    {{ role }}
                </Badge>
                <span v-if="original.roles.length === 0" class="text-xs text-muted-foreground">
                    no role
                </span>
            </div>
        </div>
    </div>
</template>

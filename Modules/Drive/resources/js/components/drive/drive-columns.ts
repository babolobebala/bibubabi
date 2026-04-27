import { Badge } from '@/components/ui/badge';
import { DataTableRowActions } from '@/components/ui/data-table';
import type { ColumnDef } from '@tanstack/vue-table';
import { ExternalLink, Pencil, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';

export interface DriveItem {
    id: number;
    nama: string | null;
    link: string | null;
    jenis: 'personal' | 'tim';
    personal: number | null;
    personal_user?: {
        id: number;
        nama: string;
        username: string;
    } | null;
    tim: number | null;
    tim_role?: {
        id: number;
        name: string;
    } | null;
    akses: 'edit' | 'view';
    created_at: string;
    updated_at: string;
}

export const getDriveColumns = (actions: {
    onEdit: (drive: DriveItem) => void;
    onDelete: (drive: DriveItem) => void;
}): ColumnDef<DriveItem>[] => [
    {
        accessorKey: 'nama',
        header: 'Nama Drive',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.original.nama || '-'),
    },
    {
        accessorKey: 'jenis',
        header: 'Jenis',
        cell: ({ row }) => {
            const isPersonal = row.original.jenis === 'personal';
            return h(Badge, { 
                variant: isPersonal ? 'outline' : 'secondary',
                class: 'capitalize'
            }, () => row.original.jenis);
        },
    },
    {
        id: 'owner',
        header: 'Pemilik / Tim',
        cell: ({ row }) => {
            let label = '-';
            if (row.original.jenis === 'personal') {
                label = row.original.personal_user ? `${row.original.personal_user.nama}` : '-';
            } else {
                label = row.original.tim_role ? row.original.tim_role.name : '-';
            }
            return h('span', { class: 'text-sm text-muted-foreground' }, label);
        },
    },
    {
        accessorKey: 'akses',
        header: 'Akses',
        cell: ({ row }) => h(Badge, { 
            variant: row.original.akses === 'edit' ? 'default' : 'secondary',
            class: 'capitalize text-[10px] px-1.5 py-0 h-4'
        }, () => row.original.akses),
    },
    {
        accessorKey: 'link',
        header: 'Link',
        cell: ({ row }) => row.original.link ? h('a', { 
            href: row.original.link, 
            target: '_blank',
            class: 'text-blue-600 hover:underline flex items-center gap-1 text-xs truncate max-w-[150px]'
        }, [
            row.original.link,
            h(ExternalLink, { class: 'h-3 w-3' })
        ]) : '-',
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) =>
            h(DataTableRowActions, {
                actions: [
                    {
                        label: 'Edit Drive',
                        icon: Pencil,
                        onClick: () => {
                            actions.onEdit(row.original);
                        },
                    },
                    {
                        label: 'Hapus Drive',
                        icon: Trash2,
                        destructive: true,
                        separator: true,
                        onClick: () => {
                            actions.onDelete(row.original);
                        },
                    },
                ],
            }),
        meta: { headerClass: 'w-10', cellClass: 'w-10' },
    },
];

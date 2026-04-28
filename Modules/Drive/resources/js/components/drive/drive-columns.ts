import { Badge } from '@/components/ui/badge';
import { DataTableRowActions } from '@/components/ui/data-table';
import type { ColumnDef } from '@tanstack/vue-table';
import { CheckCircle2, ExternalLink, Info, Pencil, Trash2, XCircle } from 'lucide-vue-next';
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
    status: 'success' | 'error';
    catatan: string | null;
    created_at: string;
    updated_at: string;
}

export const getDriveColumns = (actions: {
    onView: (drive: DriveItem) => void;
    onEdit: (drive: DriveItem) => void;
    onDelete: (drive: DriveItem) => void;
}): ColumnDef<DriveItem>[] => [
        {
            id: 'placeholder',
            header: '',
            cell: () => h('div', { class: 'w-6' }),
            meta: { headerClass: 'w-10', cellClass: 'w-10' },
        },
        {
            accessorKey: 'nama',
            header: 'Nama Drive',
            cell: ({ row }) => h('div', { class: 'text-xs font-medium' }, row.original.nama || '-'),
        },
        {
            accessorKey: 'jenis',
            header: 'Jenis',
            cell: ({ row }) => {
                const isPersonal = row.original.jenis === 'personal';
                return h(Badge, {
                    variant: isPersonal ? 'secondary' : 'default',
                    class: 'capitalize text-xs'
                }, () => row.original.jenis);
            },
            meta: { headerClass: 'hidden md:table-cell', cellClass: 'hidden md:table-cell' },
        },
        {
            id: 'owner',
            header: 'Pemilik',
            cell: ({ row }) => {
                let label = '-';
                if (row.original.jenis === 'personal') {
                    label = row.original.personal_user ? `${row.original.personal_user.nama}` : '-';
                } else {
                    label = row.original.tim_role ? row.original.tim_role.name : '-';
                }
                return h('span', { class: 'text-xs text-muted-foreground' }, label);
            },
            meta: { headerClass: 'hidden md:table-cell', cellClass: 'hidden md:table-cell' },
        },
        {
            accessorKey: 'link',
            header: 'Link',
            cell: ({ row }) => row.original.link ? h('a', {
                href: row.original.link,
                target: '_blank',
                class: 'text-blue-500 hover:text-blue-700 transition-colors'
            }, h(ExternalLink, { class: 'h-4 w-4' })) : '-',
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
                const isSuccess = row.original.status === 'success';
                return h(isSuccess ? CheckCircle2 : XCircle, {
                    class: `h-4 w-4 ${isSuccess ? 'text-green-500' : 'text-red-500'}`
                });
            },
        },
        {
            id: 'actions',
            header: '',
            cell: ({ row }) =>
                h(DataTableRowActions, {
                    actions: [
                        {
                            label: 'Lihat Drive',
                            icon: Info,
                            onClick: () => {
                                actions.onView(row.original);
                            },
                        },
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

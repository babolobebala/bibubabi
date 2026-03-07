import { Badge } from '@/components/ui/badge';
import { DataTableRowActions } from '@/components/ui/data-table';
import type { ColumnDef } from '@tanstack/vue-table';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';

export interface RoleItem {
    id: number;
    name: string;
    users_count: number;
}

function roleVariant(role: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (role === 'super_admin') return 'destructive';
    if (role === 'admin') return 'default';
    return 'secondary';
}

export const roleColumns: ColumnDef<RoleItem>[] = [
    {
        accessorKey: 'name',
        header: 'Nama Role',
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-2' }, [
                h(Badge, { variant: roleVariant(row.original.name), class: 'text-[11px]' }, () =>
                    row.original.name,
                ),
            ]),
    },
    {
        accessorKey: 'users_count',
        header: 'Jumlah User',
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                `${row.original.users_count} user`,
            ),
        enableColumnFilter: false,
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) =>
            h(DataTableRowActions, {
                actions: [
                    {
                        label: 'Edit Role',
                        icon: Pencil,
                        onClick: () => {
                            // TODO: handle edit role
                            console.log('edit role', row.original);
                        },
                    },
                    {
                        label: 'Hapus Role',
                        icon: Trash2,
                        destructive: true,
                        separator: true,
                        onClick: () => {
                            // TODO: handle delete role
                            console.log('delete role', row.original);
                        },
                    },
                ],
            }),
        enableSorting: false,
        enableColumnFilter: false,
        meta: { headerClass: 'w-10', cellClass: 'w-10' },
    },
];

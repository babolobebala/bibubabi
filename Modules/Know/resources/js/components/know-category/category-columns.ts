import { DataTableRowActions } from '@/components/ui/data-table';
import type { ColumnDef } from '@tanstack/vue-table';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';

export interface KnowCategoryItem {
    id: number;
    nama: string;
}

export const getKnowCategoryColumns = (actions: {
    onEdit: (category: KnowCategoryItem) => void;
    onDelete: (category: KnowCategoryItem) => void;
}): ColumnDef<KnowCategoryItem>[] => [
    {
        accessorKey: 'nama',
        header: 'Nama Kategori',
        enableSorting: true,
        cell: ({ row }) => h('span', { class: 'text-sm font-medium text-foreground' }, row.original.nama),
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) =>
            h(DataTableRowActions, {
                actions: [
                    {
                        label: 'Edit Kategori',
                        icon: Pencil,
                        onClick: () => {
                            actions.onEdit(row.original);
                        },
                    },
                    {
                        label: 'Hapus Kategori',
                        icon: Trash2,
                        destructive: true,
                        separator: true,
                        onClick: () => {
                            actions.onDelete(row.original);
                        },
                    },
                ],
            }),
        enableSorting: false,
        enableColumnFilter: false,
        meta: { headerClass: 'w-10', cellClass: 'w-10' },
    },
];

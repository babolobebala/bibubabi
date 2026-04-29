import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

export interface KnowItem {
    id: number;
    nama: string | null;
    deskripsi: string | null;
    link: unknown[] | null;
    pic: string | null;
    tanggal_pelaksanaan: string | null;
    kategori: string[] | null;
}

export const knowColumns: ColumnDef<KnowItem>[] = [
    {
        accessorKey: 'nama',
        header: 'Nama Knowledge',
        cell: ({ row }) => h('div', { class: 'text-xs font-medium' }, row.original.nama || '-'),
    },
    {
        accessorKey: 'pic',
        header: 'PIC',
        cell: ({ row }) => h('span', { class: 'text-xs text-muted-foreground' }, row.original.pic || '-'),
    },
    {
        accessorKey: 'tanggal_pelaksanaan',
        header: 'Tanggal',
        cell: ({ row }) => h('span', { class: 'text-xs text-muted-foreground' }, row.original.tanggal_pelaksanaan || '-'),
    },
    {
        id: 'kategori',
        header: 'Kategori',
        cell: ({ row }) => {
            const categories = row.original.kategori ?? [];
            if (categories.length === 0) {
                return h('span', { class: 'text-xs text-muted-foreground' }, '-');
            }

            return h('span', { class: 'text-xs text-muted-foreground' }, categories.join(', '));
        },
    },
    {
        id: 'link_count',
        header: 'Jumlah Link',
        cell: ({ row }) => {
            const links = Array.isArray(row.original.link) ? row.original.link : [];
            return h('span', { class: 'text-xs text-muted-foreground' }, String(links.length));
        },
    },
];

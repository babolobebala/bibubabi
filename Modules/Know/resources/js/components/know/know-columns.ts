import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

export interface KnowItem {
    id: number;
    nama: string | null;
    deskripsi: string | null;
    link: KnowLinkItem[] | null;
    pic: string | null;
    tanggal_pelaksanaan: string | null;
    kategori: string[] | null;
}

interface KnowLinkItem {
    nama: string | null;
    link: string | null;
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
        id: 'links',
        header: 'Link',
        cell: ({ row }) => {
            const links = Array.isArray(row.original.link) ? row.original.link : [];

            if (links.length === 0) {
                return h('span', { class: 'text-xs text-muted-foreground' }, '-');
            }

            return h(
                'div',
                { class: 'flex flex-col gap-1' },
                links.map((item, index) => {
                    const linkUrl = typeof item.link === 'string' ? item.link.trim() : '';
                    const linkLabelRaw = typeof item.nama === 'string' ? item.nama.trim() : '';
                    const linkLabel = linkLabelRaw !== '' ? linkLabelRaw : linkUrl;
                    const vnodeKey = `${linkLabel}-${linkUrl}-${index}`;

                    if (linkLabel === '') {
                        return h('span', { key: vnodeKey, class: 'text-xs text-muted-foreground' }, '-');
                    }

                    if (linkUrl === '') {
                        return h('span', { key: vnodeKey, class: 'text-xs text-muted-foreground break-all' }, linkLabel);
                    }

                    return h(
                        'a',
                        {
                            key: vnodeKey,
                            href: linkUrl,
                            target: '_blank',
                            rel: 'noopener noreferrer',
                            class: 'text-xs text-blue-600 hover:underline break-all',
                        },
                        linkLabel
                    );
                })
            );
        },
    },
];

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { AppWindow, Building2, MessageCircleMore, type LucideIcon, Megaphone, MessagesSquare } from 'lucide-vue-next';

interface ComplaintChannel {
    title: string;
    description: string;
    icon: LucideIcon;
    badge: string;
    responseTime: string;
    availability: string;
    actionLabel: string;
    href?: string;
}

const channels: ComplaintChannel[] = [
    {
        title: 'Website SAKU',
        description: 'Kanal utama yang paling rapi untuk tindak lanjut pengaduan.',
        icon: AppWindow,
        badge: 'Utama',
        responseTime: 'Terstruktur',
        availability: '24/7',
        actionLabel: 'Laporkan',
        href: '/pengaduan',
    },
    {
        title: 'WhatsApp',
        description: 'Pilihan tercepat untuk pertanyaan singkat dan arahan awal.',
        icon: MessageCircleMore,
        badge: 'Cepat',
        responseTime: 'Cepat',
        availability: 'Jam kerja',
        actionLabel: 'Buka WA',
        href: 'http://wa.me/+6282144406055',
    },
    {
        title: 'Datang Langsung',
        description: 'Cocok jika perlu klarifikasi detail atau membawa dokumen.',
        icon: Building2,
        badge: 'Tatap muka',
        responseTime: 'Antrean',
        availability: 'Jam kantor',
        actionLabel: 'Info Kantor',
    },
    {
        title: 'Media Sosial',
        description: 'Untuk pertanyaan umum atau pengaduan awal yang perlu diarahkan.',
        icon: Megaphone,
        badge: 'Publik',
        responseTime: 'Bervariasi',
        availability: 'Tergantung kanal',
        actionLabel: 'Lihat Kanal',
    },
    {
        title: 'SP4N-LAPOR!',
        description: 'Jalur pengaduan nasional yang formal dan terdokumentasi.',
        icon: MessagesSquare,
        badge: 'Resmi',
        responseTime: 'Mekanisme',
        availability: '24/7',
        actionLabel: 'Buka SP4N',
        href: 'https://www.lapor.go.id/',
    },
];
</script>

<template>
    <section class="py-10 sm:py-12 lg:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold tracking-[0.24em] text-primary uppercase">Kanal Pengaduan</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-foreground sm:text-4xl lg:text-[2.6rem]">Pilih kanal pelaporan</h2>
                <p class="mt-3 text-sm leading-6 text-muted-foreground sm:text-base">
                    Gunakan kanal yang paling sesuai dengan kebutuhanmu. Semua pengaduan bisa diarahkan ke jalur yang tepat untuk tindak lanjut.
                </p>
            </div>

            <div class="mt-7 grid gap-2.5 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
                <component
                    :is="channel.href ? 'a' : 'div'"
                    v-for="channel in channels"
                    :key="channel.title"
                    :href="channel.href"
                    :target="channel.href ? '_blank' : undefined"
                    :rel="channel.href ? 'noopener noreferrer' : undefined"
                    class="block"
                >
                    <Card class="h-full rounded-lg border-border py-0 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <CardContent class="flex h-full flex-col items-center gap-2 p-3.5 text-center">
                            <div class="grid h-12 w-12 place-items-center rounded-full border border-primary/15 bg-accent text-primary">
                                <component :is="channel.icon" class="h-5 w-5" />
                            </div>
                            <p class="line-clamp-2 min-h-9 text-[13px] leading-4 font-medium text-foreground">
                                {{ channel.title }}
                            </p>
                            <p class="line-clamp-2 min-h-8 text-[11px] leading-4 text-muted-foreground">
                                {{ channel.description }}
                            </p>
                            <Badge variant="secondary" class="mt-0.5">
                                {{ channel.badge }}
                            </Badge>
                        </CardContent>
                    </Card>
                </component>
            </div>
        </div>
    </section>
</template>

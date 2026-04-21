<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { ChartContainer } from '@/components/ui/chart';
import { VisArea, VisLine, VisXYContainer } from '@unovis/vue';
import {
    Activity,
    ArrowDownRight,
    ArrowUpRight,
    BookOpen,
    Briefcase,
    CircleAlert,
    Coins,
    GraduationCap,
    Heart,
    MapPin,
    Navigation,
    Scale,
    TrendingUp,
    UserCircle,
    Users,
    Wallet,
} from 'lucide-vue-next';
import { onMounted } from 'vue';

interface Trend {
    text: string;
    isUp: boolean;
}

interface Indicator {
    ticker: string;
    title: string;
    subtitle: string;
    value: string;
    unit: string;
    trend: Trend;
    date: string;
    icon: any;
    iconColor: string;
    sparkline: { x: number; y: number }[];
}

const indicators = {
    kependudukan: [
        {
            ticker: 'PMDN',
            title: 'Kepadatan Penduduk',
            subtitle: 'Tahunan • 2024',
            value: '84,12',
            unit: 'Jiwa/Km²',
            trend: { text: '1,24', isUp: true },
            date: '01 Feb 2025',
            icon: MapPin,
            iconColor: 'bg-indigo-500/10 text-indigo-500',
            sparkline: [80, 81.2, 82.5, 83.1, 84.12].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'SR',
            title: 'Sex Ratio',
            subtitle: 'Tahunan • 2024',
            value: '101,4',
            unit: 'Idx',
            trend: { text: '0,2', isUp: true },
            date: '01 Feb 2025',
            icon: UserCircle,
            iconColor: 'bg-indigo-600/10 text-indigo-600',
            sparkline: [100.8, 101.0, 101.2, 101.3, 101.4].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'LPP',
            title: 'Laju Pertumbuhan Penduduk',
            subtitle: 'Tahunan • 2024',
            value: '1,45',
            unit: '%',
            trend: { text: '0,11', isUp: false },
            date: '01 Feb 2025',
            icon: TrendingUp,
            iconColor: 'bg-indigo-400/10 text-indigo-400',
            sparkline: [1.6, 1.55, 1.5, 1.48, 1.45].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    ketenagakerjaan: [
        {
            ticker: 'TPT',
            title: 'Tingkat Pengangguran Terbuka',
            subtitle: 'Tahunan • 2024',
            value: '3,84',
            unit: '%',
            trend: { text: '0,42', isUp: false },
            date: '05 Nov 2025',
            icon: Briefcase,
            iconColor: 'bg-blue-500/10 text-blue-500',
            sparkline: [4.2, 4.1, 4.0, 3.9, 3.84].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'TPAK',
            title: 'Tingkat Partisipasi Angkatan Kerja',
            subtitle: 'Tahunan • 2024',
            value: '68,12',
            unit: '%',
            trend: { text: '1,24', isUp: true },
            date: '05 Nov 2025',
            icon: Users,
            iconColor: 'bg-blue-600/10 text-blue-600',
            sparkline: [66, 66.5, 67.2, 67.8, 68.12].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    kemiskinan: [
        {
            ticker: 'PM',
            title: 'Persentase Penduduk Miskin',
            subtitle: 'Tahunan • 2024',
            value: '12,45',
            unit: '%',
            trend: { text: '0,35', isUp: false },
            date: '15 Mar 2025',
            icon: Users,
            iconColor: 'bg-red-500/10 text-red-500',
            sparkline: [14, 13.5, 13.2, 12.8, 12.45].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'P1',
            title: 'Indeks Kedalaman Kemiskinan',
            subtitle: 'Tahunan • 2024',
            value: '1,84',
            unit: 'Idx',
            trend: { text: '0,12', isUp: false },
            date: '15 Mar 2025',
            icon: Navigation,
            iconColor: 'bg-rose-500/10 text-rose-500',
            sparkline: [2.1, 2, 1.95, 1.9, 1.84].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'P2',
            title: 'Indeks Keparahan Kemiskinan',
            subtitle: 'Tahunan • 2024',
            value: '0,42',
            unit: 'Idx',
            trend: { text: '0,05', isUp: false },
            date: '15 Mar 2025',
            icon: CircleAlert,
            iconColor: 'bg-red-400/10 text-red-400',
            sparkline: [0.5, 0.48, 0.46, 0.44, 0.42].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    pendidikan: [
        {
            ticker: 'RLS',
            title: 'Rata-rata Lama Sekolah',
            subtitle: 'Tahunan • 2024',
            value: '8,42',
            unit: 'Thn',
            trend: { text: '0,12', isUp: true },
            date: '25 Mar 2025',
            icon: Scale,
            iconColor: 'bg-emerald-500/10 text-emerald-500',
            sparkline: [8, 8.1, 8.2, 8.35, 8.42].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'HLS',
            title: 'Harapan Lama Sekolah',
            subtitle: 'Tahunan • 2024',
            value: '12,94',
            unit: 'Thn',
            trend: { text: '0,01', isUp: true },
            date: '25 Mar 2025',
            icon: GraduationCap,
            iconColor: 'bg-emerald-600/10 text-emerald-600',
            sparkline: [12.9, 12.91, 12.92, 12.93, 12.94].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'APS',
            title: 'Angka Partisipasi Sekolah',
            subtitle: 'Tahunan • 2024',
            value: '98,12',
            unit: '%',
            trend: { text: '0,24', isUp: true },
            date: '25 Mar 2025',
            icon: BookOpen,
            iconColor: 'bg-teal-500/10 text-teal-500',
            sparkline: [97, 97.4, 97.8, 98, 98.12].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    pembangunan_manusia: [
        {
            ticker: 'IPM',
            title: 'Indeks Pembangunan Manusia',
            subtitle: 'Tahunan • 2024',
            value: '72,45',
            unit: 'Idx',
            trend: { text: '0,84', isUp: true },
            date: '10 Mei 2025',
            icon: Heart,
            iconColor: 'bg-orange-500/10 text-orange-500',
            sparkline: [70, 71, 71.5, 72, 72.45].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IPG',
            title: 'Indeks Pembangunan Gender',
            subtitle: 'Tahunan • 2024',
            value: '94,10',
            unit: 'Idx',
            trend: { text: '0,12', isUp: true },
            date: '10 Mei 2025',
            icon: Users,
            iconColor: 'bg-orange-600/10 text-orange-600',
            sparkline: [93.5, 93.8, 93.9, 94.0, 94.1].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IKG',
            title: 'Indeks Ketimpangan Gender',
            subtitle: 'Tahunan • 2024',
            value: '0,444',
            unit: 'Idx',
            trend: { text: '0,02', isUp: false },
            date: '10 Mei 2025',
            icon: Activity,
            iconColor: 'bg-amber-500/10 text-amber-500',
            sparkline: [0.46, 0.455, 0.45, 0.448, 0.444].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    pdrb: [
        {
            ticker: 'PDRB',
            title: 'PDRB ADHK',
            subtitle: 'Tahunan • 2024',
            value: '18,42',
            unit: 'Tr Rp',
            trend: { text: '3,12', isUp: true },
            date: '11 Apr 2025',
            icon: Wallet,
            iconColor: 'bg-yellow-500/10 text-yellow-500',
            sparkline: [17, 17.5, 17.8, 18.2, 18.42].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'LPE',
            title: 'Laju Pertumbuhan Ekonomi',
            subtitle: 'Tahunan • 2024',
            value: '4,12',
            unit: '%',
            trend: { text: '0,11', isUp: true },
            date: '11 Apr 2025',
            icon: TrendingUp,
            iconColor: 'bg-yellow-600/10 text-yellow-600',
            sparkline: [3.8, 3.9, 4.0, 4.1, 4.12].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'PKAP',
            title: 'PDRB Per Kapita',
            subtitle: 'Tahunan • 2024',
            value: '54,38',
            unit: 'Jt Rp',
            trend: { text: '2,84', isUp: true },
            date: '11 Apr 2025',
            icon: Coins,
            iconColor: 'bg-amber-600/10 text-amber-600',
            sparkline: [50, 51.5, 52.8, 53.5, 54.38].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
};

const categories = [
    { key: 'kependudukan', name: 'Kependudukan', color: 'bg-indigo-500' },
    { key: 'ketenagakerjaan', name: 'Ketenagakerjaan', color: 'bg-blue-500' },
    { key: 'kemiskinan', name: 'Kemiskinan', color: 'bg-red-500' },
    { key: 'pendidikan', name: 'Pendidikan', color: 'bg-emerald-500' },
    { key: 'pembangunan_manusia', name: 'Pembangunan Manusia', color: 'bg-orange-500' },
    { key: 'pdrb', name: 'Produk Domestik Regional Bruto', color: 'bg-yellow-500' },
];

const fetchBpsData = async () => {
    const apiKey = import.meta.env.VITE_BPS_API;
    console.log('Fetching BPS data...', { hasKey: !!apiKey });

    if (!apiKey) {
        console.warn('BPS API Key is missing in environment variables.');
        return;
    }

    const url = `https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/5207/var/278/th/110:120/key/${apiKey}`;

    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'OK') {
            console.log('BPS Data Strategis Results:', data);
            // You can map the data to the 'indicators' object here in the future
        } else {
            console.error('BPS API Returned Error:', data.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Failed to fetch BPS data:', error instanceof Error ? error.message : error);
    }
};

onMounted(() => {
    fetchBpsData();
});
</script>

<template>
    <section class="relative overflow-hidden py-6 sm:py-8 lg:py-10">
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-7 text-center">
                <div
                    class="mx-auto mb-4 flex w-fit items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-[10px] font-bold tracking-[0.2em] text-blue-400 uppercase"
                >
                    <Activity class="h-3.5 w-3.5" />
                    Data Strategis KSB
                </div>
                <h2 class="text-2xl font-black tracking-tight text-foreground sm:text-3xl lg:text-4xl">Daftar Indikator Strategis</h2>
            </div>

            <!-- Categories Accordion -->
            <Accordion type="multiple" class="columns-1 gap-6 space-y-6 md:columns-2 md:space-y-0">
                <AccordionItem
                    v-for="cat in categories"
                    :key="cat.key"
                    :value="cat.key"
                    class="mb-4 h-fit break-inside-avoid rounded-2xl border border-border/60 bg-card/40 px-5 transition-all data-[state=open]:border-primary/30 data-[state=open]:bg-card"
                >
                    <AccordionTrigger class="px-0 py-4 hover:no-underline sm:py-5">
                        <div class="flex flex-1 items-center gap-3 overflow-hidden sm:gap-4">
                            <div :class="['h-7 w-1.5 shrink-0 rounded-full', cat.color]"></div>
                            <h3 class="truncate text-lg font-black tracking-tight text-foreground sm:text-xl">
                                {{ cat.name }}
                            </h3>
                            <span
                                class="shrink-0 rounded-full bg-muted/50 px-2 py-1 text-[10px] font-bold whitespace-nowrap text-muted-foreground sm:px-3 sm:text-xs"
                            >
                                {{ indicators[cat.key as keyof typeof indicators].length }} Indikator
                            </span>
                        </div>
                    </AccordionTrigger>

                    <AccordionContent class="pb-6">
                        <div class="mt-4 space-y-1">
                            <div
                                v-for="item in indicators[cat.key as keyof typeof indicators]"
                                :key="item.title"
                                class="group flex cursor-pointer items-center gap-2.5 overflow-hidden rounded-xl p-2.5 transition-all hover:bg-muted/40 sm:gap-3 sm:p-3"
                            >
                                <!-- Asset Icon & Info -->
                                <div class="flex w-[120px] shrink-0 items-center gap-2.5 overflow-hidden sm:w-[150px] sm:gap-3">
                                    <div
                                        :class="[
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-xl shadow-sm transition-transform group-hover:scale-105 sm:h-10 sm:w-10',
                                            item.iconColor,
                                        ]"
                                    >
                                        <component :is="item.icon" class="h-4 w-4 sm:h-5 sm:w-5" />
                                    </div>
                                    <div class="overflow-hidden">
                                        <div
                                            class="truncate text-xs leading-tight font-black tracking-tight transition-colors group-hover:text-primary sm:text-sm"
                                        >
                                            {{ item.ticker }}
                                        </div>
                                        <div class="truncate text-[10px] font-medium tracking-wider text-muted-foreground uppercase sm:text-xs">
                                            {{ item.title }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Sparkline (fluid center) -->
                                <div class="h-8 min-w-0 flex-1 px-2 sm:h-10 sm:px-4">
                                    <ChartContainer
                                        :config="{
                                            value: {
                                                color: item.trend.isUp ? '#10b981' : '#10b981',
                                            },
                                        }"
                                        class="h-full w-full opacity-50 transition-opacity group-hover:opacity-100"
                                    >
                                        <VisXYContainer :data="item.sparkline" :height="Infinity" :margin="{ top: 5, bottom: 5, left: 5, right: 5 }">
                                            <VisLine :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :stroke-width="2.5" />
                                            <VisArea :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :opacity="0.1" />
                                        </VisXYContainer>
                                    </ChartContainer>
                                </div>

                                <!-- Value & Change -->
                                <div class="w-[75px] shrink-0 text-right sm:w-[90px]">
                                    <div class="text-sm leading-tight font-black tracking-tight whitespace-nowrap tabular-nums sm:text-base">
                                        {{ item.value }}<span class="ml-0.5 text-[10px] text-muted-foreground uppercase">{{ item.unit }}</span>
                                    </div>
                                    <div
                                        :class="[
                                            'mt-1 flex items-center justify-end gap-0.5 text-[10px] font-bold whitespace-nowrap italic sm:text-xs',
                                            item.trend.isUp ? 'text-emerald-500' : 'text-orange-500',
                                        ]"
                                    >
                                        <ArrowUpRight v-if="item.trend.isUp" class="h-3 w-3" />
                                        <ArrowDownRight v-else class="h-3 w-3" />
                                        {{ item.trend.isUp ? '+' : '-' }}{{ item.trend.text }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>

            <!-- Footer / CTA -->
            <div class="mt-10 flex flex-col items-center gap-6 text-center">
                <div class="group relative inline-flex">
                    <div
                        class="absolute -inset-px rounded-full bg-linear-to-r from-blue-500 to-emerald-500 opacity-25 blur-lg transition-all duration-1000 group-hover:-inset-1 group-hover:opacity-50 group-hover:duration-200"
                    ></div>
                    <button
                        class="relative flex cursor-pointer items-center gap-3 rounded-full bg-foreground px-10 py-4 text-sm font-bold text-background transition-all hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Lihat Lebih Banyak Data Strategis
                        <ArrowUpRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Optional: Fine-tune unovis container visibility */
:deep([data-vis-xy-container]) {
    overflow: visible !important;
}

/* Customize accordion arrow position */
:deep([data-slot='accordion-trigger'] > svg) {
    width: 1.25rem;
    height: 1.25rem;
}
</style>

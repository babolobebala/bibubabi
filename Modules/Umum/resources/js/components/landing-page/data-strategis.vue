<script setup lang="ts">
import { ChartContainer } from '@/components/ui/chart';
import { VisArea, VisLine, VisXYContainer } from '@unovis/vue';
import { Activity, ArrowDownRight, ArrowUpRight, Hand, Percent, Scale, ShoppingCart, TrendingUp, Users, Wallet } from 'lucide-vue-next';

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
    ekonomi: [
        {
            ticker: 'LPE',
            title: 'Laju Pertumbuhan Ekonomi',
            subtitle: 'Tahunan • 2024',
            value: '4,12',
            unit: '%',
            trend: { text: '0,39', isUp: false },
            date: '11 Apr 2025',
            icon: TrendingUp,
            iconColor: 'bg-orange-500/10 text-orange-500',
            sparkline: [4.5, 4.3, 4.4, 4.2, 4.15, 4.12].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'INF',
            title: 'Inflasi (m-t-m)',
            subtitle: 'Bulanan • Januari 2026',
            value: '-0,21',
            unit: '%',
            trend: { text: '0,92', isUp: false },
            date: '09 Feb 2026',
            icon: Percent,
            iconColor: 'bg-yellow-500/10 text-yellow-500',
            sparkline: [0.5, 0.2, -0.1, -0.15, -0.21].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IHK',
            title: 'Indeks Harga Konsumen',
            subtitle: 'Bulanan • Januari 2026',
            value: '110,21',
            unit: 'Idx',
            trend: { text: '0,23', isUp: false },
            date: '07 Feb 2026',
            icon: ShoppingCart,
            iconColor: 'bg-orange-500/10 text-orange-500',
            sparkline: [111, 110.8, 110.5, 110.3, 110.21].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'PDRB',
            title: 'PDRB Per Kapita',
            subtitle: 'Tahunan • 2024',
            value: '54,38',
            unit: 'Jt Rp',
            trend: { text: '2,74', isUp: true },
            date: '11 Apr 2025',
            icon: Wallet,
            iconColor: 'bg-yellow-600/10 text-yellow-600',
            sparkline: [50, 51.5, 52.8, 53.5, 54.38].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
    sosial: [
        {
            ticker: 'GINI',
            title: 'Rasio Gini',
            subtitle: 'Tahunan • 2024',
            value: '0,393',
            unit: 'Pts',
            trend: { text: '0,019', isUp: false },
            date: '01 Des 2025',
            icon: Scale,
            iconColor: 'bg-yellow-600/10 text-yellow-600',
            sparkline: [0.41, 0.405, 0.4, 0.395, 0.393].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IPG',
            title: 'Indeks Pembangunan Gender',
            subtitle: 'Tahunan • 2024',
            value: '94,10',
            unit: 'Idx',
            trend: { text: '0,07', isUp: true },
            date: '05 Mei 2025',
            icon: Users,
            iconColor: 'bg-orange-500/10 text-orange-500',
            sparkline: [93.5, 93.8, 93.9, 94.0, 94.1].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IKG',
            title: 'Indeks Ketimpangan Gender',
            subtitle: 'Tahunan • 2024',
            value: '0,444',
            unit: 'Idx',
            trend: { text: '0,021', isUp: false },
            date: '05 Mei 2025',
            icon: Activity,
            iconColor: 'bg-yellow-500/10 text-yellow-500',
            sparkline: [0.46, 0.455, 0.45, 0.448, 0.444].map((y, x) => ({ x, y })),
        },
        {
            ticker: 'IDG',
            title: 'Indeks Pemberdayaan Gender',
            subtitle: 'Tahunan • 2024',
            value: '77,02',
            unit: 'Idx',
            trend: { text: '0,02', isUp: true },
            date: '05 Mei 2025',
            icon: Hand,
            iconColor: 'bg-orange-600/10 text-orange-600',
            sparkline: [76.5, 76.8, 76.9, 77.0, 77.02].map((y, x) => ({ x, y })),
        },
    ] as Indicator[],
};
</script>

<template>
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-20 text-center">
                <div
                    class="mx-auto mb-4 flex w-fit items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-xs font-bold tracking-widest text-blue-400 uppercase"
                >
                    <Activity class="h-3 w-3" />
                    Data Strategis KSB
                </div>
                <h2 class="text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl">Indikator Pembangunan</h2>
                <p class="mt-4 text-lg text-muted-foreground">Monitoring capaian indikator makro secara real-time</p>
            </div>

            <!-- Categories Section (1xN List Style) -->
            <div class="grid gap-16 lg:grid-cols-2">
                <!-- Ekonomi Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-border/60 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-1.5 rounded-full bg-orange-500"></div>
                            <h3 class="text-2xl font-black tracking-tight text-foreground">Ekonomi</h3>
                        </div>
                        <span class="rounded-full bg-muted/50 px-3 py-1 text-xs font-bold text-muted-foreground"
                            >{{ indicators.ekonomi.length }} Assets</span
                        >
                    </div>

                    <div class="space-y-1">
                        <div
                            v-for="item in indicators.ekonomi"
                            :key="item.title"
                            class="group flex cursor-pointer items-center gap-4 rounded-2xl p-4 transition-all hover:bg-muted/40"
                        >
                            <!-- Asset Icon & Info -->
                            <div class="flex min-w-[140px] items-center gap-4 sm:min-w-[200px]">
                                <div
                                    :class="[
                                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl shadow-sm transition-transform group-hover:scale-105',
                                        item.iconColor,
                                    ]"
                                >
                                    <component :is="item.icon" class="h-6 w-6" />
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-base leading-tight font-black tracking-tight transition-colors group-hover:text-primary">
                                        {{ item.ticker }}
                                    </div>
                                    <div class="truncate text-xs font-medium text-muted-foreground">
                                        {{ item.title }}
                                    </div>
                                </div>
                            </div>

                            <!-- Sparkline (using Shadcn Chart base) -->
                            <div class="hidden h-14 min-w-[100px] flex-1 px-4 sm:block">
                                <ChartContainer
                                    :config="{
                                        value: {
                                            color: item.trend.isUp ? '#10b981' : '#f59e0b',
                                        },
                                    }"
                                    class="h-full w-full opacity-60 transition-opacity group-hover:opacity-100"
                                >
                                    <VisXYContainer :data="item.sparkline" :height="56" :margin="{ top: 5, bottom: 5, left: 5, right: 5 }">
                                        <VisLine :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :stroke-width="2.5" />
                                        <VisArea :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :opacity="0.1" />
                                    </VisXYContainer>
                                </ChartContainer>
                            </div>

                            <!-- Value & Change -->
                            <div class="ml-auto min-w-[80px] text-right">
                                <div class="text-lg leading-tight font-black tracking-tight tabular-nums">
                                    {{ item.value }}<span class="ml-0.5 text-[10px] text-muted-foreground">{{ item.unit }}</span>
                                </div>
                                <div
                                    :class="[
                                        'mt-0.5 flex items-center justify-end gap-0.5 text-xs font-bold italic',
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
                </div>

                <!-- Sosial Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-border/60 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-1.5 rounded-full bg-blue-500"></div>
                            <h3 class="text-2xl font-black tracking-tight text-foreground">Sosial</h3>
                        </div>
                        <span class="rounded-full bg-muted/50 px-3 py-1 text-xs font-bold text-muted-foreground"
                            >{{ indicators.sosial.length }} Assets</span
                        >
                    </div>

                    <div class="space-y-1">
                        <div
                            v-for="item in indicators.sosial"
                            :key="item.title"
                            class="group flex cursor-pointer items-center gap-4 rounded-2xl p-4 transition-all hover:bg-muted/40"
                        >
                            <!-- Asset Icon & Info -->
                            <div class="flex min-w-[140px] items-center gap-4 sm:min-w-[200px]">
                                <div
                                    :class="[
                                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl shadow-sm transition-transform group-hover:scale-105',
                                        item.iconColor,
                                    ]"
                                >
                                    <component :is="item.icon" class="h-6 w-6" />
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-base leading-tight font-black tracking-tight transition-colors group-hover:text-primary">
                                        {{ item.ticker }}
                                    </div>
                                    <div class="truncate text-xs font-medium text-muted-foreground">
                                        {{ item.title }}
                                    </div>
                                </div>
                            </div>

                            <!-- Sparkline (using Shadcn Chart base) -->
                            <div class="hidden h-14 min-w-[100px] flex-1 px-4 sm:block">
                                <ChartContainer
                                    :config="{
                                        value: {
                                            color: item.trend.isUp ? '#10b981' : '#f59e0b',
                                        },
                                    }"
                                    class="h-full w-full opacity-60 transition-opacity group-hover:opacity-100"
                                >
                                    <VisXYContainer :data="item.sparkline" :height="56" :margin="{ top: 5, bottom: 5, left: 5, right: 5 }">
                                        <VisLine :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :stroke-width="2.5" />
                                        <VisArea :x="(d: any) => d.x" :y="(d: any) => d.y" color="var(--color-value)" :opacity="0.1" />
                                    </VisXYContainer>
                                </ChartContainer>
                            </div>

                            <!-- Value & Change -->
                            <div class="ml-auto min-w-[80px] text-right">
                                <div class="text-lg leading-tight font-black tracking-tight tabular-nums">
                                    {{ item.value }}<span class="ml-0.5 text-[10px] text-muted-foreground">{{ item.unit }}</span>
                                </div>
                                <div
                                    :class="[
                                        'mt-0.5 flex items-center justify-end gap-0.5 text-xs font-bold italic',
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
                </div>
            </div>

            <!-- Footer / CTA -->
            <div class="mt-20 flex flex-col items-center gap-6 text-center">
                <div class="group relative inline-flex">
                    <div
                        class="absolute -inset-px rounded-full bg-linear-to-r from-blue-500 to-emerald-500 opacity-25 blur-lg transition-all duration-1000 group-hover:-inset-1 group-hover:opacity-50 group-hover:duration-200"
                    ></div>
                    <button
                        class="relative flex items-center gap-3 rounded-full bg-foreground px-10 py-4 text-sm font-bold text-background transition-all hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Eksplorasi Portal Data Strategis
                        <ArrowUpRight class="h-4 w-4" />
                    </button>
                </div>
                <p class="text-sm font-medium text-muted-foreground/80">
                    Sumber: <span class="text-foreground">Satu Data KSB</span> • Diperbarui terakhir: 28 Maret 2026
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: Fine-tune unovis container visibility */
:deep([data-vis-xy-container]) {
    overflow: visible !important;
}
</style>

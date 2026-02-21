<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface ProductPrice {
    regular: number;
    sale?: number;
    currency: string;
}

interface Product {
    name: string;
    image: {
        src: string;
        alt: string;
    };
    link: string;
    description: string;
    price: ProductPrice;
    badge?: {
        text: string;
        color?: string;
    };
}

interface Props {
    className?: string;
}

const props = defineProps<Props>();

const products: Product[] = [
    {
        name: "Vexon CoreStep '08 LX",
        image: {
            src: 'https://deifkwefumgah.cloudfront.net/shadcnblocks/block/ecommerce/clothes/joshua-diaz-ETNoDLl8yFE-unsplash-1.jpg',
            alt: 'Vexon CoreStep shoes',
        },
        link: '#',
        description: 'Everyday comfort meets bold tri-color style in this performance-driven design.',
        price: {
            regular: 499,
            sale: 399,
            currency: 'USD',
        },
        badge: {
            text: 'Selling fast!',
            color: 'oklch(57.7% 0.245 27.325)',
        },
    },
    {
        name: 'Urban Chill Jacket',
        image: {
            src: 'https://deifkwefumgah.cloudfront.net/shadcnblocks/block/ecommerce/clothes/pexels-cottonbro-6764040-2.jpg',
            alt: 'Urban Chill Jacket',
        },
        link: '#',
        description: 'A denim puffer with tonal blues, perfect for layering across seasons.',
        price: {
            regular: 180,
            currency: 'USD',
        },
    },
    {
        name: 'Maison Liora Bag',
        image: {
            src: 'https://deifkwefumgah.cloudfront.net/shadcnblocks/block/ecommerce/clothes/Woman-with-Tote-Bag-1.png',
            alt: 'Maison Liora Bag',
        },
        link: '#',
        description: 'A refined bag that easily switches from shoulder to crossbody or top-handle.',
        price: {
            regular: 420,
            currency: 'USD',
        },
        badge: {
            text: 'New',
        },
    },
];

function formatPrice(price: number | undefined, currency: string): string {
    if (price == null) {
        return '';
    }

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(price);
}
</script>

<template>
    <section :class="cn('py-32', props.className)">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 justify-items-center gap-6 md:grid-cols-2 xl:grid-cols-3">
                <a
                    v-for="(product, index) in products"
                    :key="`product-list-1-card-${index}`"
                    :href="product.link"
                    class="block h-full w-full max-w-md transition-opacity hover:opacity-80"
                >
                    <Card class="h-full overflow-hidden p-0">
                        <CardHeader class="relative block p-0">
                            <div class="aspect-[1.268115942] overflow-hidden">
                                <img :src="product.image.src" :alt="product.image.alt" class="block size-full object-cover object-center" />
                            </div>

                            <Badge v-if="product.badge" :style="{ backgroundColor: product.badge.color }" class="absolute start-4 top-4">
                                {{ product.badge.text }}
                            </Badge>
                        </CardHeader>

                        <CardContent class="flex h-full flex-col gap-4 pb-6">
                            <CardTitle class="text-xl font-semibold">{{ product.name }}</CardTitle>
                            <CardDescription class="font-medium text-muted-foreground">
                                {{ product.description }}
                            </CardDescription>

                            <div class="mt-auto text-lg font-semibold">
                                <template v-if="product.price.sale != null">
                                    <span class="me-2">{{ formatPrice(product.price.sale, product.price.currency) }}</span>
                                    <span class="text-muted-foreground line-through">{{
                                        formatPrice(product.price.regular, product.price.currency)
                                    }}</span>
                                </template>
                                <template v-else>
                                    <span>{{ formatPrice(product.price.regular, product.price.currency) }}</span>
                                </template>
                            </div>
                        </CardContent>
                    </Card>
                </a>
            </div>
        </div>
    </section>
</template>

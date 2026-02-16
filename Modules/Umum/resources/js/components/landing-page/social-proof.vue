<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent } from '@/components/ui/card';

interface Testimonial {
    body: string;
    author: {
        name: string;
        handle: string;
        imageUrl: string;
    };
}

const testimonials: Testimonial[] = [
    {
        body: "The AI-powered analytics dashboard revolutionized our decision-making process. It's intuitive, fast, and provides insights we never knew we needed. Absolutely game-changing for our business!",
        author: {
            name: 'Emily Chen',
            handle: 'emilychen_tech',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-1.svg',
        },
    },
    {
        body: "I was skeptical about AI tools, but this platform changed my mind. The natural language processing capabilities are remarkable. It's like having a brilliant assistant always at your fingertips.",
        author: {
            name: 'Marcus Johnson',
            handle: 'marcusj_ai',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-2.svg',
        },
    },
    {
        body: "As a small business owner, I thought advanced AI was out of reach. This solution proved me wrong. It's affordable, scalable, and has dramatically improved our customer service efficiency.",
        author: {
            name: 'Sarah Thompson',
            handle: 'sarahT_biz',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-3.svg',
        },
    },
    {
        body: "The ethical approach to AI development here is commendable. Not only is the technology cutting-edge, but it's also designed with real consideration for societal impact. Impressive work!",
        author: {
            name: 'Dr. Aisha Patel',
            handle: 'dr_aisha_ethics',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-4.svg',
        },
    },
    {
        body: "I've worked with many AI platforms, but the speed and accuracy of this one are unparalleled. It's reduced our data processing time by 70% and improved accuracy by 25%. Truly exceptional.",
        author: {
            name: 'Robert Yamamoto',
            handle: 'robyam_data',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-5.svg',
        },
    },
    {
        body: "The customer support team behind this AI product deserves as much praise as the tech itself. They're knowledgeable, responsive, and genuinely care about user success. A rare find in tech!",
        author: {
            name: 'Olivia Foster',
            handle: 'livfoster_cx',
            imageUrl: 'https://ui.convertfa.st/avatars/avatar-6.svg',
        },
    },
];
</script>

<template>
    <div class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="my-8 text-center">
                <div class="text-sm font-semibold tracking-wide uppercase">Social Proof</div>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Hear from Our Customers</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="testimonial in testimonials" :key="testimonial.author.handle" class="my-4">
                    <CardContent class="pt-6">
                        <blockquote>
                            <p>"{{ testimonial.body }}"</p>
                        </blockquote>
                        <div class="mt-6 flex items-center gap-x-4">
                            <Avatar>
                                <AvatarImage :src="testimonial.author.imageUrl" :alt="testimonial.author.name" />
                                <AvatarFallback>{{ testimonial.author.name.charAt(0) }}</AvatarFallback>
                            </Avatar>
                            <div>
                                <div class="font-semibold">{{ testimonial.author.name }}</div>
                                <div class="text-zinc-600">@{{ testimonial.author.handle }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

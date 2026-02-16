<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { computed } from 'vue';

interface FAQItem {
    question: string;
    answer: string;
}

const defaultItems: FAQItem[] = [
    {
        question: 'Why use ConvertFast to build landing pages?',
        answer: 'ConvertFast provides a comprehensive set of tools and pre-built components specifically designed for creating high-converting landing pages. Our intuitive interface, customizable templates, and optimized code export save you time and effort while ensuring professional results.',
    },
    {
        question: 'Is ConvertFast open source?',
        answer: "Yes, ConvertFast is an open-source project. This means you can use, modify, and contribute to the codebase. We believe in transparency and community-driven development, which helps us continually improve and adapt to developers' needs.",
    },
    {
        question: 'How does ConvertFast compare to other landing page builders?',
        answer: "ConvertFast stands out by focusing on developers' needs. Unlike traditional drag-and-drop builders, we provide clean, exportable code that integrates seamlessly with your existing projects. Our components are based on popular libraries like shadcn, ensuring high-quality, customizable UI elements.",
    },
    {
        question: 'Can I use ConvertFast with my preferred frontend framework?',
        answer: 'Absolutely! ConvertFast is designed to be framework-agnostic. While our components are primarily React-based, the exported code can be easily adapted to work with other popular frontend frameworks like Vue, Angular, or even vanilla JavaScript.',
    },
    {
        question: 'Is there a learning curve to using ConvertFast?',
        answer: "ConvertFast is designed to be intuitive for developers familiar with modern web development practices. If you're comfortable with React and component-based architecture, you'll find ConvertFast easy to use. We also provide comprehensive documentation and examples to help you get started quickly.",
    },
];

interface Props {
    items?: FAQItem[];
}

const props = defineProps<Props>();
const faqItems = computed<FAQItem[]>(() => props.items ?? defaultItems);
</script>

<template>
    <section class="bg-linear-to-t from-zinc-50 to-white dark:from-zinc-950">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <h2 class="mb-8 text-center text-3xl font-bold tracking-tight text-primary sm:text-4xl">Frequently Asked Questions</h2>
                <Accordion type="single" collapsible class="space-y-4">
                    <AccordionItem v-for="(faq, index) in faqItems" :key="index" :value="`item-${index}`" class="rounded-lg border shadow-sm">
                        <AccordionTrigger class="px-4 py-4">
                            <span class="text-left font-medium">{{ faq.question }}</span>
                        </AccordionTrigger>
                        <AccordionContent class="px-4 pt-2 pb-4 text-gray-600">
                            {{ faq.answer }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </section>
</template>

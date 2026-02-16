<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { cn } from '@/lib/utils'

interface Plan {
  name: string
  id: string
  href: string
  price: {
    monthly: string
    annually: string
  }
  description: string
  actionTitle: string
  features: string[]
  popular: boolean
}

const plans: Plan[] = [
  {
    name: 'Starter',
    id: 'plan-starter',
    href: '#',
    price: { monthly: '$19', annually: '$190' },
    description: 'Perfect for individual developers or small projects.',
    actionTitle: 'Get started',
    features: [
      '5 landing page templates',
      'Basic customization options',
      'Export to HTML/CSS',
      '48-hour email support',
    ],
    popular: false,
  },
  {
    name: 'Pro',
    id: 'plan-pro',
    href: '#',
    price: { monthly: '$49', annually: '$490' },
    description: 'Ideal for growing businesses and agencies.',
    actionTitle: 'Get started',
    features: [
      '20 landing page templates',
      'Advanced customization',
      'Export to React/Vue/Angular',
      '24-hour email support',
      'A/B testing tools',
    ],
    popular: true,
  },
  {
    name: 'Enterprise',
    id: 'plan-enterprise',
    href: '#',
    price: { monthly: '$99', annually: '$990' },
    description: 'For large teams and high-volume projects.',
    actionTitle: 'Contact sales',
    features: [
      'Unlimited landing page templates',
      'Full customization control',
      'Export to any framework',
      'Priority 24/7 support',
      'Advanced analytics',
      'White-labeling options',
    ],
    popular: false,
  },
]
</script>

<template>
  <div class="py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-4xl sm:text-center">
        <h2 class="text-base font-semibold leading-7 text-muted-foreground">Pricing</h2>
        <p class="mt-2 text-4xl font-bold tracking-tight text-primary sm:text-5xl">
          Choose the perfect plan for your needs
        </p>
      </div>
      <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-muted-foreground sm:text-center">
        ConvertFast offers flexible pricing options to suit developers and teams of all sizes. Start building stunning
        landing pages faster than ever.
      </p>
      <div class="mt-20 flow-root">
        <div class="isolate -mt-16 grid max-w-sm grid-cols-1 gap-y-16 gap-x-4 sm:mx-auto lg:-mx-8 lg:mt-0 lg:max-w-none lg:grid-cols-3 xl:-mx-4">
          <Card
            v-for="plan in plans"
            :key="plan.id"
            :class="cn('flex flex-col', plan.popular ? 'ring-1 ring-primary' : '')"
          >
            <CardHeader>
              <CardTitle :id="plan.id" class="text-base font-semibold leading-7">
                {{ plan.name }}
              </CardTitle>
              <CardDescription class="mt-6 flex items-baseline gap-x-1">
                <span class="text-5xl font-bold tracking-tight text-primary">{{ plan.price.monthly }}</span>
                <span class="text-sm font-semibold leading-6 text-muted-foreground">/month</span>
              </CardDescription>
              <p class="mt-3 text-sm leading-6 text-muted-foreground">{{ plan.price.annually }} billed annually</p>
            </CardHeader>
            <CardContent>
              <p class="mt-10 text-sm font-semibold leading-6 text-primary">{{ plan.description }}</p>
              <ul role="list" class="mt-6 space-y-3 text-sm leading-6 text-muted-foreground">
                <li v-for="feature in plan.features" :key="feature">
                  {{ feature }}
                </li>
              </ul>
            </CardContent>
            <CardFooter class="mt-auto">
              <Button class="w-full" :aria-describedby="plan.id" as-child>
                <a :href="plan.href">{{ plan.actionTitle }}</a>
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>

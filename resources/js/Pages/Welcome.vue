<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    canLogin?: boolean;
    canRegister?: boolean;
}

defineProps<Props>();
</script>

<template>

    <Head title="Accueil" />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Header -->
        <header class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>

            <!-- Navigation -->
            <nav class="relative z-10 flex items-center justify-between p-6 lg:px-8">
                <div class="flex lg:flex-1">
                    <span class="text-2xl font-bold text-white">CollectorsHub</span>
                </div>

                <div v-if="canLogin" class="flex lg:flex-1 lg:justify-end space-x-4">
                    <Link href="/login"
                        class="text-sm font-semibold leading-6 text-white hover:text-blue-300 transition-colors">
                    Login
                    </Link>
                    <Link v-if="canRegister" href="/register"
                        class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                    Register
                    </Link>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative z-10 mx-auto max-w-7xl px-6 pb-24 pt-10 sm:pb-32 lg:flex lg:px-8 lg:py-40">
                <div class="mx-auto max-w-2xl flex-shrink-0 lg:mx-0 lg:max-w-xl lg:pt-8">
                    <h1 class="mt-10 text-4xl font-bold tracking-tight text-white sm:text-6xl">
                        Bienvenue sur
                        <span
                            class="text-gradient bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                            CollectorsHub
                        </span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">
                        Une application moderne construite avec Vue 3, TypeScript, Laravel et Inertia.js.
                        Découvrez une expérience de développement exceptionnelle avec des technologies de pointe.
                    </p>
                    <div class="mt-10 flex items-center gap-x-6">
                        <Link href="/login"
                            class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:scale-105">
                        Commencer
                        </Link>
                        <Link href="/register"
                            class="text-sm font-semibold leading-6 text-white hover:text-blue-300 transition-colors">
                        En savoir plus <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                </div>
            </div>
        </header>
    </div>
</template>

<style scoped>
.text-gradient {
    background: linear-gradient(90deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text;
    background-clip: text;
}
</style>
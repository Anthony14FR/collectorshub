<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface User {
    username: string;
    email: string;
    level: number;
    experience: number;
    cash: number;
    last_login: string | null;
    role: 'user' | 'premium' | 'admin';
    status: 'active' | 'suspended' | 'banned';
    email_verified_at?: string | null;
}

interface Props {
    auth: {
        user: User | null;
    };
}

const props = defineProps<Props>();

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'));
};
</script>

<template>
    <Head title="Collectors Hub - Accueil" />
    
    <div class="min-h-screen bg-gradient-to-b from-red-500 to-red-600 font-pixel">
        <!-- Header with Pokéball pattern -->
        <div class="w-full h-16 bg-white border-b-8 border-black relative">
            <div class="absolute bottom-0 left-0 w-full h-16 flex items-center">
                <div class="w-16 h-16 bg-white rounded-full border-8 border-black absolute left-1/2 transform -translate-x-1/2 translate-y-1/2 z-10">
                    <div class="w-8 h-8 bg-white rounded-full border-4 border-black absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
                </div>
            </div>
        </div>

        <main class="container mx-auto py-12 px-4">
            <!-- User info and auth buttons -->
            <div class="flex justify-end space-x-4 mb-8">
                <template v-if="auth.user">
                    <div class="bg-yellow-300 border-4 border-blue-700 rounded-lg p-3 shadow-[6px_6px_0px_0px_rgba(0,0,0,0.8)] flex items-center space-x-3">
                        <div class="flex flex-col">
                            <span class="text-blue-800 font-bold">{{ auth.user.username }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-700 text-white text-xs rounded">Nv.{{ auth.user.level }}</span>
                                <span class="text-xs">{{ auth.user.cash }} ₽</span>
                            </div>
                        </div>
                        <button
                            @click="logout"
                            class="px-4 py-2 bg-red-600 text-white rounded border-4 border-black hover:bg-red-700 transition-colors transform hover:scale-105 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)]"
                        >
                            Déconnexion
                        </button>
                    </div>
                </template>
                <template v-else>
                    <Link 
                        :href="route('login')"
                        class="px-6 py-2 bg-blue-600 text-white rounded border-4 border-black hover:bg-blue-700 transition-colors transform hover:scale-105 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)]"
                    >
                        Login
                    </Link>
                    <Link 
                        :href="route('register')"
                        class="px-6 py-2 bg-green-600 text-white rounded border-4 border-black hover:bg-green-700 transition-colors transform hover:scale-105 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)]"
                    >
                        Register
                    </Link>
                </template>
            </div>

            <!-- Main title with pixelated styling -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-yellow-300 mb-2 tracking-wide drop-shadow-[4px_4px_0px_rgba(0,0,0,0.8)]">
                    Collectors Hub
                </h1>
                <div class="w-64 h-2 bg-yellow-300 mx-auto rounded-full"></div>
                <p class="text-white mt-4 text-xl">Attrapez-les pas, ils ont peur !</p>
            </div>
            
            <!-- Main content cards with pixelated styling -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg border-8 border-blue-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.8)] transform hover:scale-105 transition-transform">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-500 rounded-full border-4 border-white mr-4"></div>
                        <h2 class="text-2xl font-bold text-blue-700">Découvrez les Pokémon</h2>
                    </div>
                    <p class="text-gray-700">Explorez notre collection complète de Pokémon et commencez votre aventure de dresseur !</p>
                    <div class="mt-4 flex justify-end">
                        <button class="px-4 py-2 bg-red-500 text-white rounded border-4 border-black hover:bg-red-600 transition-colors">
                            Explorer
                        </button>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg border-8 border-blue-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.8)] transform hover:scale-105 transition-transform">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full border-4 border-white mr-4"></div>
                        <h2 class="text-2xl font-bold text-blue-700">Gérez votre Collection</h2>
                    </div>
                    <p class="text-gray-700">Créez votre Pokédex personnalisé et suivez votre progression de collectionneur.</p>
                    <div class="mt-4 flex justify-end">
                        <button class="px-4 py-2 bg-blue-500 text-white rounded border-4 border-black hover:bg-blue-600 transition-colors">
                            Mon Pokédex
                        </button>
                    </div>
                </div>
            </div>

            <!-- Decorative pixel elements -->
            <div class="mt-16 grid grid-cols-5 gap-4">
                <div v-for="i in 5" :key="i" class="h-8 w-8 bg-yellow-300 rounded-lg border-4 border-black transform rotate-45"></div>
            </div>
        </main>

        <!-- Footer with pixel grass -->
        <div class="w-full h-16 bg-green-600 border-t-8 border-green-800 relative">
            <div class="absolute top-0 left-0 w-full flex">
                <div v-for="i in 20" :key="i" class="w-8 h-8 bg-green-500 rounded-t-full border-t-4 border-l-4 border-r-4 border-green-700"></div>
            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

.font-pixel {
    font-family: 'Press Start 2P', cursive;
}

/* Pixelated border effect */
.border-pixelated {
    box-shadow: 
        4px 0 0 0 #000,
        -4px 0 0 0 #000,
        0 4px 0 0 #000,
        0 -4px 0 0 #000;
}
</style>
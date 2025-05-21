<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type PageProps } from '@/types/page';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps<{
    auth: PageProps['auth']
}>();

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'));
};
</script>

<template>
    <Head title="Collectors Hub - Accueil" />
    
    <MainLayout :auth="auth">
        <div class="bg-gradient-to-b from-red-500 to-red-600 py-12 font-pixel">
            <!-- Main title with pixelated styling -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-yellow-300 mb-2 tracking-wide drop-shadow-[4px_4px_0px_rgba(0,0,0,0.8)]">
                    Collectors Hub
                </h1>
                <div class="w-64 h-2 bg-yellow-300 mx-auto rounded-full"></div>
                <p class="text-white mt-4 text-xl">Attrapez-les pas, ils ont peur !</p>
            </div>
            
            <!-- Main content cards with pixelated styling -->
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-6 rounded-lg border-8 border-blue-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.8)] transform hover:scale-105 transition-transform">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-red-500 rounded-full border-4 border-white mr-4"></div>
                            <h2 class="text-2xl font-bold text-blue-700">Découvrez les Pokémon</h2>
                        </div>
                        <p class="text-gray-700">Explorez notre collection complète de Pokémon et commencez votre aventure de dresseur !</p>
                        <div class="mt-4 flex justify-end">
                            <Link :href="route('pokedex.public')" class="px-4 py-2 bg-red-500 text-white rounded border-4 border-black hover:bg-red-600 transition-colors">
                                Explorer
                            </Link>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border-8 border-blue-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,0.8)] transform hover:scale-105 transition-transform">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full border-4 border-white mr-4"></div>
                            <h2 class="text-2xl font-bold text-blue-700">Gérez votre Collection</h2>
                        </div>
                        <p class="text-gray-700">Créez votre Pokédex personnalisé et suivez votre progression de collectionneur.</p>
                        <div class="mt-4 flex justify-end">
                            <Link v-if="auth && auth.user" :href="route('me.pokedex')" class="px-4 py-2 bg-blue-500 text-white rounded border-4 border-black hover:bg-blue-600 transition-colors">
                                Mon Pokédex
                            </Link>
                            <Link v-else :href="route('login')" class="px-4 py-2 bg-blue-500 text-white rounded border-4 border-black hover:bg-blue-600 transition-colors">
                                Connexion
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Decorative pixel elements -->
                <div class="mt-16 grid grid-cols-5 gap-4">
                    <div v-for="i in 5" :key="i" class="h-8 w-8 bg-yellow-300 rounded-lg border-4 border-black transform rotate-45"></div>
                </div>
            </div>
            
            <!-- Footer with pixel grass -->
            <div class="w-full h-16 bg-green-600 border-t-8 border-green-800 relative mt-12">
                <div class="absolute top-0 left-0 w-full flex">
                    <div v-for="i in 20" :key="i" class="w-8 h-8 bg-green-500 rounded-t-full border-t-4 border-l-4 border-r-4 border-green-700"></div>
                </div>
            </div>
        </div>
    </MainLayout>
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
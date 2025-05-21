<script setup lang="ts">
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import { type PageProps } from '@/types/page';

// Le props auth est optionnel car il ne sera pas présent pour les pages non-authentifiées
interface Props {
    auth?: PageProps['auth'];
}

const props = defineProps<Props>();

// Vérifier si l'utilisateur est connecté
const isAuthenticated = computed(() => {
    return props.auth && props.auth.user;
});

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Header principal -->
            <nav class="border-b border-gray-100 bg-white shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Logo et titre à gauche -->
                        <div class="flex items-center">
                            <Link :href="isAuthenticated ? route('dashboard') : '/'" class="flex items-center">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 mr-3" />
                                <span class="text-xl font-bold text-gray-800">CollectorsHub</span>
                            </Link>
                        </div>

                        <!-- Si connecté: Stats utilisateur au centre -->
                        <div v-if="isAuthenticated" class="flex items-center space-x-8">
                            <div class="flex items-center space-x-4 bg-gray-50 px-6 py-2 rounded-lg shadow-inner">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Niveau</p>
                                    <p class="font-bold text-blue-600">{{ auth.user.level }}</p>
                                </div>
                                <div class="w-48">
                                    <p class="text-xs text-gray-500">Expérience</p>
                                    <div class="h-2 w-full rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-blue-500 transition-all"
                                            :style="{ width: `${(auth.user.experience % 100)}%` }">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">Cash</p>
                                    <p class="font-bold text-green-600">{{ auth.user.cash }} ₽</p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu de navigation à droite -->
                        <div class="flex items-center space-x-4">
                            <!-- Si non connecté: boutons de connexion et inscription -->
                            <template v-if="!isAuthenticated">
                                <Link :href="route('login')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Connexion</Link>
                                <Link :href="route('register')" class="text-sm text-indigo-600 hover:text-indigo-700">Inscription</Link>
                            </template>
                            
                            <!-- Si connecté: nom utilisateur et menu déroulant -->
                            <template v-else>
                                <span class="text-gray-600">{{ auth.user.username }}</span>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center rounded-full bg-gray-50 p-2 transition hover:bg-gray-100">
                                            <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('me.pokedex')" class="flex items-center space-x-2">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 1.5 1.5 0 011.5-1.5c.526 0 .988-.27 1.256-.667a6.012 6.012 0 011.912 2.706C17.454 8.986 17.5 9.48 17.5 10c0 4.142-3.358 7.5-7.5 7.5S2.5 14.142 2.5 10c0-.52.046-1.014.132-1.973z" />
                                            </svg>
                                            <span>Mon Pokédex</span>
                                        </DropdownLink>
                                        
                                        <DropdownLink :href="route('pokedex.public')" class="flex items-center space-x-2">
                                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                                            </svg>
                                            <span>Pokédex Complet</span>
                                        </DropdownLink>
                                        
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center space-x-2">
                                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                            </svg>
                                            <span>Profil</span>
                                        </DropdownLink>

                                        <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 w-full">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" />
                                            </svg>
                                            <span>Déconnexion</span>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </template>

                            <!-- Menu pour utilisateurs non connectés -->
                            <Dropdown v-if="!isAuthenticated" align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center rounded-full bg-gray-50 p-2 transition hover:bg-gray-100">
                                        <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('pokedex.public')" class="flex items-center space-x-2">
                                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                                        </svg>
                                        <span>Pokédex Complet</span>
                                    </DropdownLink>
                                    
                                    <DropdownLink :href="route('login')" class="flex items-center space-x-2">
                                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" />
                                        </svg>
                                        <span>Connexion</span>
                                    </DropdownLink>
                                    
                                    <DropdownLink :href="route('register')" class="flex items-center space-x-2">
                                        <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                        </svg>
                                        <span>Inscription</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template> 
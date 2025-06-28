<script setup lang="ts">
import type { User } from '@/types/user';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    user: User;
}>();

const getRoleColor = (role: string) => {
    switch (role) {
        case 'admin': return 'bg-red-100 text-red-800';
        case 'premium': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'suspended': return 'bg-orange-100 text-orange-800';
        case 'banned': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>

    <Head :title="`Utilisateur: ${user.username}`" />

    <div class="min-h-screen bg-gray-100 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Profil de {{ user.username }}
                        </h1>
                        <div class="flex items-center space-x-3">
                            <Link :href="`/admin/users/${user.id}/edit`"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Modifier
                            </Link>
                            <Link href="/admin/users" class="text-gray-600 hover:text-gray-900 transition-colors">
                            ← Retour à la liste
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom d'utilisateur</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.username }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="getRoleColor(user.role)">
                                        {{ user.role }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="getStatusColor(user.status)">
                                        {{ user.status }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Niveau</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.level }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Expérience</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.experience.toLocaleString() }} XP</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cash</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.cash.toLocaleString() }} ₽</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email vérifié</dt>
                                <dd class="mt-1">
                                    <span v-if="user.email_verified_at"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Vérifié
                                    </span>
                                    <span v-else
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Non vérifié
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Inscrit le</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(user.created_at).toLocaleDateString('fr-FR') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière connexion</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ user.last_login ? new Date(user.last_login).toLocaleDateString('fr-FR') :
                                        'Jamais' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Stats -->
                <div class="space-y-6">
                    <!-- Pokédex Stats -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pokédex</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Total capturés</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ user.pokedex?.length || 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Shiny</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{user.pokedex?.filter(p => p.is_shiny).length || 0}}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">En équipe</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{user.pokedex?.filter(p => p.is_in_team).length || 0}}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Stats -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventaire</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Objets différents</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ user.inventory?.length || 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Total objets</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{user.inventory?.reduce((total, item) => total + item.quantity, 0) || 0}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
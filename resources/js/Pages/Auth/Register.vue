<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { nextTick, reactive, ref } from 'vue';

const form = reactive({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    processing: false,
    errors: {} as Record<string, string>,
});

const nameInput = ref<HTMLInputElement>();

const submit = () => {
    form.processing = true;
    form.errors = {};

    router.post('/register', {
        username: form.username,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
    }, {
        onFinish: () => {
            form.processing = false;
            form.password = '';
            form.password_confirmation = '';
        },
        onError: (errors: Record<string, string>) => {
            form.processing = false;
            form.errors = errors;
            if (errors.username) {
                nextTick(() => nameInput.value?.focus());
            }
        },
    });
};
</script>

<template>

    <Head title="Inscription" />

    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <Link href="/" class="text-3xl font-bold text-white hover:text-blue-300 transition-colors">
                CollectorsHub
                </Link>
                <h2 class="mt-6 text-2xl font-bold text-white">
                    Créez votre compte
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Ou
                    <Link href="/login" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                    connectez-vous à votre compte existant
                    </Link>
                </p>
            </div>

            <!-- Register Form -->
            <form @submit.prevent="submit"
                class="mt-8 space-y-6 bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                <div class="space-y-4">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                            Nom d'utilisateur
                        </label>
                        <input id="username" ref="nameInput" v-model="form.username" type="text" required
                            autocomplete="username"
                            class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="Votre nom d'utilisateur" />
                        <div v-if="form.errors.username" class="mt-2 text-sm text-red-400">
                            {{ form.errors.username }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            Adresse email
                        </label>
                        <input id="email" v-model="form.email" type="email" required autocomplete="email"
                            class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="votre@email.com" />
                        <div v-if="form.errors.email" class="mt-2 text-sm text-red-400">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            Mot de passe
                        </label>
                        <input id="password" v-model="form.password" type="password" required
                            autocomplete="new-password"
                            class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="••••••••" />
                        <div v-if="form.errors.password" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                            autocomplete="new-password"
                            class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="••••••••" />
                        <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" :disabled="form.processing"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Inscription...
                        </span>
                        <span v-else>S'inscrire</span>
                    </button>
                </div>
            </form>

            <!-- Back to Home -->
            <div class="text-center">
                <Link href="/" class="text-sm text-gray-400 hover:text-white transition-colors">
                ← Retour à l'accueil
                </Link>
            </div>
        </div>
    </div>
</template>
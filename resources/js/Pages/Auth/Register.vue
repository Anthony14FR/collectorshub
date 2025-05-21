<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface RegisterForm {
    name: string;
    username: string;
    email: string;
    password: string;
    password_confirmation: string;
}

const form = useForm<RegisterForm>({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Créer un compte
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                        <input
                            id="username"
                            type="text"
                            v-model="form.username"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                        <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">
                            {{ form.errors.username }}
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        :disabled="form.processing"
                    >
                        S'inscrire
                    </button>
                </div>

                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm text-blue-600 hover:text-blue-500"
                    >
                        Déjà un compte ? Se connecter
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

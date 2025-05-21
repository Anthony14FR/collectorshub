<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}

const form = useForm<LoginForm>({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Connexion
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                            autocomplete="email"
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
                            autocomplete="current-password"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                        />
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        :disabled="form.processing"
                    >
                        Se connecter
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <Link
                        href="/forgot-password"
                        class="text-sm text-blue-600 hover:text-blue-500"
                    >
                        Mot de passe oublié ?
                    </Link>
                    <Link
                        href="/register"
                        class="text-sm text-blue-600 hover:text-blue-500"
                    >
                        Pas encore de compte ?
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

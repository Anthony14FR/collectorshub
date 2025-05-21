<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Mon Équipe</h1>
                    <p class="mb-4 text-green-600">{{ message }}</p>
                    
                    <!-- Affichage des données brutes -->
                    <pre class="bg-gray-100 p-4 rounded-lg overflow-auto">
                        {{ JSON.stringify(pokemon, null, 2) }}
                    </pre>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: String,
    pokemon: Object
});
</script> 
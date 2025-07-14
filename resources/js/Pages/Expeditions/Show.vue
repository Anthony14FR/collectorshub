<template>
  <div class="p-4">
    <h1 class="text-2xl mb-4">{{ expedition.name }}</h1>
    <p class="mb-4">{{ expedition.description }}</p>
    
    <div class="mb-4">
      <h2 class="font-bold mb-2">Exigences:</h2>
      <ul>
        <li v-for="req in expedition.requirements" :key="req.id">
          {{ req.quantity }} {{ req.type }} {{ req.value }}
        </li>
      </ul>
    </div>
    
    <div class="mb-4">
      <h2 class="font-bold mb-2">Pokémons éligibles:</h2>
      <div class="grid grid-cols-4 gap-2">
        <div v-for="pokemon in eligiblePokemons" :key="pokemon.id" 
             class="border p-2 cursor-pointer"
             :class="{ 'bg-blue-200': selectedPokemons.includes(pokemon.id) }"
             @click="togglePokemon(pokemon.id)">
          <p class="text-sm">{{ pokemon.pokemon.name }}</p>
          <p class="text-xs">{{ pokemon.pokemon.rarity }}</p>
          <p class="text-xs">Niveau {{ pokemon.level }}</p>
        </div>
      </div>
    </div>
    
    <div class="mb-4">
      <p>Pokémons sélectionnés: {{ selectedPokemons.length }}</p>
    </div>
    
    <div class="flex gap-2">
      <button @click="startExpedition" 
              :disabled="selectedPokemons.length === 0"
              class="bg-green-500 text-white px-4 py-2 rounded disabled:bg-gray-300">
        Démarrer l'expédition
      </button>
      
      <a href="/expeditions" class="bg-gray-500 text-white px-4 py-2 rounded">
        Retour
      </a>
    </div>
    
    <div v-if="expeditionStatus && expeditionStatus.status === 'in_progress'" class="mt-4 p-4 bg-yellow-100">
      <h3 class="font-bold">Expédition en cours</h3>
      <p>Fin prévue: {{ new Date(expeditionStatus.ends_at).toLocaleString() }}</p>
      <div class="mt-2">
        <h4 class="font-semibold">Pokémons en expédition:</h4>
        <ul>
          <li v-for="pokemon in expeditionStatus.pokemons" :key="pokemon.id">
            {{ pokemon.pokedex.pokemon.name }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  expedition: Object,
  eligiblePokemons: Array,
  expeditionStatus: Object
})

const selectedPokemons = ref([])

const togglePokemon = (pokemonId) => {
  const index = selectedPokemons.value.indexOf(pokemonId)
  if (index > -1) {
    selectedPokemons.value.splice(index, 1)
  } else {
    selectedPokemons.value.push(pokemonId)
  }
}

const startExpedition = () => {
  router.post(`/expeditions/${props.expedition.id}/start`, {
    pokemon_ids: selectedPokemons.value
  })
}
</script> 
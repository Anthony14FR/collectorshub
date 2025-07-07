<template>
  <div class="bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-extrabold text-white mb-8">Mettre un Pokémon en vente</h1>
      
      <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-500 text-white p-4 rounded-md mb-6">
        {{ $page.props.flash.success }}
      </div>
      
      <div v-if="$page.props.errors && $page.props.errors.message" class="bg-red-500 text-white p-4 rounded-md mb-6">
        {{ $page.props.errors.message }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
          <h2 class="text-xl font-bold text-white mb-4">Nouveau Pokémon à vendre</h2>
          
          <div v-if="processing" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
          </div>
          
          <div v-else>
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-300 mb-2">Sélectionner un Pokémon</label>
              <select v-model="form.pokemon_id" @change="updatePriceRange" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Choisir un Pokémon</option>
                <option v-for="pokemon in userPokemons" :key="pokemon.id" :value="pokemon.id" :disabled="pokemon.is_in_team">
                  {{ pokemon.name }} (Niv. {{ pokemon.level }})
                  {{ pokemon.is_shiny ? '✨' : '' }}
                  {{ pokemon.is_in_team ? '- Dans l\'équipe' : '' }}
                </option>
              </select>
            </div>
            
            <div v-if="selectedPokemon" class="bg-gray-700 rounded-lg p-4 mb-6">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold text-white">{{ selectedPokemon.name }}</h3>
                <span :class="getRarityClass(selectedPokemon.rarity)" class="px-2 py-1 rounded text-xs font-bold">
                  {{ formatRarity(selectedPokemon.rarity) }}
                </span>
              </div>
              
              <div class="flex justify-between mb-4">
                <span class="text-gray-300">Niveau {{ selectedPokemon.level }}</span>
                <span v-if="selectedPokemon.is_shiny" class="text-yellow-400 font-bold">✨ Shiny</span>
              </div>
              
              <div class="flex flex-wrap gap-1 mb-3">
                <span 
                  v-for="(type, index) in getTypes(selectedPokemon.types)" 
                  :key="index"
                  class="px-2 py-1 rounded text-xs font-bold"
                  :class="getTypeClass(type)"
                >
                  {{ formatType(type) }}
                </span>
              </div>
              
              <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="bg-gray-600 rounded p-2">
                  <span class="text-gray-400 text-xs">HP</span>
                  <p class="text-white font-bold">{{ selectedPokemon.hp }}</p>
                </div>
                <div class="bg-gray-600 rounded p-2">
                  <span class="text-gray-400 text-xs">Attaque</span>
                  <p class="text-white font-bold">{{ selectedPokemon.attack }}</p>
                </div>
                <div class="bg-gray-600 rounded p-2">
                  <span class="text-gray-400 text-xs">Défense</span>
                  <p class="text-white font-bold">{{ selectedPokemon.defense }}</p>
                </div>
                <div class="bg-gray-600 rounded p-2">
                  <span class="text-gray-400 text-xs">Vitesse</span>
                  <p class="text-white font-bold">{{ selectedPokemon.speed }}</p>
                </div>
              </div>
            </div>
            
            <div v-if="form.pokemon_id" class="mb-6">
              <label class="block text-sm font-medium text-gray-300 mb-2">Prix</label>
              <div class="flex items-center">
                <input 
                  type="number" 
                  v-model="form.price" 
                  class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :min="priceRange.min"
                  :max="priceRange.max"
                  placeholder="Prix de vente"
                >
                <span class="ml-2 text-yellow-400 font-bold">$</span>
              </div>
              <p class="text-sm text-gray-400 mt-1">
                Prix minimum: {{ formatPrice(priceRange.min) }} - Prix maximum: {{ formatPrice(priceRange.max) }}
              </p>
            </div>
            
            <div class="flex justify-end">
              <button 
                @click="listPokemon" 
                :disabled="!canSubmit || processing" 
                :class="[
                  'font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500',
                  canSubmit && !processing ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-600 text-gray-400 cursor-not-allowed'
                ]"
              >
                {{ processing ? 'En cours...' : 'Mettre en vente' }}
              </button>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
          <h2 class="text-xl font-bold text-white mb-4">Mes Pokémons en vente</h2>
          
          <div v-if="myListings.length === 0" class="text-center py-8">
            <p class="text-gray-300">Vous n'avez aucun Pokémon en vente actuellement.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="listing in myListings" :key="listing.id" class="bg-gray-700 rounded-lg p-4 flex justify-between items-center">
              <div class="flex items-center">
                <div class="mr-4">
                  <h3 class="text-white font-bold">{{ listing.pokemon.name }}</h3>
                  <div class="flex items-center space-x-2 text-sm text-gray-300">
                    <span>Niv. {{ listing.pokemon.level }}</span>
                    <span v-if="listing.pokemon.is_shiny" class="text-yellow-400">✨ Shiny</span>
                    <span :class="getRarityClass(listing.pokemon.rarity)" class="px-2 py-0.5 rounded text-xs">
                      {{ formatRarity(listing.pokemon.rarity) }}
                    </span>
                  </div>
                </div>
                <div class="text-yellow-400 font-bold">{{ formatPrice(listing.price) }}</div>
              </div>
              <button 
                @click="cancelListing(listing.id)" 
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
              >
                Retirer
              </button>
            </div>
          </div>
          
          <div class="mt-6 text-center">
            <a href="/marketplace" class="text-indigo-400 hover:text-indigo-300">
              Retour à la marketplace
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  userPokemons: Array,
  myListings: Array
});

const page = usePage();
const userPokemons = ref(props.userPokemons || []);
const myListings = ref(props.myListings || []);
const selectedPokemon = ref(null);
const priceRange = ref({ min: 10, max: 1000000 });
const processing = ref(false);

const form = useForm({
  pokemon_id: '',
  price: ''
});

const canSubmit = computed(() => {
  return form.pokemon_id && 
         form.price >= priceRange.value.min && 
         form.price <= priceRange.value.max;
});

const getRarityClass = (rarity) => {
  const classes = {
    normal: 'bg-gray-600 text-white',
    rare: 'bg-blue-600 text-white',
    epic: 'bg-purple-600 text-white',
    legendary: 'bg-yellow-600 text-black'
  };
  return classes[rarity] || classes.normal;
};

const formatRarity = (rarity) => {
  const rarityLabels = {
    normal: 'Normal',
    rare: 'Rare',
    epic: 'Épique',
    legendary: 'Légendaire'
  };
  return rarityLabels[rarity] || rarity;
};

const getTypeClass = (type) => {
  const classes = {
    normal: 'bg-gray-400 text-gray-800',
    fire: 'bg-red-500 text-white',
    water: 'bg-blue-500 text-white',
    electric: 'bg-yellow-400 text-gray-800',
    grass: 'bg-green-500 text-white',
    ice: 'bg-blue-200 text-gray-800',
    fighting: 'bg-red-700 text-white',
    poison: 'bg-purple-500 text-white',
    ground: 'bg-yellow-600 text-white',
    flying: 'bg-indigo-300 text-gray-800',
    psychic: 'bg-pink-500 text-white',
    bug: 'bg-green-600 text-white',
    rock: 'bg-yellow-700 text-white',
    ghost: 'bg-purple-700 text-white',
    dragon: 'bg-indigo-600 text-white',
    dark: 'bg-gray-800 text-white',
    steel: 'bg-gray-500 text-white',
    fairy: 'bg-pink-300 text-gray-800'
  };
  return classes[type] || 'bg-gray-400 text-gray-800';
};

const formatType = (type) => {
  const typeLabels = {
    normal: 'Normal',
    fire: 'Feu',
    water: 'Eau',
    electric: 'Électrique',
    grass: 'Plante',
    ice: 'Glace',
    fighting: 'Combat',
    poison: 'Poison',
    ground: 'Sol',
    flying: 'Vol',
    psychic: 'Psy',
    bug: 'Insecte',
    rock: 'Roche',
    ghost: 'Spectre',
    dragon: 'Dragon',
    dark: 'Ténèbres',
    steel: 'Acier',
    fairy: 'Fée'
  };
  return typeLabels[type] || type;
};

const getTypes = (types) => {
  if (!types) return [];
  if (typeof types === 'string') {
    try {
      return JSON.parse(types);
    } catch (e) {
      return [];
    }
  }
  if (Array.isArray(types)) {
    return types.map(type => typeof type === 'object' && type.name ? type.name : type);
  }
  return [];
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' $';
};

const updatePriceRange = () => {
  if (!form.pokemon_id) {
    selectedPokemon.value = null;
    return;
  }
  
  const pokemon = userPokemons.value.find(p => p.id == form.pokemon_id);
  selectedPokemon.value = pokemon;
  
  if (pokemon) {
    const ranges = {
      normal: { min: 10, max: 1000000 },
      rare: { min: 100, max: 1000000 },
      epic: { min: 1000, max: 1000000 },
      legendary: { min: 10000, max: 1000000 }
    };
    
    priceRange.value = ranges[pokemon.rarity] || ranges.normal;
    
    if (!form.price || form.price < priceRange.value.min) {
      form.price = priceRange.value.min;
    } else if (form.price > priceRange.value.max) {
      form.price = priceRange.value.max;
    }
  }
};

const listPokemon = () => {
  if (!canSubmit.value) return;
  
  processing.value = true;
  
  form.post('/marketplace/sell', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      selectedPokemon.value = null;
      processing.value = false;
      setTimeout(() => {
        router.reload();
      }, 1000);
    },
    onError: () => {
      processing.value = false;
    }
  });
};

const cancelListing = (listingId) => {
  if (confirm('Êtes-vous sûr de vouloir retirer cette annonce ?')) {
    processing.value = true;
    
    router.post(`/marketplace/cancel/${listingId}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(listing => listing.id !== listingId);
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      }
    });
  }
};
</script> 
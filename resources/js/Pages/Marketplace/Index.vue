<template>
  <div class="bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-extrabold text-white mb-8">Marketplace</h1>
      
      <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-500 text-white p-4 rounded-md mb-6">
        {{ $page.props.flash.success }}
      </div>
      
      <div v-if="$page.props.errors && $page.props.errors.message" class="bg-red-500 text-white p-4 rounded-md mb-6">
        {{ $page.props.errors.message }}
      </div>
      
      <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-white">Filtres</h2>
          <div class="flex space-x-4">
            <button 
              @click="showMyListings = !showMyListings" 
              class="px-4 py-2 rounded-md font-medium"
              :class="showMyListings ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300'"
            >
              {{ showMyListings ? 'Afficher toutes les annonces' : 'Afficher mes annonces' }}
            </button>
            <a 
              href="/marketplace/sell" 
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium"
            >
              Vendre un Pokémon
            </a>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Rareté</label>
            <select v-model="filters.rarity" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Toutes</option>
              <option value="common">Commun</option>
              <option value="uncommon">Peu commun</option>
              <option value="rare">Rare</option>
              <option value="epic">Épique</option>
              <option value="legendary">Légendaire</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Type</label>
            <select v-model="filters.type" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Tous</option>
              <option value="normal">Normal</option>
              <option value="fire">Feu</option>
              <option value="water">Eau</option>
              <option value="electric">Électrique</option>
              <option value="grass">Plante</option>
              <option value="ice">Glace</option>
              <option value="fighting">Combat</option>
              <option value="poison">Poison</option>
              <option value="ground">Sol</option>
              <option value="flying">Vol</option>
              <option value="psychic">Psy</option>
              <option value="bug">Insecte</option>
              <option value="rock">Roche</option>
              <option value="ghost">Spectre</option>
              <option value="dragon">Dragon</option>
              <option value="dark">Ténèbres</option>
              <option value="steel">Acier</option>
              <option value="fairy">Fée</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Shiny</label>
            <select v-model="filters.isShiny" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Tous</option>
              <option value="true">Shiny uniquement</option>
              <option value="false">Normal uniquement</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Prix minimum</label>
            <input type="number" v-model="filters.minPrice" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Prix min">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">Prix maximum</label>
            <input type="number" v-model="filters.maxPrice" class="bg-gray-700 text-white rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Prix max">
          </div>
          <div class="flex items-end">
            <button @click="applyFilters" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
              Appliquer les filtres
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
      </div>
      
      <div v-if="showMyListings && displayedListings.length > 0" class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Mes annonces</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="listing in displayedListings" :key="listing.id" class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 border-indigo-500">
            <div class="p-4">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold text-white">{{ listing.pokemon.name }}</h3>
                <span :class="getRarityClass(listing.pokemon.rarity)" class="px-2 py-1 rounded text-xs font-bold">
                  {{ listing.pokemon.rarity }}
                </span>
              </div>
              
              <div class="flex justify-between mb-4">
                <span class="text-gray-300">Niveau {{ listing.pokemon.level }}</span>
                <span v-if="listing.pokemon.is_shiny" class="text-yellow-400 font-bold">✨ Shiny</span>
              </div>
              
              <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">HP</span>
                  <p class="text-white font-bold">{{ listing.pokemon.hp }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Attaque</span>
                  <p class="text-white font-bold">{{ listing.pokemon.attack }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Défense</span>
                  <p class="text-white font-bold">{{ listing.pokemon.defense }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Vitesse</span>
                  <p class="text-white font-bold">{{ listing.pokemon.speed }}</p>
                </div>
              </div>
              
              <div class="flex justify-between items-center mb-4">
                <div class="text-gray-300">
                  <span class="text-sm">Vendeur:</span>
                  <span class="font-bold ml-1">{{ listing.seller.username }} (Vous)</span>
                </div>
                <div class="text-yellow-400 font-bold text-xl">{{ formatPrice(listing.price) }}</div>
              </div>
              
              <button @click="cancelListing(listing.id)" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                Retirer de la vente
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="!showMyListings && displayedListings.length > 0">
        <h2 class="text-2xl font-bold text-white mb-4">Pokémons à vendre</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="listing in displayedListings" :key="listing.id" class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-4">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold text-white">{{ listing.pokemon.name }}</h3>
                <span :class="getRarityClass(listing.pokemon.rarity)" class="px-2 py-1 rounded text-xs font-bold">
                  {{ listing.pokemon.rarity }}
                </span>
              </div>
              
              <div class="flex justify-between mb-4">
                <span class="text-gray-300">Niveau {{ listing.pokemon.level }}</span>
                <span v-if="listing.pokemon.is_shiny" class="text-yellow-400 font-bold">✨ Shiny</span>
              </div>
              
              <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">HP</span>
                  <p class="text-white font-bold">{{ listing.pokemon.hp }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Attaque</span>
                  <p class="text-white font-bold">{{ listing.pokemon.attack }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Défense</span>
                  <p class="text-white font-bold">{{ listing.pokemon.defense }}</p>
                </div>
                <div class="bg-gray-700 rounded p-2">
                  <span class="text-gray-400 text-xs">Vitesse</span>
                  <p class="text-white font-bold">{{ listing.pokemon.speed }}</p>
                </div>
              </div>
              
              <div class="flex justify-between items-center mb-4">
                <div class="text-gray-300">
                  <span class="text-sm">Vendeur:</span>
                  <span class="font-bold ml-1">{{ listing.seller.username }}</span>
                </div>
                <div class="text-yellow-400 font-bold text-xl">{{ formatPrice(listing.price) }}</div>
              </div>
              
              <button @click="buyPokemon(listing.id)" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                Acheter
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="displayedListings.length === 0" class="bg-gray-800 rounded-lg shadow-lg p-6 text-center">
        <p class="text-gray-300 text-lg">
          {{ showMyListings ? 'Vous n\'avez aucun Pokémon en vente' : 'Aucune annonce disponible' }}
        </p>
        <a v-if="showMyListings" href="/marketplace/sell" class="inline-block mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium">
          Mettre un Pokémon en vente
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  myListings: Array,
  otherListings: Array
});

const page = usePage();
const loading = ref(false);
const showMyListings = ref(false);
const myListings = ref(props.myListings || []);
const otherListings = ref(props.otherListings || []);
const filters = ref({
  rarity: '',
  type: '',
  isShiny: '',
  minPrice: '',
  maxPrice: ''
});

const displayedListings = computed(() => {
  return showMyListings.value ? myListings.value : otherListings.value;
});

const getRarityClass = (rarity) => {
  const classes = {
    common: 'bg-gray-600 text-white',
    uncommon: 'bg-green-600 text-white',
    rare: 'bg-blue-600 text-white',
    epic: 'bg-purple-600 text-white',
    legendary: 'bg-yellow-600 text-black'
  };
  return classes[rarity] || classes.common;
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' $';
};

const applyFilters = async () => {
  loading.value = true;
  
  try {
    const params = {};
    
    if (showMyListings.value) params.myListings = 'true';
    if (filters.value.rarity) params.rarity = filters.value.rarity;
    if (filters.value.type) params.type = filters.value.type;
    if (filters.value.isShiny) params.isShiny = filters.value.isShiny;
    if (filters.value.minPrice) params.minPrice = filters.value.minPrice;
    if (filters.value.maxPrice) params.maxPrice = filters.value.maxPrice;
    
    const response = await fetch(`/marketplace/listings?${new URLSearchParams(params)}`);
    const data = await response.json();
    
    if (showMyListings.value) {
      myListings.value = data;
    } else {
      otherListings.value = data;
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des annonces:', error);
  } finally {
    loading.value = false;
  }
};

const buyPokemon = (listingId) => {
  if (confirm('Êtes-vous sûr de vouloir acheter ce Pokémon ?')) {
    loading.value = true;
    
    router.post(`/marketplace/buy/${listingId}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    });
  }
};

const cancelListing = (listingId) => {
  if (confirm('Êtes-vous sûr de vouloir retirer cette annonce ?')) {
    loading.value = true;
    
    router.post(`/marketplace/cancel/${listingId}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(listing => listing.id !== listingId);
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    });
  }
};

onMounted(() => {
  if (myListings.value.length === 0 && otherListings.value.length === 0) {
    applyFilters();
  }
});
</script> 
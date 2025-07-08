<template>
  <Head title="Vendre un Pokémon" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    
    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <!-- Header -->
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏷️ VENDRE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            METTRE EN VENTE
          </p>
        </div>
      </div>

      <!-- Flash Messages -->
      <div v-if="($page.props.flash as any)?.success" class="mx-4 mb-4">
        <Alert type="success" :message="($page.props.flash as any).success" />
      </div>
      <div v-if="($page.props.errors as any)?.message" class="mx-4 mb-4">
        <Alert type="error" :message="($page.props.errors as any).message" />
      </div>

      <!-- Left Side - New Listing -->
      <div class="absolute left-4 top-20 w-72">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">➕</span>
              NOUVELLE ANNONCE
            </h3>
          </div>
          
          <div class="p-3">
            <!-- Add New Card -->
            <div v-if="!selectedPokemon" 
                 @click="showCreateModal = true"
                 class="bg-base-200/30 backdrop-blur-sm rounded-lg p-8 border-2 border-dashed border-base-300/50 hover:border-primary/50 transition-all duration-200 cursor-pointer group text-center">
              <div class="text-6xl text-base-content/30 group-hover:text-primary/50 transition-colors duration-200 mb-2">+</div>
              <p class="text-sm text-base-content/70 group-hover:text-base-content transition-colors duration-200">
                Cliquer pour ajouter
              </p>
            </div>
            
            <!-- Selected Pokemon Form -->
            <div v-else class="space-y-4">
              <div class="bg-base-200/30 backdrop-blur-sm rounded-lg p-3 border border-base-300/20">
                <div class="flex items-center gap-3 mb-3">
                  <img
                    :src="`/images/pokemon-gifs/${selectedPokemon.is_shiny ? (selectedPokemon.id - 1000) + '_S' : selectedPokemon.id}.gif`"
                    :alt="selectedPokemon.name"
                    class="w-12 h-12 object-contain"
                    style="image-rendering: pixelated;"
                  />
                  <div class="flex-1">
                    <h4 class="font-bold text-sm">{{ selectedPokemon.name }}</h4>
                    <p class="text-xs text-base-content/70">Niv. {{ selectedPokemon.level }}</p>
                  </div>
                  <Button @click="resetForm" variant="ghost" size="sm">✕</Button>
                </div>
                
                <div class="flex items-center gap-2 mb-2">
                  <RarityBadge :rarity="selectedPokemon.rarity" size="xs" />
                  <span v-if="selectedPokemon.is_shiny" class="text-yellow-400 text-xs">✨</span>
                </div>
                
                <div class="flex flex-wrap gap-1">
                  <PokemonTypeBadge
                    v-for="type in getTypes(selectedPokemon.types)"
                    :key="type"
                    :type="type"
                    size="xs"
                  />
                </div>
              </div>
              
              <Input
                v-model="form.price"
                type="number"
                label="Prix de vente"
                :placeholder="`${formatPrice(priceRange.min)} - ${formatPrice(priceRange.max)}`"
                variant="bordered"
                size="sm"
                :helper-text="`Recommandé : ${formatPrice(priceRange.min)} - ${formatPrice(priceRange.max)}`"
              />
              
              <Button
                @click="listPokemon"
                :disabled="!canSubmit || processing"
                variant="primary"
                size="md"
                class="w-full"
              >
                {{ processing ? '🔄' : '💰' }} Mettre en vente
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - My Listings -->
      <div class="absolute right-4 top-20 w-72">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-[500px]">
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🏪</span>
              MES ANNONCES
            </h3>
          </div>
          
          <div class="flex-1 overflow-y-auto p-3">
            <div v-if="myListings.length === 0" class="text-center py-8">
              <p class="text-2xl mb-2">📭</p>
              <p class="text-sm mb-1">Aucune annonce</p>
              <p class="opacity-70 text-xs">Créez votre première annonce !</p>
            </div>
            
            <div v-else class="space-y-2">
              <div 
                v-for="listing in myListings" 
                :key="listing.id"
                class="bg-base-200/30 backdrop-blur-sm rounded-lg p-3 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer group"
                @click="showPokemonDetails(listing.pokemon)"
              >
                <div class="flex items-center gap-3">
                  <div class="relative">
                    <img
                      :src="`/images/pokemon-gifs/${listing.pokemon.is_shiny ? (listing.pokemon.id - 1000) + '_S' : listing.pokemon.id}.gif`"
                      :alt="listing.pokemon.name"
                      class="w-10 h-10 object-contain group-hover:scale-110 transition-transform duration-200"
                      style="image-rendering: pixelated;"
                    />
                    <div v-if="listing.pokemon.is_shiny" class="absolute -top-1 -right-1 text-yellow-400 text-xs">✨</div>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm text-base-content truncate">{{ listing.pokemon.name }}</h4>
                    <p class="text-xs text-base-content/70">Niv. {{ listing.pokemon.level }}</p>
                    <div class="text-xs font-bold text-warning">{{ formatPrice(listing.price) }}</div>
                  </div>
                  
                  <Button
                    @click.stop="cancelListing(listing.id)"
                    variant="ghost"
                    size="sm"
                    class="!text-error hover:!bg-error/5"
                  >
                    🗑️
                  </Button>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="myListings.length > 0" class="bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
            <div class="text-xs text-center text-base-content/70">
              {{ myListings.length }} annonce{{ myListings.length > 1 ? 's' : '' }}
            </div>
          </div>
        </div>
        
        <!-- Back to Marketplace -->
        <div class="mt-4">
          <Button
            @click="router.visit('/marketplace')"
            variant="secondary"
            size="sm"
            class="w-full"
          >
            ← Retour marketplace
          </Button>
        </div>
      </div>

      <!-- Center Logo or Stats -->
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
        <div class="text-center">
          <div class="w-32 h-32 bg-gradient-to-br from-warning/20 to-warning/10 rounded-full flex items-center justify-center mb-4 border border-warning/30">
            <span class="text-6xl">🏷️</span>
          </div>
          <h2 class="text-xl font-bold text-base-content/70 mb-2">Marketplace</h2>
          <div class="grid grid-cols-2 gap-4 text-center">
            <div class="group cursor-pointer">
              <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Disponibles</div>
              <div class="text-lg font-bold group-hover:scale-110 transition-transform text-success">{{ availablePokemons.length }}</div>
            </div>
            <div class="group cursor-pointer">
              <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">En vente</div>
              <div class="text-lg font-bold text-warning group-hover:scale-110 transition-transform">{{ myListings.length }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pokemon Selection Modal -->
      <Modal :show="showCreateModal" @close="showCreateModal = false">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-success/20 to-success/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">📦</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent">
                Choisir un Pokémon
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-success">{{ availablePokemons.length }} disponibles</span>
              </div>
            </div>
          </div>
        </template>
        
        <template #default>
          <div v-if="availablePokemons.length === 0" class="text-center py-8">
            <p class="text-2xl mb-2">😔</p>
            <p class="text-lg font-bold mb-2">Aucun Pokémon disponible</p>
            <p class="text-base-content/70 mb-4">Tous vos Pokémon sont dans votre équipe ou déjà en vente.</p>
            <Button @click="showCreateModal = false" variant="secondary" size="md">
              Fermer
            </Button>
          </div>
          
          <div v-else>
            <div class="mb-6">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                🎯 Choisir un Pokémon
              </h3>
              <p class="text-base-content/70 text-sm mt-1">Sélectionnez un Pokémon de votre inventaire à mettre en vente</p>
            </div>
            
            <div class="max-h-[500px] overflow-y-auto">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="pokemon in availablePokemons" 
                  :key="pokemon.id"
                  class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer group"
                  @click="selectPokemon(pokemon)"
                >
                  <div class="flex flex-col items-center">
                    <!-- Pokemon Image -->
                    <div class="relative mb-3">
                      <img
                        :src="`/images/pokemon-gifs/${pokemon.is_shiny ? (pokemon.id - 1000) + '_S' : pokemon.id}.gif`"
                        :alt="pokemon.name"
                        class="w-20 h-20 object-contain group-hover:scale-110 transition-transform duration-300"
                        style="image-rendering: pixelated;"
                      />
                      <!-- Shiny indicator -->
                      <div v-if="pokemon.is_shiny" class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                        <span class="text-yellow-400 text-sm">✨</span>
                      </div>
                      <!-- Stars in top left -->
                      <div v-if="pokemon.rarity" class="absolute -top-2 -left-2 w-auto h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-2">
                        <span class="text-yellow-400 text-xs font-bold">{{ pokemon.rarity === 'normal' ? 1 : pokemon.rarity === 'rare' ? 2 : pokemon.rarity === 'epic' ? 3 : 4 }}</span>
                        <span class="text-yellow-400 text-xs ml-1">⭐</span>
                      </div>
                    </div>
                    
                    <!-- Pokemon Name & Level -->
                    <div class="text-center mb-2">
                      <h4 class="font-bold text-lg text-base-content">{{ pokemon.name }}</h4>
                      <p class="text-sm text-base-content/70">Niveau {{ pokemon.level }}</p>
                    </div>
                    
                    <!-- Rarity Badge -->
                    <div class="flex items-center justify-center mb-2">
                      <RarityBadge v-if="pokemon.rarity" :rarity="pokemon.rarity" size="xs" />
                    </div>
                    
                    <!-- Types -->
                    <div class="flex flex-wrap gap-1 justify-center mb-3">
                      <PokemonTypeBadge
                        v-for="(type, index) in getTypes(pokemon.types)"
                        :key="index"
                        :type="type"
                        size="xs"
                      />
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-1 w-full">
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">HP</div>
                        <div class="text-sm font-bold text-error">{{ pokemon.hp }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">ATK</div>
                        <div class="text-sm font-bold text-warning">{{ pokemon.attack }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">DEF</div>
                        <div class="text-sm font-bold text-info">{{ pokemon.defense }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">SPD</div>
                        <div class="text-sm font-bold text-success">{{ pokemon.speed }}</div>
                      </div>
                    </div>
                    
                    <!-- Click hint -->
                    <div class="mt-2 text-xs text-center text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                      Cliquer pour sélectionner
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Modal>

      <!-- Pokemon Detail Modal -->
      <Modal :show="showPokemonModal" @close="showPokemonModal = false">
        <template #header>
          <div v-if="selectedPokemon" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">📦</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                {{ selectedPokemon.name }}
                <span v-if="selectedPokemon.is_shiny" class="text-yellow-400 ml-2">✨</span>
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-warning">Niveau {{ selectedPokemon.level }}</span>
              </div>
            </div>
          </div>
        </template>
        
        <template #default>
          <div v-if="selectedPokemon" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Pokemon Image -->
            <div class="flex justify-center">
              <div class="relative">
                <img
                  :src="`/images/pokemon-gifs/${selectedPokemon.is_shiny ? (selectedPokemon.id - 1000) + '_S' : selectedPokemon.id}.gif`"
                  :alt="selectedPokemon.name"
                  class="w-48 h-48 object-contain"
                  style="image-rendering: pixelated;"
                />
              </div>
            </div>

            <!-- Pokemon Details -->
            <div class="space-y-6">
              <div class="text-center">
                <RarityBadge :rarity="selectedPokemon.rarity" size="md" />
              </div>
              
              <div class="flex justify-center gap-2">
                <PokemonTypeBadge
                  v-for="type in getTypes(selectedPokemon.types)"
                  :key="type"
                  :type="type"
                  size="sm"
                />
              </div>

              <!-- Stats -->
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">HP</span>
                  <p class="text-2xl font-bold text-error">{{ selectedPokemon.hp }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Attaque</span>
                  <p class="text-2xl font-bold text-warning">{{ selectedPokemon.attack }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Défense</span>
                  <p class="text-2xl font-bold text-info">{{ selectedPokemon.defense }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Vitesse</span>
                  <p class="text-2xl font-bold text-success">{{ selectedPokemon.speed }}</p>
                </div>
              </div>

              <!-- Status -->
              <div class="bg-base-200/50 rounded-xl p-4 text-center">
                <p class="text-base-content/70 text-sm mb-2">Statut</p>
                <div class="flex justify-center gap-2">
                  <span v-if="selectedPokemon.is_in_team" class="px-3 py-1 bg-primary/20 text-primary rounded-full text-sm">Dans l'équipe</span>
                  <span v-else class="px-3 py-1 bg-success/20 text-success rounded-full text-sm">Disponible</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Select from '@/Components/UI/Select.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Modal from '@/Components/UI/Modal.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import Spinner from '@/Components/UI/Spinner.vue';

interface Pokemon {
  id: number;
  name: string;
  level: number;
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  is_shiny: boolean;
  rarity: string;
  types: string | any[];
  is_in_team: boolean;
}

interface Listing {
  id: number;
  price: number;
  pokemon: Pokemon;
  seller: {
    id: number;
    username: string;
  };
}

interface Props {
  userPokemons: Pokemon[];
  myListings: Listing[];
}

const props = defineProps<Props>();
const userPokemons = ref(props.userPokemons || []);
const myListings = ref(props.myListings || []);
const selectedPokemon = ref<Pokemon | null>(null);
const showCreateModal = ref(false);
const showPokemonModal = ref(false);
const priceRange = ref({ min: 10, max: 1000000 });
const processing = ref(false);

const form = ref({
  pokemon_id: '',
  price: ''
});

const availablePokemons = computed(() => {
  return userPokemons.value.filter(pokemon => !pokemon.is_in_team);
});

const canSubmit = computed(() => {
  return form.value.pokemon_id && 
         form.value.price && 
         Number(form.value.price) >= priceRange.value.min && 
         Number(form.value.price) <= priceRange.value.max;
});

const getTypes = (types: string | any[]) => {
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

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

const selectPokemon = (pokemon: Pokemon) => {
  form.value.pokemon_id = pokemon.id.toString();
  selectedPokemon.value = pokemon;
  updatePriceRange();
  showCreateModal.value = false;
};

const updatePriceRange = () => {
  if (!selectedPokemon.value) return;
  
  const ranges = {
    normal: { min: 10, max: 1000000 },
    rare: { min: 100, max: 1000000 },
    epic: { min: 1000, max: 1000000 },
    legendary: { min: 10000, max: 1000000 }
  };
  
  priceRange.value = ranges[selectedPokemon.value.rarity as keyof typeof ranges] || ranges.normal;
  
  if (!form.value.price || Number(form.value.price) < priceRange.value.min) {
    form.value.price = priceRange.value.min.toString();
  } else if (Number(form.value.price) > priceRange.value.max) {
    form.value.price = priceRange.value.max.toString();
  }
};

const showPokemonDetails = (pokemon: Pokemon) => {
  selectedPokemon.value = pokemon;
  showPokemonModal.value = true;
};

const resetForm = () => {
  form.value = { pokemon_id: '', price: '' };
  selectedPokemon.value = null;
};

const listPokemon = () => {
  if (!canSubmit.value) return;
  
  processing.value = true;
  
  const formData = new FormData();
  formData.append('pokemon_id', form.value.pokemon_id);
  formData.append('price', form.value.price);
  
  router.post('/marketplace/sell', formData, {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
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

const cancelListing = (listingId: number) => {
  if (confirm('Êtes-vous sûr de vouloir retirer cette annonce ?')) {
    processing.value = true;
    
    router.post(`/marketplace/cancel/${listingId}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(listing => listing.id !== listingId);
        processing.value = false;
        showPokemonModal.value = false;
      },
      onError: () => {
        processing.value = false;
      }
    });
  }
};
</script> 
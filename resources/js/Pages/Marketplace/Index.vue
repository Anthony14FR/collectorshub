<template>
  <Head title="Marketplace" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    
    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <!-- Header -->
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏪 MARKETPLACE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ showMyListings ? 'MES ANNONCES' : 'POKÉMONS À VENDRE' }}
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

      <!-- Left Side - Filters -->
      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔍</span>
              FILTRES
            </h3>
          </div>
          
          <div class="p-3 space-y-3">
            <Select
              v-model="filters.rarity"
              :options="rarityOptions"
              label="Rareté"
              variant="default"
              size="xs"
            />
            <Select
              v-model="filters.type"
              :options="typeOptions"
              label="Type"
              variant="default"
              size="xs"
            />
            <Select
              v-model="filters.isShiny"
              :options="shinyOptions"
              label="Shiny"
              variant="default"
              size="xs"
            />
            <Input
              v-model="filters.minPrice"
              type="number"
              label="Prix min"
              placeholder="0"
              variant="default"
              size="sm"
            />
            <Input
              v-model="filters.maxPrice"
              type="number"
              label="Prix max"
              placeholder="999999"
              variant="default"
              size="sm"
            />
            
            <Button
              @click="applyFilters"
              variant="secondary"
              size="sm"
              :disabled="loading"
              class="w-full"
            >
              {{ loading ? '🔄' : '🔍' }} Filtrer
            </Button>
          </div>
        </div>
      </div>

      <!-- Right Side - Controls -->
      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-accent/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚙️</span>
              ACTIONS
            </h3>
          </div>
          
          <div class="p-3 space-y-2">
            <Button
              @click="toggleMyListings"
              :variant="showMyListings ? 'primary' : 'secondary'"
              size="sm"
              class="w-full"
            >
              {{ showMyListings ? '👥 Voir toutes' : '👤 Mes annonces' }}
            </Button>
            
            <Button
              @click="router.visit('/marketplace/sell')"
              variant="secondary"
              size="sm"
              class="w-full"
            >
              🏷️ Vendre
            </Button>
          </div>
        </div>
        
        <!-- Stats -->
        <div v-if="!loading" class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mt-4">
          <div class="bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-b border-warning/20">
            <h4 class="text-xs font-bold tracking-wider">STATISTIQUES</h4>
          </div>
          <div class="p-3">
            <div class="grid grid-cols-2 gap-2 text-center">
              <div class="group cursor-pointer">
                <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Total</div>
                <div class="text-sm font-bold group-hover:scale-110 transition-transform">{{ displayedListings.length }}</div>
              </div>
              <div class="group cursor-pointer">
                <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Shiny</div>
                <div class="text-sm font-bold text-yellow-400 group-hover:scale-110 transition-transform">
                  {{ displayedListings.filter(l => l.pokemon.is_shiny).length }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Center Content -->
      <div class="absolute top-32 left-1/2 -translate-x-1/2 w-[700px] max-h-[600px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📦</span>
              {{ showMyListings ? 'MES ANNONCES' : 'MARKETPLACE' }}
            </h3>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <div v-if="loading" class="flex justify-center items-center py-12">
              <Spinner size="md" />
            </div>

            <div v-else-if="paginatedListings.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="listing in paginatedListings" 
                :key="listing.id"
                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer group"
                @click="showPokemonDetails(listing)"
              >
                <div class="flex gap-4">
                  <!-- Pokemon Image -->
                  <div class="relative flex-shrink-0">
                    <img
                      :src="`/images/pokemon-gifs/${listing.pokemon.is_shiny ? (listing.pokemon.id - 1000) + '_S' : listing.pokemon.id}.gif`"
                      :alt="listing.pokemon.name"
                      class="w-20 h-20 object-contain group-hover:scale-110 transition-transform duration-300"
                      style="image-rendering: pixelated;"
                    />
                    <!-- Shiny indicator -->
                    <div v-if="listing.pokemon.is_shiny" class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                      <span class="text-yellow-400 text-sm">✨</span>
                    </div>
                    <!-- Stars in top left -->
                    <div v-if="listing.pokemon.rarity" class="absolute -top-2 -left-2 w-auto h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-2">
                      <span class="text-yellow-400 text-xs font-bold">{{ listing.pokemon.rarity === 'normal' ? 1 : listing.pokemon.rarity === 'rare' ? 2 : listing.pokemon.rarity === 'epic' ? 3 : 4 }}</span>
                      <span class="text-yellow-400 text-xs ml-1">⭐</span>
                    </div>
                  </div>
                  
                  <!-- Pokemon Info -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-2">
                      <div>
                        <h4 class="font-bold text-lg text-base-content">{{ listing.pokemon.name }}</h4>
                        <p class="text-sm text-base-content/70">Niveau {{ listing.pokemon.level }}</p>
                      </div>
                      <div class="text-right">
                        <div class="text-xl font-bold text-warning">{{ formatPrice(listing.price) }}</div>
                        <div class="text-xs text-base-content/50">Prix</div>
                      </div>
                    </div>
                    
                    <!-- Rarity Badge -->
                    <div class="flex items-center gap-2 mb-3">
                      <RarityBadge v-if="listing.pokemon.rarity" :rarity="listing.pokemon.rarity" size="xs" />
                    </div>
                    
                    <!-- Types -->
                    <div class="flex flex-wrap gap-1 mb-3">
                      <PokemonTypeBadge
                        v-for="(type, index) in getTypes(listing.pokemon.types)"
                        :key="index"
                        :type="type"
                        size="xs"
                      />
                    </div>
                    
                    <!-- Stats mini -->
                    <div class="grid grid-cols-4 gap-1 mb-2">
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">HP</div>
                        <div class="text-sm font-bold text-error">{{ listing.pokemon.hp }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">ATK</div>
                        <div class="text-sm font-bold text-warning">{{ listing.pokemon.attack }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">DEF</div>
                        <div class="text-sm font-bold text-info">{{ listing.pokemon.defense }}</div>
                      </div>
                      <div class="bg-base-300/30 rounded px-2 py-1 text-center">
                        <div class="text-xs text-base-content/70">SPD</div>
                        <div class="text-sm font-bold text-success">{{ listing.pokemon.speed }}</div>
                      </div>
                    </div>
                    
                    <!-- Seller -->
                    <div class="text-xs text-base-content/70 flex items-center justify-between">
                      <span>Vendeur: {{ listing.seller.username }}{{ showMyListings ? ' (Vous)' : '' }}</span>
                      <span class="text-primary">Cliquer pour détails</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <p class="text-2xl mb-2">{{ showMyListings ? '📭' : '🛒' }}</p>
              <p class="text-sm mb-1">{{ showMyListings ? 'Aucune annonce' : 'Aucun Pokémon en vente' }}</p>
              <p class="opacity-70 text-xs">
                {{ showMyListings ? 'Mettez des Pokémon en vente !' : 'Revenez plus tard !' }}
              </p>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-4 py-3 border-t border-warning/20">
            <div class="flex justify-center items-center gap-2">
              <Button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                variant="ghost"
                size="sm"
              >
                ←
              </Button>
              
              <span v-for="page in totalPages" :key="page">
                <Button
                  @click="changePage(page)"
                  :variant="currentPage === page ? 'primary' : 'ghost'"
                  size="sm"
                  class="min-w-[2rem]"
                >
                  {{ page }}
                </Button>
              </span>
              
              <Button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                variant="ghost"
                size="sm"
              >
                →
              </Button>
            </div>
          </div>

          <div v-if="displayedListings.length > 0" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
            <div class="text-xs text-center text-base-content/70">
              {{ displayedListings.length }} annonce{{ displayedListings.length > 1 ? 's' : '' }} trouvée{{ displayedListings.length > 1 ? 's' : '' }}
              {{ totalPages > 1 ? ` - Page ${currentPage}/${totalPages}` : '' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Pokemon Detail Modal -->
      <Modal :show="showPokemonModal" @close="showPokemonModal = false">
        <template #header>
          <div v-if="selectedPokemon" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">📦</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                {{ selectedPokemon.pokemon.name }}
                <span v-if="selectedPokemon.pokemon.is_shiny" class="text-yellow-400 ml-2">✨</span>
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-warning">{{ formatPrice(selectedPokemon.price) }}</span>
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
                  :src="`/images/pokemon-gifs/${selectedPokemon.pokemon.is_shiny ? (selectedPokemon.pokemon.id - 1000) + '_S' : selectedPokemon.pokemon.id}.gif`"
                  :alt="selectedPokemon.pokemon.name"
                  class="w-48 h-48 object-contain"
                  style="image-rendering: pixelated;"
                />
              </div>
            </div>

            <!-- Pokemon Details -->
            <div class="space-y-6">
              <div class="text-center">
                <RarityBadge v-if="selectedPokemon.pokemon.rarity" :rarity="selectedPokemon.pokemon.rarity" size="md" />
              </div>
              
              <div class="flex justify-center gap-2">
                <PokemonTypeBadge
                  v-for="type in getTypes(selectedPokemon.pokemon.types)"
                  :key="type"
                  :type="type"
                  size="sm"
                />
              </div>

              <!-- Stats -->
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">HP</span>
                  <p class="text-2xl font-bold text-error">{{ selectedPokemon.pokemon.hp }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Attaque</span>
                  <p class="text-2xl font-bold text-warning">{{ selectedPokemon.pokemon.attack }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Défense</span>
                  <p class="text-2xl font-bold text-info">{{ selectedPokemon.pokemon.defense }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Vitesse</span>
                  <p class="text-2xl font-bold text-success">{{ selectedPokemon.pokemon.speed }}</p>
                </div>
              </div>

              <!-- Seller Info -->
              <div class="bg-base-200/50 rounded-xl p-4">
                <h4 class="font-bold text-base-content mb-2">Vendeur</h4>
                <p class="text-base-content/70">
                  {{ selectedPokemon.seller.username }}
                  <span v-if="showMyListings" class="text-primary ml-1">(Vous)</span>
                </p>
              </div>

              <!-- Actions -->
              <div class="space-y-2">
                <Button
                  v-if="showMyListings"
                  @click="cancelListing(selectedPokemon.id)"
                  variant="outline"
                  size="lg"
                  :disabled="loading"
                  class="w-full !text-error !border-error/30 hover:!border-error/50 hover:!bg-error/5"
                >
                  {{ loading ? '🔄' : '🗑️' }} Retirer de la vente
                </Button>
                <Button
                  v-else
                  @click="buyPokemon(selectedPokemon)"
                  variant="primary"
                  size="lg"
                  :disabled="loading"
                  class="w-full"
                >
                  {{ loading ? '🔄' : '💰' }} Acheter pour {{ formatPrice(selectedPokemon.price) }}
                </Button>
              </div>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
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
  myListings: Listing[];
  otherListings: Listing[];
}

const props = defineProps<Props>();
const loading = ref(false);
const showMyListings = ref(false);
const myListings = ref(props.myListings || []);
const otherListings = ref(props.otherListings || []);
const selectedPokemon = ref<Listing | null>(null);
const showPokemonModal = ref(false);
const currentPage = ref(1);
const itemsPerPage = 6;

const filters = ref({
  rarity: '',
  type: '',
  isShiny: '',
  minPrice: '',
  maxPrice: ''
});

const rarityOptions = [
  { value: '', label: 'Toutes' },
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' }
];

const typeOptions = [
  { value: '', label: 'Tous' },
  { value: 'normal', label: 'Normal' },
  { value: 'fire', label: 'Feu' },
  { value: 'water', label: 'Eau' },
  { value: 'electric', label: 'Électrique' },
  { value: 'grass', label: 'Plante' },
  { value: 'ice', label: 'Glace' },
  { value: 'fighting', label: 'Combat' },
  { value: 'poison', label: 'Poison' },
  { value: 'ground', label: 'Sol' },
  { value: 'flying', label: 'Vol' },
  { value: 'psychic', label: 'Psy' },
  { value: 'bug', label: 'Insecte' },
  { value: 'rock', label: 'Roche' },
  { value: 'ghost', label: 'Spectre' },
  { value: 'dragon', label: 'Dragon' },
  { value: 'dark', label: 'Ténèbres' },
  { value: 'steel', label: 'Acier' },
  { value: 'fairy', label: 'Fée' }
];

const shinyOptions = [
  { value: '', label: 'Tous' },
  { value: 'true', label: 'Shiny uniquement' },
  { value: 'false', label: 'Normal uniquement' }
];

const displayedListings = computed(() => {
  return showMyListings.value ? myListings.value : otherListings.value;
});

const paginatedListings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return displayedListings.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(displayedListings.value.length / itemsPerPage);
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

const applyFilters = async () => {
  loading.value = true;
  currentPage.value = 1;
  
  try {
    const params: any = {};
    
    if (showMyListings.value) params.myListings = 'true';
    if (filters.value.rarity) params.rarity = filters.value.rarity;
    if (filters.value.type) params.type = filters.value.type;
    if (filters.value.isShiny) params.isShiny = filters.value.isShiny;
    if (filters.value.minPrice) params.minPrice = Number(filters.value.minPrice);
    if (filters.value.maxPrice) params.maxPrice = Number(filters.value.maxPrice);
    
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

const buyPokemon = (listing: Listing) => {
  loading.value = true;
  
  router.post(`/marketplace/buy/${listing.id}`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      loading.value = false;
      showPokemonModal.value = false;
    },
    onError: () => {
      loading.value = false;
    }
  });
};

const cancelListing = (listingId: number) => {
  if (confirm('Êtes-vous sûr de vouloir retirer cette annonce ?')) {
    loading.value = true;
    
    router.post(`/marketplace/cancel/${listingId}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(listing => listing.id !== listingId);
        loading.value = false;
        showPokemonModal.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    });
  }
};

const showPokemonDetails = (listing: Listing) => {
  selectedPokemon.value = listing;
  showPokemonModal.value = true;
};

const toggleMyListings = () => {
  showMyListings.value = !showMyListings.value;
  currentPage.value = 1;
};

const changePage = (page: number) => {
  currentPage.value = page;
};

onMounted(() => {
  if (myListings.value.length === 0 && otherListings.value.length === 0) {
    applyFilters();
  }
});
</script> 
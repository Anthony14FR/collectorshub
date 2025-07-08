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
            POKÉMONS À VENDRE
          </p>
        </div>
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
        <!-- Controls Section -->
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-accent/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚙️</span>
              ACTIONS
            </h3>
          </div>

          <div class="p-3">
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

        <!-- My Listings Section - TOUJOURS VISIBLE -->
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-[500px] mt-4">
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
              >
                <div class="flex items-center gap-3">
                  <div class="relative">
                    <img
                      :src="`/images/pokemon-gifs/${getPokemonData(listing).is_shiny ? (getPokemonData(listing).id - 1000) + '_S' : getPokemonData(listing).id}.gif`"
                      :alt="getPokemonData(listing).name"
                      class="w-12 h-12 object-contain group-hover:scale-110 transition-transform duration-200"
                      style="image-rendering: pixelated;"
                    />
                    <!-- Shiny indicator -->
                    <div v-if="getPokemonData(listing).is_shiny" class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                      <span class="text-yellow-400 text-xs">✨</span>
                    </div>
                    <!-- Rarity stars -->
                    <div v-if="getPokemonData(listing).rarity" class="absolute -top-1 -left-1 w-auto h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-1">
                      <span class="text-yellow-400 text-xs font-bold">{{ getRarityStars(getPokemonData(listing).rarity) }}⭐</span>
                    </div>
                  </div>

                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm text-base-content truncate">{{ getPokemonData(listing).name }}</h4>
                    <p class="text-xs text-base-content/70">Niv. {{ getPokemonData(listing).level }}</p>
                    <!-- Rarity text with color -->
                    <p v-if="getPokemonData(listing).rarity" class="text-xs font-medium uppercase" :class="getRarityColor(getPokemonData(listing).rarity)">
                      {{ getPokemonData(listing).rarity }}
                    </p>
                    <div class="text-xs font-bold text-warning">{{ formatPrice(listing.price) }}</div>
                  </div>

                  <Button
                    @click.stop="showCancelConfirm(listing)"
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

        <!-- Wallet Display -->
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mt-4">
          <div class="bg-gradient-to-r from-success/10 to-success/5 px-3 py-2 border-b border-success/20">
            <h4 class="text-xs font-bold tracking-wider">PORTE-MONNAIE</h4>
          </div>
          <div class="p-3 text-center">
            <div class="text-2xl font-bold text-success">{{ formatPrice(userCash) }}</div>
            <div class="text-xs text-base-content/70">Votre solde</div>
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
                <div class="text-sm font-bold group-hover:scale-110 transition-transform">{{ otherListings.length }}</div>
              </div>
              <div class="group cursor-pointer">
                <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Shiny</div>
                <div class="text-sm font-bold text-yellow-400 group-hover:scale-110 transition-transform">
                  {{ otherListings.filter(l => getPokemonData(l).is_shiny).length }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Center Content -->
      <div class="absolute top-32 left-1/2 -translate-x-1/2 w-[700px] max-h-[600px]">
        <!-- Flash Messages - Positioned above the marketplace section -->
        <div v-if="$page.props.flash?.success" class="mb-4">
          <Alert type="success" :message="$page.props.flash.success" />
        </div>
        <div v-if="$page.props.errors?.message" class="mb-4">
          <Alert type="error" :message="$page.props.errors.message" />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📦</span>
              MARKETPLACE
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
                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 group"
              >
                <div class="flex flex-col h-full">
                  <!-- Top section: Image + Info -->
                  <div class="flex gap-4 mb-3">
                    <!-- Pokemon Image -->
                    <div class="relative flex-shrink-0 flex items-center">
                      <img
                        :src="`/images/pokemon-gifs/${getPokemonData(listing).is_shiny ? (getPokemonData(listing).id - 1000) + '_S' : getPokemonData(listing).id}.gif`"
                        :alt="getPokemonData(listing).name"
                        class="w-24 h-24 object-contain group-hover:scale-110 transition-transform duration-300"
                        style="image-rendering: pixelated;"
                      />
                      <!-- Rarity and Shiny indicators in top left -->
                      <div v-if="getPokemonData(listing).rarity || getPokemonData(listing).is_shiny" class="absolute -top-2 -left-2 flex gap-1">
                        <!-- Rarity stars -->
                        <div v-if="getPokemonData(listing).rarity" class="w-auto h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-2">
                          <span class="text-yellow-400 text-xs font-bold">{{ getRarityStars(getPokemonData(listing).rarity) }}⭐</span>
                        </div>
                        <!-- Shiny indicator -->
                        <div v-if="getPokemonData(listing).is_shiny" class="w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                          <span class="text-yellow-400 text-xs">✨</span>
                        </div>
                      </div>
                    </div>

                    <!-- Pokemon Info -->
                    <div class="flex-1 min-w-0 flex flex-col">
                      <div class="flex items-start justify-between mb-2">
                        <div>
                          <h4 class="font-bold text-base text-base-content">{{ getPokemonData(listing).name }}</h4>
                          <p class="text-xs text-base-content/70">Niveau {{ getPokemonData(listing).level }}</p>
                        </div>
                        <div class="text-right">
                          <div class="text-lg font-bold text-warning">{{ formatPrice(listing.price) }}</div>
                        </div>
                      </div>



                      <!-- Types -->
                      <div class="flex flex-wrap gap-1">
                        <PokemonTypeBadge
                          v-for="(type, index) in getTypes(getPokemonData(listing).types)"
                          :key="index"
                          :type="type"
                          size="xs"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Bottom section with seller and button -->
                  <div class="flex items-center justify-between mt-auto pt-2 border-t border-base-300/20">
                    <!-- Seller info on bottom left -->
                    <div class="text-xs text-base-content/70">
                      <span>{{ listing.seller.username }}</span>
                    </div>

                    <!-- Action button on bottom right -->
                    <div>
                      <Button
                        @click="showPurchaseConfirm(listing)"
                        variant="secondary"
                        size="sm"
                        :disabled="loading || userCash < listing.price"
                        :class="userCash < listing.price ? 'opacity-50' : ''"
                      >
                        💰 Acheter
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <p class="text-2xl mb-2">🛒</p>
              <p class="text-sm mb-1">Aucun Pokémon en vente</p>
              <p class="opacity-70 text-xs">Revenez plus tard !</p>
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

      <!-- Purchase Confirmation Modal -->
      <ConfirmationModal
        :show="showPurchaseModal"
        :listing="selectedListing"
        :user-cash="userCash"
        :loading="loading"
        @close="showPurchaseModal = false"
        @confirm="confirmPurchase"
      />

      <!-- Cancel Confirmation Modal -->
      <Modal :show="showCancelModal" @close="showCancelModal = false">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-error/20 to-error/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">🗑️</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent">
                Retirer de la vente
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-error">Confirmation requise</span>
              </div>
            </div>
          </div>
        </template>

        <template #default>
          <div v-if="selectedListing" class="space-y-4">
            <p class="text-base-content">
              Êtes-vous sûr de vouloir retirer
              <span class="font-bold">{{ selectedListing.pokemon.name }}</span>
              de la vente ?
            </p>

            <div class="flex gap-3">
              <Button
                @click="showCancelModal = false"
                variant="outline"
                size="lg"
                class="flex-1"
                :disabled="loading"
              >
                Annuler
              </Button>

              <Button
                @click="confirmCancel"
                variant="error"
                size="lg"
                class="flex-1"
                :disabled="loading"
              >
                {{ loading ? '🔄 En cours...' : '🗑️ Confirmer' }}
              </Button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, Head, usePage } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Select from '@/Components/UI/Select.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Modal from '@/Components/UI/Modal.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
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
const page = usePage();
const loading = ref(false);
const myListings = ref(props.myListings || []);
const otherListings = ref(props.otherListings || []);
const selectedListing = ref<Listing | null>(null);
const showPurchaseModal = ref(false);
const showCancelModal = ref(false);
const currentPage = ref(1);
const itemsPerPage = 6;

// Get user cash from page props
const userCash = computed(() => {
  return page.props.auth?.user?.cash || 0;
});

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
  // Affiche toujours les autres annonces dans le centre, mes annonces sont à droite
  return otherListings.value;
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
  console.log('Types bruts reçus:', types);
  if (!types) return [];
  if (typeof types === 'string') {
    try {
      const parsed = JSON.parse(types);
      console.log('Types parsés:', parsed);
      return parsed;
    } catch (e) {
      console.log('Erreur parsing types:', e);
      return [];
    }
  }
  if (Array.isArray(types)) {
    const result = types.map(type => typeof type === 'object' && type.name ? type.name : type);
    console.log('Types array traités:', result);
    return result;
  }
  return [];
};

const getPokemonData = (listing: Listing) => {
  // Access the nested pokemon data correctly
  const pokemonData = listing.pokemon.pokemon || listing.pokemon;
  return {
    ...listing.pokemon,
    name: pokemonData.name || listing.pokemon.name,
    rarity: pokemonData.rarity || listing.pokemon.rarity,
    is_shiny: pokemonData.is_shiny || listing.pokemon.is_shiny,
    types: pokemonData.types || listing.pokemon.types,
    level: listing.pokemon.level,
    id: pokemonData.id || listing.pokemon.id,
    hp: pokemonData.hp || listing.pokemon.hp,
    attack: pokemonData.attack || listing.pokemon.attack,
    defense: pokemonData.defense || listing.pokemon.defense,
    speed: pokemonData.speed || listing.pokemon.speed
  };
};

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

const getRarityStars = (rarity: string) => {
  switch(rarity) {
    case 'normal': return 1;
    case 'rare': return 2;
    case 'epic': return 3;
    case 'legendary': return 4;
    default: return 1;
  }
};

const getRarityColor = (rarity: string) => {
  switch(rarity) {
    case 'normal': return 'text-base-content/70';
    case 'rare': return 'text-blue-400';
    case 'epic': return 'text-purple-400';
    case 'legendary': return 'text-orange-400';
    default: return 'text-base-content/70';
  }
};

const applyFilters = async () => {
  loading.value = true;
  currentPage.value = 1;

  try {
    const params: any = {};

    // Les filtres n'affectent que les autres annonces, PAS mes annonces
    if (filters.value.rarity) params.rarity = filters.value.rarity;
    if (filters.value.type) params.type = filters.value.type;
    if (filters.value.isShiny) params.isShiny = filters.value.isShiny;
    if (filters.value.minPrice) params.minPrice = Number(filters.value.minPrice);
    if (filters.value.maxPrice) params.maxPrice = Number(filters.value.maxPrice);

    const response = await fetch(`/marketplace/listings?${new URLSearchParams(params)}`);
    const data = await response.json();

    // Toujours mettre à jour seulement otherListings
    otherListings.value = data;
  } catch (error) {
    console.error('Erreur lors de la récupération des annonces:', error);
  } finally {
    loading.value = false;
  }
};

const showPurchaseConfirm = (listing: Listing) => {
  // Garde l'ID original de la listing, mais transforme seulement les données Pokemon pour l'affichage
  const transformedListing = {
    ...listing, // Garde l'ID original !
    pokemon: getPokemonData(listing)
  };
  selectedListing.value = transformedListing;
  showPurchaseModal.value = true;
};

const showCancelConfirm = (listing: Listing) => {
  // Transform the listing with correct pokemon data
  const transformedListing = {
    ...listing,
    pokemon: getPokemonData(listing)
  };
  selectedListing.value = transformedListing;
  showCancelModal.value = true;
};

const confirmPurchase = () => {
  if (!selectedListing.value) return;

  loading.value = true;

  router.post(`/marketplace/buy/${selectedListing.value.id}`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Remove the purchased pokemon from the listings
      otherListings.value = otherListings.value.filter(listing => listing.id !== selectedListing.value!.id);
      loading.value = false;
      showPurchaseModal.value = false;
      selectedListing.value = null;
    },
    onError: () => {
      loading.value = false;
    }
  });
};

const confirmCancel = () => {
  if (!selectedListing.value) return;

  loading.value = true;

  router.post(`/marketplace/cancel/${selectedListing.value.id}`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      myListings.value = myListings.value.filter(listing => listing.id !== selectedListing.value!.id);
      loading.value = false;
      showCancelModal.value = false;
      selectedListing.value = null;
    },
    onError: () => {
      loading.value = false;
    }
  });
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

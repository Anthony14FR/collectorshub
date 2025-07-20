<script setup lang="ts">
import PokemonCard from '@/Components/Cards/PokemonCard.vue';
import UpgradeModal from '@/Components/PokemonUpgrade/UpgradeModal.vue';
import UpgradeSuccessModal from '@/Components/PokemonUpgrade/UpgradeSuccessModal.vue';
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import type { PokedexEntry } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import {
  ArrowLeft,
  ChevronLeft,
  ChevronRight,
  RotateCcw,
  Search,
  Star
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

declare function route(name: string, params?: Record<string, any>): string;

interface Props {
  userPokemons: PokedexEntry[];
  flash?: {
    success?: string;
    upgraded_pokemon?: PokedexEntry;
  };
}

const { userPokemons, flash } = defineProps<Props>();

const selectedPokemon = ref<PokedexEntry | null>(null);
const showUpgradeModal = ref(false);
const showSuccessModal = ref(false);

const showAlert = ref(false);
const alertMessage = ref('');
const alertType = ref<'success' | 'error'>('success');

const localUserPokemons = ref([...userPokemons]);

const searchQuery = ref('');
const rarityFilter = ref<string>('all');
const starFilter = ref<string>('all');
const shinyFilter = ref<string>('all');
const upgradableFilter = ref<string>('upgradable');
const currentPage = ref(1);
const itemsPerPage = 18;
const loading = ref(false);
const upgradableIds = ref<number[]>([]);
const errors = ref<Record<string, string>>({});

const rarityOptions = [
  { value: 'all', label: 'Toutes les raretés' },
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' }
];

const starOptions = [
  { value: 'all', label: 'Toutes les étoiles' },
  { value: '0', label: '0★' },
  { value: '1', label: '1★' },
  { value: '2', label: '2★' },
  { value: '3', label: '3★' },
  { value: '4', label: '4★' },
  { value: '5', label: '5★' }
];

const shinyOptions = [
  { value: 'all', label: 'Tous' },
  { value: 'shiny', label: 'Shiny uniquement' },
  { value: 'normal', label: 'Normaux uniquement' }
];

const upgradableOptions = [
  { value: 'upgradable', label: 'Augmentation possible' },
  { value: 'all', label: 'Tous les Pokémon' }
];

const upgradablePokemons = computed(() => {
  return localUserPokemons.value
    .filter(p => p.star < 6)
    .sort((a, b) => a.pokemon_id - b.pokemon_id);
});

const filteredPokemons = computed(() => {
  let filtered = [...upgradablePokemons.value];
  
  if (upgradableFilter.value === 'upgradable') {
    filtered = filtered.filter(p => upgradableIds.value.includes(p.id));
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(p => 
      (p.nickname && p.nickname.toLowerCase().includes(query)) ||
      p.pokemon?.name.toLowerCase().includes(query) ||
      p.pokemon_id.toString().includes(query)
    );
  }

  if (rarityFilter.value !== 'all') {
    filtered = filtered.filter(p => p.pokemon?.rarity === rarityFilter.value);
  }

  if (starFilter.value !== 'all') {
    const targetStar = parseInt(starFilter.value);
    filtered = filtered.filter(p => p.star === targetStar);
  }

  if (shinyFilter.value === 'shiny') {
    filtered = filtered.filter(p => p.pokemon?.is_shiny);
  } else if (shinyFilter.value === 'normal') {
    filtered = filtered.filter(p => !p.pokemon?.is_shiny);
  }

  return filtered;
});

const totalPages = computed(() => Math.ceil(filteredPokemons.value.length / itemsPerPage));
const paginatedPokemons = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredPokemons.value.slice(start, end);
});

const openUpgradeModal = (pokemon: PokedexEntry) => {
  selectedPokemon.value = pokemon;
  showUpgradeModal.value = true;
};

const closeUpgradeModal = () => {
  selectedPokemon.value = null;
  showUpgradeModal.value = false;
};

const showAlertMessage = (message: string, type: 'success' | 'error' = 'success') => {
  alertMessage.value = message;
  alertType.value = type;
  showAlert.value = true;
  setTimeout(() => {
    showAlert.value = false;
  }, 4000);
};

const onUpgradeSuccess = (upgradedPokemon: PokedexEntry) => {
  showUpgradeModal.value = false;
  
  selectedPokemon.value = upgradedPokemon;
  showSuccessModal.value = true;
  
  router.reload({
    onSuccess: () => {
      localUserPokemons.value = [...userPokemons];
      fetchUpgradablePokemons();
    }
  });
};

const goBack = () => {
  router.visit('/me');
};

const resetFilters = () => {
  searchQuery.value = '';
  rarityFilter.value = 'all';
  starFilter.value = 'all';
  shinyFilter.value = 'all';
  upgradableFilter.value = 'upgradable';
  currentPage.value = 1;
};

const changePage = (page: number) => {
  currentPage.value = page;
};

const fetchUpgradablePokemons = async () => {
  loading.value = true;
  try {
    const response = await fetch(route('upgradable-pokemons'));
    const data = await response.json();
    upgradableIds.value = data.upgradableIds || [];
    errors.value = data.errors || {};
  } catch (error) {
    console.error('Erreur lors de la récupération des Pokémon upgradables:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUpgradablePokemons();
  
  if (flash?.success && flash?.upgraded_pokemon) {
    selectedPokemon.value = flash.upgraded_pokemon;
    showSuccessModal.value = true;
  }
});
</script>

<template>
  <Head title="Amélioration Pokémon" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="w-full max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 pb-16">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 sm:pt-6 mb-4 sm:mb-6">
          <div class="flex items-center gap-2 sm:gap-4">
            <Button @click="goBack" variant="secondary" size="sm" class="shrink-0">
              <ArrowLeft :size="16" class="inline" /> Retour
            </Button>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              <Star :size="24" class="inline" /> Amélioration Pokémon
            </h1>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 shadow-lg">
          <div class="p-3 sm:p-4 lg:p-6 border-b border-base-300/30 bg-base-200/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
              <div class="sm:col-span-2 lg:col-span-1 xl:col-span-2">
                <Input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher par nom ou N°..."
                  class="w-full h-[42px]"
                />
              </div>
              
              <Select
                v-model="rarityFilter"
                :options="rarityOptions"
                class="w-full h-[42px]"
              />
              
              <Select
                v-model="starFilter"
                :options="starOptions"
                class="w-full h-[42px]"
              />
              
              <Select
                v-model="shinyFilter"
                :options="shinyOptions"
                class="w-full h-[42px]"
              />

              <Select
                v-model="upgradableFilter"
                :options="upgradableOptions"
                class="w-full h-[42px]"
              />

              <Button @click="resetFilters" variant="outline" class="w-full h-[42px]">
                <RotateCcw :size="16" />
              </Button>
            </div>
          </div>

          <div class="flex-1 p-3 sm:p-4 lg:p-6 min-h-[600px]">
            <div v-if="loading" class="flex justify-center items-center py-12 sm:py-20">
              <div class="loading loading-spinner loading-lg"></div>
            </div>

            <div v-else-if="paginatedPokemons.length > 0" class="space-y-4 sm:space-y-6">
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-x-3 sm:gap-x-4 gap-y-6 sm:gap-y-8">
                <div v-for="pokemon in paginatedPokemons" :key="pokemon.id" 
                     class="cursor-pointer transform transition-transform hover:scale-105 relative"
                     @click="openUpgradeModal(pokemon)">
                  <PokemonCard :entry="pokemon" />
                  <div class="absolute -bottom-2 left-0 right-0 flex justify-center px-1">
                    <div v-if="upgradableIds.includes(pokemon.id)" class="w-full">
                      <span class="badge badge-success badge-xs sm:badge-sm w-full text-[10px] sm:text-xs truncate">
                        <span class="hidden sm:inline">Ressources disponibles</span>
                        <span class="sm:hidden">Disponible</span>
                      </span>
                    </div>
                    <div v-else class="w-full">
                      <span class="badge badge-error badge-xs sm:badge-sm w-full text-[10px] sm:text-xs truncate" :title="errors[pokemon.id]">
                        <span class="hidden sm:inline">Ressources insuffisantes</span>
                        <span class="sm:hidden">Insuffisant</span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-12 sm:py-20 text-center px-4">
              <Search :size="64" class="mx-auto mb-4 text-base-content/30" />
              <h3 class="text-lg sm:text-xl font-bold mb-2">Aucun Pokémon trouvé</h3>
              <p class="text-sm sm:text-base text-base-content/70 mb-4 max-w-md">Essayez de modifier vos filtres de recherche.</p>
              <Button @click="resetFilters" variant="primary">
                Réinitialiser les filtres
              </Button>
            </div>
          </div>

          <div v-if="totalPages > 1" class="p-3 sm:p-4 border-t border-base-300/30 bg-base-200/50">
            <div class="flex justify-center items-center gap-1 sm:gap-2">
              <Button 
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                variant="outline"
                size="sm"
                class="px-2 sm:px-3"
              >
                <ChevronLeft :size="16" />
              </Button>
              
              <div class="flex gap-1 items-center">
                <span class="text-sm text-base-content/70 px-2">
                  Page {{ currentPage }} sur {{ totalPages }}
                </span>
              </div>
              
              <Button 
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                variant="outline"
                size="sm"
                class="px-2 sm:px-3"
              >
                <ChevronRight :size="16" />
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Alert 
      v-if="showAlert" 
      :type="alertType" 
      :message="alertMessage" 
      @close="showAlert = false" 
      class="fixed top-4 right-4 z-50 max-w-[calc(100vw-2rem)] sm:max-w-md"
    />

    <UpgradeModal 
      v-if="selectedPokemon"
      :pokemon="selectedPokemon"
      :show="showUpgradeModal"
      @close="closeUpgradeModal"
      @success="onUpgradeSuccess"
      @error="(message) => showAlertMessage(message, 'error')"
    />

    <UpgradeSuccessModal 
      v-if="selectedPokemon"
      :pokemon="selectedPokemon"
      :show="showSuccessModal"
      @close="showSuccessModal = false"
    />
  </div>
</template>

<style scoped>
:deep(input),
:deep(select) {
  height: 42px !important;
  min-height: 42px !important;
  max-height: 42px !important;
  box-sizing: border-box !important;
}

.h-\[42px\] {
  height: 42px !important;
}
</style>
<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { getTypeColor, getRarityColor } from '@/utils/pokemon';
import { Zap, Home, Plus, Eye, Edit, Trash2, AlertTriangle, Search, Filter, RotateCcw, BarChart3, ChevronUp, ChevronDown, Sparkles, Hash, Star } from 'lucide-vue-next';

interface Pokemon {
  id: number;
  pokedex_id: number;
  name: string;
  types: Array<{ name: string; image: string }>;
  rarity: string;
  generation: number;
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  special_attack: number;
  special_defense: number;
  is_shiny: boolean;
}

interface PaginatedPokemons {
  data: Pokemon[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

const props = defineProps<{
  pokemons: PaginatedPokemons;
  stats: {
    total: number;
    filtered: number;
    by_rarity: Record<string, number>;
    by_generation: Record<string, number>;
    shiny_count: number;
    avg_stats: number;
  };
  filters?: {
    rarity?: string;
    search?: string;
    type?: string;
    shiny?: string;
  };
  sort?: {
    sort_by?: string;
    sort_direction?: string;
  };
  rarities: string[];
  types: string[];
}>();

const sortField = ref<string>('');
const sortDirection = ref<'asc' | 'desc'>('asc');
const rarityFilter = ref<string>(props.filters?.rarity || '');
const searchFilter = ref<string>(props.filters?.search || '');
const typeFilter = ref<string>(props.filters?.type || '');
const shinyFilter = ref<string>(props.filters?.shiny || '');

const currentSortBy = ref<string>(props.sort?.sort_by || 'pokedex_id');
const currentSortDirection = ref<string>(props.sort?.sort_direction || 'asc');

const sortedPokemons = computed(() => {
  if (!sortField.value) return props.pokemons.data;

  const sorted = [...props.pokemons.data].sort((a, b) => {
    let aValue = a[sortField.value as keyof Pokemon];
    let bValue = b[sortField.value as keyof Pokemon];

    if (aValue === null || aValue === undefined) aValue = '';
    if (bValue === null || bValue === undefined) bValue = '';

    if (typeof aValue === 'string' && typeof bValue === 'string') {
      aValue = aValue.toLowerCase();
      bValue = bValue.toLowerCase();
    }

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });

  return sorted;
});

const showDeleteModal = ref(false);
const pokemonToDelete = ref<Pokemon | null>(null);

const deletePokemon = (pokemonId: number) => {
  const pokemon = props.pokemons.data.find(p => p.id === pokemonId);
  if (pokemon) {
    pokemonToDelete.value = pokemon;
    showDeleteModal.value = true;
  }
};

const confirmDelete = () => {
  if (pokemonToDelete.value) {
    router.delete(`/admin/pokemons/${pokemonToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false;
        pokemonToDelete.value = null;
      }
    });
  }
};

const sortBy = (field: string) => {
  if (currentSortBy.value === field) {
    currentSortDirection.value = currentSortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    currentSortBy.value = field;
    currentSortDirection.value = 'asc';
  }
  applyFilters();
};

const getSortIcon = (field: string) => {
  if (currentSortBy.value !== field) return '↕️';
  return currentSortDirection.value === 'asc' ? '↑' : '↓';
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (rarityFilter.value) {
    params.append('rarity', rarityFilter.value);
  }

  if (searchFilter.value) {
    params.append('search', searchFilter.value);
  }

  if (typeFilter.value) {
    params.append('type', typeFilter.value);
  }

  if (shinyFilter.value !== '') {
    params.append('shiny', shinyFilter.value);
  }

  if (currentSortBy.value) {
    params.append('sort_by', currentSortBy.value);
  }

  if (currentSortDirection.value) {
    params.append('sort_direction', currentSortDirection.value);
  }

  router.get(`/admin/pokemons?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  sortField.value = '';
  sortDirection.value = 'asc';
  rarityFilter.value = '';
  searchFilter.value = '';
  typeFilter.value = '';
  shinyFilter.value = '';
  currentSortBy.value = 'pokedex_id';
  currentSortDirection.value = 'asc';
  router.get('/admin/pokemons', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getRarityLabel = (rarity: string) => {
  const labels: Record<string, string> = {
    normal: 'Normal',
    rare: 'Rare',
    epic: 'Épique',
    legendary: 'Légendaire'
  };
  return labels[rarity] || rarity;
};

const getTotalStats = (pokemon: Pokemon) => {
  return pokemon.hp + pokemon.attack + pokemon.defense + pokemon.speed + pokemon.special_attack + pokemon.special_defense;
};

const calculateCP = (pokemon: Pokemon) => {
  const baseCP = pokemon.hp + pokemon.attack + pokemon.defense +
    pokemon.special_attack + pokemon.special_defense + pokemon.speed;
  let finalCP = baseCP;
  if (pokemon.is_shiny) {
    finalCP = Math.floor(finalCP * 1.1);
  }
  const multipliers: Record<string, number> = {
    normal: 1.10,
    rare: 1.50,
    epic: 2.25,
    legendary: 4.0
  };
  const rarityMultiplier = multipliers[pokemon.rarity] || 1.10;
  finalCP = Math.floor(finalCP * rarityMultiplier);
  return finalCP * 10;
};

const getPokemonImage = (pokemon: Pokemon) => {
  const suffix = pokemon.is_shiny ? '_S' : '';
  return `/images/pokemon-gifs/${pokemon.pokedex_id}${suffix}.gif`;
};
</script>

<template>
  <Head title="Gestion des Pokémon" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            GESTION DES POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ stats.filtered }} / {{ stats.total }} Pokémon affichés
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Zap" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button
                    @click="router.visit('/admin/pokemons/create')"
                    variant="secondary"
                    size="sm"
                    class="w-full justify-start"
                  >
                    <component :is="Plus" :size="16" class="mr-2" /> Nouveau Pokémon
                  </Button>
                  <Button
                    @click="router.visit('/admin')"
                    variant="outline"
                    size="sm"
                    class="w-full justify-start"
                  >
                    <component :is="Home" :size="16" class="mr-2" /> Dashboard
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Total</span>
                    <span class="text-sm font-bold text-info">{{ stats.total }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Affichés</span>
                    <span class="text-sm font-bold text-success">{{ stats.filtered }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Shiny</span>
                    <span class="text-sm font-bold text-warning">{{ stats.shiny_count }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Stats moy.</span>
                    <span class="text-sm font-bold text-primary">{{ Math.round(stats.avg_stats) }}</span>
                  </div>
                  <div class="border-t border-base-300/30 pt-2">
                    <div class="text-xs text-base-content/70 mb-2">Par rareté:</div>
                    <div v-for="(count, rarity) in stats.by_rarity" :key="rarity" class="flex justify-between items-center">
                      <span class="text-xs text-base-content/70">{{ getRarityLabel(rarity) }}</span>
                      <span class="text-sm font-bold">{{ count }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Filter" :size="18" />
                    FILTRES
                  </h3>
                </div>
                <div class="p-3 space-y-3">
                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Rareté</label>
                    <select
                      v-model="rarityFilter"
                      @change="applyFilters"
                      class="w-full px-3 py-2 bg-base-300 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    >
                      <option value="">Toutes</option>
                      <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                        {{ getRarityLabel(rarity) }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Type</label>
                    <select
                      v-model="typeFilter"
                      @change="applyFilters"
                      class="w-full px-3 py-2 bg-base-300 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    >
                      <option value="">Tous</option>
                      <option v-for="type in types" :key="type" :value="type">
                        {{ type }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Shiny</label>
                    <select
                      v-model="shinyFilter"
                      @change="applyFilters"
                      class="w-full px-3 py-2 bg-base-300 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    >
                      <option value="">Tous</option>
                      <option value="true">Shiny uniquement</option>
                      <option value="false">Non-shiny uniquement</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Recherche</label>
                    <input
                      v-model="searchFilter"
                      @keyup.enter="applyFilters"
                      type="text"
                      placeholder="Nom ou ID..."
                      class="w-full px-3 py-2 bg-base-200/50 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    />
                  </div>
                  <div class="flex gap-2">
                    <Button @click="applyFilters" variant="primary" size="sm" class="flex-1">
                      <component :is="Search" :size="14" class="mr-1" />
                      Filtrer
                    </Button>
                    <Button @click="clearFilters" variant="ghost" size="sm">
                      <component :is="RotateCcw" :size="14" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-base-content/70">Pokémon:</span>
                    <span class="text-sm font-bold">{{ sortedPokemons.length }}</span>
                    <span class="text-xs text-base-content/50">sur {{ stats.total }}</span>
                  </div>
                  <Button
                    @click="router.visit('/admin/pokemons/create')"
                    variant="primary"
                    size="sm"
                  >
                    <component :is="Plus" :size="16" class="mr-2" />
                    Créer
                  </Button>
                </div>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-4">
                  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                      v-for="pokemon in sortedPokemons"
                      :key="pokemon.id"
                      class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-4 hover:border-primary/40 transition-all duration-200 flex flex-col justify-between min-h-[320px] group hover:scale-105"
                    >
                      <div>
                        <div class="flex items-start gap-3 mb-3">
                          <div class="flex-shrink-0">
                            <div class="w-16 h-16 flex items-center justify-center overflow-hidden">
                              <img
                                :src="getPokemonImage(pokemon)"
                                :alt="pokemon.name"
                                class="w-full h-full object-contain"
                                @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                              />
                            </div>
                          </div>
                          <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                              <h3 class="font-semibold text-lg group-hover:text-primary transition-colors">
                                {{ pokemon.name }}
                              </h3>
                              <span v-if="pokemon.is_shiny" class="text-yellow-500">
                                <component :is="Sparkles" :size="16" />
                              </span>
                            </div>
                            <div class="flex items-center gap-1 mb-2">
                              <component :is="Hash" :size="12" class="text-base-content/50" />
                              <span class="text-xs text-base-content/70 font-mono">
                                #{{ pokemon.pokedex_id.toString().padStart(3, '0') }}
                              </span>
                            </div>
                            <div class="flex gap-1 mb-2">
                              <span
                                v-for="type in pokemon.types"
                                :key="type.name"
                                :class="`badge badge-xs bg-gradient-to-r ${getTypeColor(type.name)} text-white border-0`"
                              >
                                {{ type.name }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="space-y-2 mt-auto">
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Rareté:</span>
                          <span
                            :class="`badge badge-xs bg-gradient-to-r ${getRarityColor(pokemon.rarity)} text-white border-0`"
                          >
                            {{ getRarityLabel(pokemon.rarity) }}
                          </span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Génération:</span>
                          <span class="text-xs text-base-content/70">{{ pokemon.generation || '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">Stats Total:</span>
                          <span class="text-xs text-base-content/70">{{ getTotalStats(pokemon) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium">CP:</span>
                          <span class="text-xs text-base-content/70">{{ calculateCP(pokemon) }}</span>
                        </div>
                        <div class="flex gap-2 pt-2">
                          <Button
                            @click="router.visit(`/admin/pokemons/${pokemon.id}`)"
                            variant="ghost"
                            size="sm"
                            class="flex-1"
                            title="Voir le Pokémon"
                          >
                            <component :is="Eye" :size="16" />
                          </Button>
                          <Button
                            @click="router.visit(`/admin/pokemons/${pokemon.id}/edit`)"
                            variant="secondary"
                            size="sm"
                            class="flex-1"
                            title="Modifier le Pokémon"
                          >
                            <component :is="Edit" :size="16" />
                          </Button>
                          <Button
                            @click="deletePokemon(pokemon.id)"
                            variant="error"
                            size="sm"
                            class="flex-1"
                            title="Supprimer le Pokémon"
                          >
                            <component :is="Trash2" :size="16" />
                          </Button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-if="pokemons.links.length > 3" class="mt-6 flex justify-center">
                    <div class="flex gap-1">
                      <Button
                        v-for="link in pokemons.links"
                        :key="link.label"
                        @click="link.url ? router.visit(link.url) : null"
                        :disabled="!link.url || link.active"
                        :variant="link.active ? 'primary' : 'outline'"
                        size="sm"
                        class="min-w-[40px]"
                      >
                        <span v-html="link.label"></span>
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <component :is="AlertTriangle" :size="24" class="text-error" />
          </div>
          <div class="ml-3">
            <h3 class="text-lg font-medium text-base-content">Confirmer la suppression</h3>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-sm text-base-content/70">
            Êtes-vous sûr de vouloir supprimer le Pokémon
            <strong>{{ pokemonToDelete?.name }}</strong>
            ? Cette action est irréversible.
          </p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <Button variant="ghost" @click="showDeleteModal = false">Annuler</Button>
          <Button variant="error" @click="confirmDelete">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>

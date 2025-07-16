<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { getTypeColor, getRarityColor } from '@/utils/pokemon';

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

const deletePokemon = (pokemonId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce Pokémon ?')) {
    router.delete(`/admin/pokemons/${pokemonId}`, {
      preserveScroll: true,
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
  
  <div class="h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative overflow-hidden">
    <BackgroundEffects />

    <div class="relative z-10 h-full w-full flex flex-col">
      <div class="flex justify-center pt-4 mb-4 flex-shrink-0">
        <div class="text-center">
          <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent mb-1 tracking-wider">
            ⚡ GESTION POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ stats.filtered }} / {{ stats.total }} Pokémon affichés
          </p>
        </div>
      </div>

      <div class="flex-1 flex flex-col lg:flex-row gap-4 px-2 md:px-4 lg:px-8 min-h-0 pb-4">
        <!-- Main Table Container -->
        <div class="flex-1 lg:mr-4 flex flex-col min-h-0" style="max-height: 85vh;">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-t-xl border border-b-0 border-base-300/30 p-4 flex-shrink-0">
            <div class="flex flex-wrap gap-4 items-center">
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Rareté:</label>
                <select 
                  v-model="rarityFilter"
                  @change="applyFilters"
                  class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[120px]"
                >
                  <option value="">Toutes</option>
                  <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                    {{ getRarityLabel(rarity) }}
                  </option>
                </select>
              </div>
              


              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Type:</label>
                <select 
                  v-model="typeFilter"
                  @change="applyFilters"
                  class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[120px]"
                >
                  <option value="">Tous</option>
                  <option v-for="type in types" :key="type" :value="type">
                    {{ type }}
                  </option>
                </select>
              </div>

              <div class="flex items-center gap-2">
                <input
                  v-model="searchFilter"
                  @keyup.enter="applyFilters"
                  type="text"
                  placeholder="Rechercher par nom ou ID..."
                  class="input input-sm input-bordered bg-base-100/80 border-base-300/50 text-sm w-48"
                />
                <Button @click="applyFilters" size="sm" variant="outline">
                  🔍
                </Button>
              </div>

              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Shiny:</label>
                <select 
                  v-model="shinyFilter"
                  @change="applyFilters"
                  class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[100px]"
                >
                  <option value="">Tous</option>
                  <option value="true">Shiny uniquement</option>
                  <option value="false">Non-shiny uniquement</option>
                </select>
              </div>

              <Button @click="clearFilters" size="sm" variant="ghost">
                🗑️ Effacer
              </Button>
            </div>


          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-b-xl border border-base-300/30 flex-1 overflow-hidden">
            <div class="overflow-x-auto h-full">
              <table class="table table-zebra w-full">
                <thead class="bg-base-200 sticky top-0">
                  <tr>
                    <th class="cursor-pointer" @click="sortBy('pokedex_id')">
                      ID {{ getSortIcon('pokedex_id') }}
                    </th>
                    <th>Image</th>
                    <th class="cursor-pointer" @click="sortBy('name')">
                      Nom {{ getSortIcon('name') }}
                    </th>
                    <th>Types</th>
                    <th class="cursor-pointer" @click="sortBy('rarity')">
                      Rareté {{ getSortIcon('rarity') }}
                    </th>
                    <th class="cursor-pointer" @click="sortBy('generation')">
                      Gén. {{ getSortIcon('generation') }}
                    </th>
                    <th>Stats Total</th>
                    <th>CP</th>
                    <th>Shiny</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pokemon in sortedPokemons" :key="pokemon.id" class="hover:bg-base-200/30">
                    <td class="font-mono text-sm">#{{ pokemon.pokedex_id.toString().padStart(3, '0') }}</td>
                    <td>
                      <img 
                        :src="getPokemonImage(pokemon)" 
                        :alt="pokemon.name"
                        class="w-12 h-12 object-contain"
                        @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                      />
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <span class="font-semibold">{{ pokemon.name }}</span>
                        <span v-if="pokemon.is_shiny" class="text-yellow-500">✨</span>
                      </div>
                    </td>
                    <td>
                      <div class="flex gap-1">
                        <span
                          v-for="type in pokemon.types"
                          :key="type.name"
                          :class="`badge badge-sm bg-gradient-to-r ${getTypeColor(type.name)} text-white border-0`"
                        >
                          {{ type.name }}
                        </span>
                      </div>
                    </td>
                    <td>
                      <span
                        :class="`badge badge-sm bg-gradient-to-r ${getRarityColor(pokemon.rarity)} text-white border-0`"
                      >
                        {{ getRarityLabel(pokemon.rarity) }}
                      </span>
                    </td>
                    <td class="text-center">{{ pokemon.generation || '-' }}</td>
                    <td class="text-center font-mono text-sm">
                      {{ getTotalStats(pokemon) }}
                    </td>
                    <td class="text-center font-mono text-sm">
                      {{ calculateCP(pokemon) }}
                    </td>
                    <td class="text-center">
                      <span v-if="pokemon.is_shiny" class="text-yellow-500">✨</span>
                      <span v-else class="text-base-content/30">-</span>
                    </td>
                    <td>
                      <div class="flex gap-1">
                        <Button
                          @click="router.visit(`/admin/pokemons/${pokemon.id}`)"
                          size="sm"
                          variant="ghost"
                        >
                          👁️
                        </Button>
                        <Button
                          @click="router.visit(`/admin/pokemons/${pokemon.id}/edit`)"
                          size="sm"
                          variant="ghost"
                        >
                          ✏️
                        </Button>
                        <Button
                          @click="deletePokemon(pokemon.id)"
                          size="sm"
                          variant="ghost"
                          class="text-error hover:text-error"
                        >
                          🗑️
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pokemons.links.length > 3" class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 mt-4">
            <div class="flex justify-between items-center">
              <div class="text-sm text-base-content/70">
                Affichage de {{ (pokemons.current_page - 1) * pokemons.per_page + 1 }} à 
                {{ Math.min(pokemons.current_page * pokemons.per_page, pokemons.total) }} 
                sur {{ pokemons.total }} Pokémon
              </div>
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

        <!-- Sidebar -->
        <div class="lg:w-80 flex flex-col gap-4 flex-shrink-0">
          <!-- Stats -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📊</span>
                STATISTIQUES
              </h3>
            </div>
            <div class="p-3 space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Total</span>
                <span class="text-sm font-bold text-error">{{ stats.total }}</span>
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
                <span class="text-sm font-bold text-info">{{ Math.round(stats.avg_stats) }}</span>
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

          <!-- Actions -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">⚡</span>
                ACTIONS
              </h3>
            </div>
            <div class="p-3 space-y-2">
              <Button
                @click="router.visit('/admin/pokemons/create')"
                variant="primary"
                size="sm"
                class="w-full"
              >
                ➕ Nouveau Pokémon
              </Button>
              <div class="border-t border-base-300/30 pt-2">
                <Button
                  @click="router.visit('/admin')"
                  variant="ghost"
                  size="sm"
                  class="w-full"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 
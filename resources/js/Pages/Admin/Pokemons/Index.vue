<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
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

const rarityFilter = ref(props.filters?.rarity || '');
const searchTerm = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');
const shinyFilter = ref(props.filters?.shiny || '');
const showDeleteModal = ref(false);
const pokemonToDelete = ref<Pokemon | null>(null);

const getRarityLabel = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
};

const rarityOptions = [
  { value: '', label: 'Toutes raretés' },
  ...props.rarities.map(rarity => ({ value: rarity, label: getRarityLabel(rarity) }))
];

const typeOptions = [
  { value: '', label: 'Tous types' },
  ...props.types.map(type => ({ value: type, label: type }))
];

const shinyOptions = [
  { value: '', label: 'Tous' },
  { value: '1', label: 'Shiny' },
  { value: '0', label: 'Normal' }
];

const filteredPokemons = computed(() => {
  return props.pokemons.data.filter(pokemon => {
    const matchesSearch = pokemon.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      pokemon.pokedex_id.toString().includes(searchTerm.value);
    return matchesSearch;
  });
});

const deletePokemon = (pokemon: Pokemon) => {
  pokemonToDelete.value = pokemon;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (pokemonToDelete.value) {
    router.delete(`/admin/pokemons/${pokemonToDelete.value.id}`, {
      preserveScroll: true,
      onFinish: () => {
        showDeleteModal.value = false;
        pokemonToDelete.value = null;
      }
    });
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  pokemonToDelete.value = null;
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (rarityFilter.value) {
    params.append('rarity', rarityFilter.value);
  }

  if (typeFilter.value) {
    params.append('type', typeFilter.value);
  }

  if (shinyFilter.value) {
    params.append('shiny', shinyFilter.value);
  }

  router.get(`/admin/pokemons?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  rarityFilter.value = '';
  searchTerm.value = '';
  typeFilter.value = '';
  shinyFilter.value = '';
  router.get('/admin/pokemons', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getRarityBadgeColor = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'text-base-content bg-base-200/50 border-base-300/30';
  case 'rare': return 'text-info bg-info/10 border-info/30';
  case 'epic': return 'text-warning bg-warning/10 border-warning/30';
  case 'legendary': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content bg-base-200/50 border-base-300/30';
  }
};

const getPokemonImage = (pokemon: Pokemon) => {
  const suffix = pokemon.is_shiny ? '_S' : '';
  return `/images/pokemon-gifs/${pokemon.pokedex_id}${suffix}.gif`;
};

const getTotalStats = (pokemon: Pokemon) => {
  return pokemon.hp + pokemon.attack + pokemon.defense + pokemon.speed + pokemon.special_attack + pokemon.special_defense;
};
</script>

<template>
  <Head title="Gestion des Pokémon" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ⚡ GESTION POKÉMON
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ props.pokemons.total }} pokémon{{ props.pokemons.total > 1 ? 's' : '' }} enregistré{{ props.pokemons.total > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    LISTE DES POKÉMON
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Select
                        v-model="rarityFilter"
                        @change="applyFilters"
                        :options="rarityOptions"
                        class="w-32"
                      />

                      <Select
                        v-model="typeFilter"
                        @change="applyFilters"
                        :options="typeOptions"
                        class="w-32"
                      />

                      <Select
                        v-model="shinyFilter"
                        @change="applyFilters"
                        :options="shinyOptions"
                        class="w-24"
                      />

                      <Button @click="clearFilters" variant="outline" size="sm">
                        ✨ Reset
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gradient-to-r from-base-200/50 to-base-300/30 border-b border-base-300/30">
                    <tr>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Pokémon
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Types
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Rareté
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Stats
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Génération
                      </th>
                      <th class="px-6 py-4 text-center text-sm font-bold text-base-content uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-base-300/30">
                    <tr
                      v-for="pokemon in filteredPokemons"
                      :key="pokemon.id"
                      class="hover:bg-base-200/30 transition-colors duration-200"
                    >
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="relative w-12 h-12 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center overflow-hidden">
                            <img
                              :src="getPokemonImage(pokemon)"
                              :alt="pokemon.name"
                              class="w-full h-full object-contain"
                              @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                            />
                            <div v-if="pokemon.is_shiny" class="absolute -top-1 -right-1 text-lg">✨</div>
                          </div>
                          <div>
                            <div class="font-semibold text-base-content">{{ pokemon.name }}</div>
                            <div class="text-sm text-base-content/70">#{{ pokemon.pokedex_id }}</div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex gap-1 flex-wrap">
                          <span
                            v-for="type in pokemon.types"
                            :key="type.name"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            :class="getTypeColor(type.name)"
                          >
                            {{ type.name }}
                          </span>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getRarityBadgeColor(pokemon.rarity)
                        ]">
                          {{ getRarityLabel(pokemon.rarity) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm">
                          <div class="font-semibold text-primary">{{ getTotalStats(pokemon) }}</div>
                          <div class="text-base-content/70">Total</div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="font-semibold text-secondary">
                          Gen {{ pokemon.generation }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                          <Button
                            @click="router.visit(`/admin/pokemons/${pokemon.id}`)"
                            variant="ghost"
                            size="sm"
                            class="text-info hover:text-info hover:bg-info/20 transition-colors"
                            title="Voir"
                          >
                            👁️
                          </Button>
                          <Button
                            @click="router.visit(`/admin/pokemons/${pokemon.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/20 transition-colors"
                            title="Modifier"
                          >
                            ✏️
                          </Button>
                          <Button
                            @click="deletePokemon(pokemon)"
                            variant="ghost"
                            size="sm"
                            class="text-error hover:text-error hover:bg-error/20 transition-colors"
                            title="Supprimer"
                          >
                            🗑️
                          </Button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="props.pokemons.last_page > 1" class="p-4 bg-gradient-to-r from-error/5 to-error/10 border-t border-error/20">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                  <template v-for="link in props.pokemons.links" :key="link.label">
                    <Button
                      v-if="link.url"
                      @click="router.visit(link.url)"
                      :variant="link.active ? 'primary' : 'ghost'"
                      size="sm"
                      class="min-w-[2.5rem]"
                      v-html="link.label"
                    />
                    <span v-else class="px-3 py-2 text-base-content/50 text-sm" v-html="link.label" />
                  </template>
                </div>
                <div class="text-xs text-center text-base-content/70 mt-2">
                  Affichage de {{ (props.pokemons.current_page - 1) * props.pokemons.per_page + 1 }}
                  à {{ Math.min(props.pokemons.current_page * props.pokemons.per_page, props.pokemons.total) }}
                  sur {{ props.pokemons.total }} résultats
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/pokemons/create')"
                  variant="secondary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ➕ Nouveau Pokémon
                </Button>
                <Button
                  @click="router.visit('/admin/')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  ← Dashboard
                </Button>
                <Button
                  @click="router.visit('/me')"
                  variant="ghost"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-error">{{ props.stats.total }}</div>
                  <div class="text-sm text-base-content/70">Total Pokémon</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-warning">{{ props.stats.shiny_count }}</div>
                    <div class="text-xs text-base-content/70">Shiny</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-info">{{ Math.round(props.stats.avg_stats) }}</div>
                    <div class="text-xs text-base-content/70">Stats moy.</div>
                  </div>
                </div>
                <div class="space-y-2">
                  <div class="text-xs text-base-content/70 mb-2">Par rareté:</div>
                  <div v-for="(count, rarity) in props.stats.by_rarity" :key="rarity" class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">{{ getRarityLabel(rarity) }}</span>
                    <span class="text-sm font-bold">{{ count }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="cancelDelete" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-error/20 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚠️</span>
          </div>
          <h3 class="text-xl font-bold text-base-content">Supprimer le Pokémon</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer le Pokémon
          <span class="font-bold text-error">{{ pokemonToDelete?.name }}</span> ?
        </p>
        <p class="text-sm text-base-content/60">
          Cette action est irréversible et supprimera toutes les données associées.
        </p>

        <div class="flex gap-3 pt-4">
          <Button @click="confirmDelete" variant="outline" class="flex-1 border-error text-error hover:bg-error hover:text-error-content">
            🗑️ Supprimer
          </Button>
          <Button @click="cancelDelete" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>

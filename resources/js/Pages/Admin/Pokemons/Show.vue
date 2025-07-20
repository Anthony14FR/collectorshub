<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { getTypeColor, getRarityColor } from '@/utils/pokemon';

interface Pokemon {
  id: number;
  pokedex_id: number;
  name: string;
  types: Array<{ name: string; image: string }>;
  resistances: Array<{ name: string; damage_multiplier: number; damage_relation: string }>;
  evolution: { id: number; name: string; pokedex_id: number } | null;
  pre_evolution: { id: number; name: string; pokedex_id: number } | null;
  description: string;
  height: number;
  weight: number;
  rarity: string;
  is_shiny: boolean;
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  special_attack: number;
  special_defense: number;
  generation: number | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  pokemon: Pokemon;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deletePokemon = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/pokemons/${props.pokemon.id}`, {
    onSuccess: () => router.visit('/admin/pokemons'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
};

const getRarityLabel = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
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

const getResistanceColor = (relation: string) => {
  switch (relation) {
  case 'resistant': return 'text-success bg-success/10 border-success/30';
  case 'vulnerable': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content bg-base-200/50 border-base-300/30';
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getTotalStats = () => {
  return props.pokemon.hp + props.pokemon.attack + props.pokemon.defense +
    props.pokemon.speed + props.pokemon.special_attack + props.pokemon.special_defense;
};

const getPokemonImage = (pokemon: Pokemon) => {
  const suffix = pokemon.is_shiny ? '_S' : '';
  return `/images/pokemon-gifs/${pokemon.pokedex_id}${suffix}.gif`;
};

const getEvolutionImage = (pokemon: { pokedex_id: number }) => {
  return `/images/pokemon-gifs/${pokemon.pokedex_id}.gif`;
};

const resistantResistances = computed(() => {
  return props.pokemon.resistances.filter(r => r.damage_relation === 'resistant');
});

const vulnerableResistances = computed(() => {
  return props.pokemon.resistances.filter(r => r.damage_relation === 'vulnerable');
});
</script>

<template>
  <Head :title="`Pokémon: ${pokemon.name}`" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent mb-2 tracking-wider">
            👁️ DÉTAILS POKÉMON
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Informations complètes de {{ props.pokemon.name }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <div class="flex items-center gap-4">
                  <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center overflow-hidden">
                    <img
                      :src="getPokemonImage(pokemon)"
                      :alt="pokemon.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                    <div v-if="pokemon.is_shiny" class="absolute -top-1 -right-1 text-2xl">✨</div>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-base-content mb-1">{{ props.pokemon.name }}</h3>
                    <p class="text-base-content/70 mb-2">#{{ props.pokemon.pokedex_id }}</p>
                    <div class="flex gap-3 flex-wrap">
                      <span
                        v-for="type in props.pokemon.types"
                        :key="type.name"
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                          getTypeColor(type.name)
                        ]"
                      >
                        {{ type.name }}
                      </span>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getRarityBadgeColor(props.pokemon.rarity)
                      ]">
                        {{ getRarityLabel(props.pokemon.rarity) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-success/10 to-success/5 rounded-xl p-6 border border-success/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-success mb-2">{{ props.pokemon.hp }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">PV</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-error/10 to-error/5 rounded-xl p-6 border border-error/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-error mb-2">{{ props.pokemon.attack }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Attaque</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-6 border border-warning/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-warning mb-2">{{ props.pokemon.defense }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Défense</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-6 border border-info/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-info mb-2">{{ props.pokemon.speed }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Vitesse</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl p-6 border border-secondary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-secondary mb-2">{{ props.pokemon.special_attack }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Att. Spé</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl p-6 border border-primary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-primary mb-2">{{ props.pokemon.special_defense }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Déf. Spé</div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📋 Informations générales
                    </h4>

                    <div class="space-y-4">
                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Description</span>
                        <span class="font-medium text-right max-w-xs">{{ props.pokemon.description || 'Aucune description' }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Taille</span>
                        <span class="font-medium">{{ props.pokemon.height }}m</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Poids</span>
                        <span class="font-medium">{{ props.pokemon.weight }}kg</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Génération</span>
                        <span class="font-medium">{{ props.pokemon.generation || 'Inconnue' }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Total des stats</span>
                        <span class="font-medium text-success">{{ getTotalStats() }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      ⚔️ Résistances
                    </h4>

                    <div class="space-y-4">
                      <div v-if="resistantResistances.length > 0">
                        <h5 class="text-sm font-semibold text-success mb-2">Résistant à:</h5>
                        <div class="grid grid-cols-2 gap-2">
                          <div
                            v-for="resistance in resistantResistances"
                            :key="`resistant-${resistance.name}`"
                            :class="[
                              'p-2 rounded-lg border text-center text-xs',
                              getResistanceColor(resistance.damage_relation)
                            ]"
                          >
                            <div class="font-semibold">{{ resistance.name }}</div>
                            <div class="text-xs">×{{ resistance.damage_multiplier }}</div>
                          </div>
                        </div>
                      </div>

                      <div v-if="vulnerableResistances.length > 0">
                        <h5 class="text-sm font-semibold text-error mb-2">Vulnérable à:</h5>
                        <div class="grid grid-cols-2 gap-2">
                          <div
                            v-for="resistance in vulnerableResistances"
                            :key="`vulnerable-${resistance.name}`"
                            :class="[
                              'p-2 rounded-lg border text-center text-xs',
                              getResistanceColor(resistance.damage_relation)
                            ]"
                          >
                            <div class="font-semibold">{{ resistance.name }}</div>
                            <div class="text-xs">×{{ resistance.damage_multiplier }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="props.pokemon.evolution || props.pokemon.pre_evolution" class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    🔗 Évolutions
                  </h4>

                  <div class="flex items-center justify-center gap-8">
                    <div v-if="props.pokemon.pre_evolution" class="text-center">
                      <div class="relative w-20 h-20 mx-auto mb-2">
                        <img
                          :src="getEvolutionImage(props.pokemon.pre_evolution)"
                          :alt="props.pokemon.pre_evolution.name"
                          class="w-full h-full object-contain"
                          @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                        />
                      </div>
                      <div class="text-sm font-semibold">{{ props.pokemon.pre_evolution.name }}</div>
                      <div class="text-xs text-base-content/60">#{{ props.pokemon.pre_evolution.pokedex_id }}</div>
                    </div>

                    <div class="text-center">
                      <div class="relative w-20 h-20 mx-auto mb-2">
                        <img
                          :src="getPokemonImage(props.pokemon)"
                          :alt="props.pokemon.name"
                          class="w-full h-full object-contain"
                          @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                        />
                        <div v-if="props.pokemon.is_shiny" class="absolute -top-1 -right-1 text-lg">✨</div>
                      </div>
                      <div class="text-sm font-semibold">{{ props.pokemon.name }}</div>
                      <div class="text-xs text-base-content/60">#{{ props.pokemon.pokedex_id }}</div>
                    </div>

                    <div v-if="props.pokemon.evolution" class="text-center">
                      <div class="relative w-20 h-20 mx-auto mb-2">
                        <img
                          :src="getEvolutionImage(props.pokemon.evolution)"
                          :alt="props.pokemon.evolution.name"
                          class="w-full h-full object-contain"
                          @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                        />
                      </div>
                      <div class="text-sm font-semibold">{{ props.pokemon.evolution.name }}</div>
                      <div class="text-xs text-base-content/60">#{{ props.pokemon.evolution.pokedex_id }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/pokemons/${props.pokemon.id}/edit`)"
                  variant="primary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ✏️ Modifier le Pokémon
                </Button>
                <Button
                  @click="router.visit('/admin/pokemons')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste Pokémon
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  RÉSUMÉ
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div>
                    <div class="text-xl font-bold text-info">{{ getTotalStats() }}</div>
                    <div class="text-xs text-base-content/70">Total stats</div>
                  </div>
                  <div>
                    <div class="text-xl font-bold text-success">{{ props.pokemon.generation || '?' }}</div>
                    <div class="text-xs text-base-content/70">Génération</div>
                  </div>
                </div>

                <div class="text-center pt-4 border-t border-base-300/30">
                  <div class="text-sm font-medium text-base-content mb-1">Type de Pokémon</div>
                  <div class="text-2xl font-bold text-primary">
                    {{ props.pokemon.is_shiny ? '✨' : '⭐' }}
                  </div>
                  <div class="text-xs text-base-content/60">
                    {{ props.pokemon.is_shiny ? 'Shiny' : 'Normal' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📅</span>
                  HISTORIQUE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-sm text-base-content/70 mb-1">Créé le</div>
                  <div class="text-sm font-medium">{{ formatDate(props.pokemon.created_at) }}</div>
                </div>
                <div class="text-center">
                  <div class="text-sm text-base-content/70 mb-1">Modifié le</div>
                  <div class="text-sm font-medium">{{ formatDate(props.pokemon.updated_at) }}</div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚠️</span>
                  ZONE DANGER
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <p class="text-sm text-base-content/70">
                  Actions irréversibles sur ce Pokémon
                </p>
                <Button
                  @click="deletePokemon"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer le Pokémon
                </Button>
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
          <span class="font-bold text-error">{{ props.pokemon.name }}</span> ?
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

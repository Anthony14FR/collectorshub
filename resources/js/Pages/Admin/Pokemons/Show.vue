<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { getRarityColor } from '@/utils/pokemon';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Zap, ArrowLeft, Edit, Trash2, AlertTriangle, Home, BarChart3, Hash, Sparkles, Shield, ArrowRight, Users, Info } from 'lucide-vue-next';

interface Pokemon {
  id: number;
  pokedex_id: number;
  name: string;
  types: Array<{ name: string; image: string }>;
  resistances: Array<{ name: string; damage_multiplier: number; damage_relation: string }>;
  evolution_id: number | null;
  pre_evolution_id: number | null;
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
  evolution?: Pokemon;
  preEvolution?: Pokemon;
}

const { pokemon, evolutions, preEvolutions } = defineProps<{
  pokemon: Pokemon;
  evolutions: Pokemon[];
  preEvolutions: Pokemon[];
}>();

const showDeleteModal = ref(false);

const getRarityLabel = (rarity: string) => {
  const labels: Record<string, string> = {
    normal: 'Normal',
    rare: 'Rare',
    epic: 'Épique',
    legendary: 'Légendaire'
  };
  return labels[rarity] || rarity;
};

const getDamageRelationLabel = (relation: string) => {
  const labels: Record<string, string> = {
    neutral: 'Neutre',
    resistant: 'Résistant',
    vulnerable: 'Vulnérable',
    twice_resistant: 'Double Résistant'
  };
  return labels[relation] || relation;
};

const getTotalStats = (pokemon: Pokemon) => {
  return pokemon.hp + pokemon.attack + pokemon.defense + pokemon.speed + pokemon.special_attack + pokemon.special_defense;
};

const getPokemonImage = (pokemon: Pokemon) => {
  const suffix = pokemon.is_shiny ? '_S' : '';
  return `/images/pokemon-gifs/${pokemon.pokedex_id}${suffix}.gif`;
};

const getTypeImage = (typeName: string) => {
  const normalizedName = typeName
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[éèêë]/g, 'e')
  return `/images/types/${normalizedName}.png`;
};

const calculateCP = (pokemon: Pokemon) => {
  const baseCP = pokemon.hp + pokemon.attack + pokemon.defense +
    pokemon.special_attack + pokemon.special_defense + pokemon.speed;

  let finalCP = baseCP;

  if (pokemon.is_shiny) {
    finalCP = Math.floor(finalCP * 1.1);
  }

  const rarityMultiplier = getRarityMultiplier(pokemon.rarity);
  finalCP = Math.floor(finalCP * rarityMultiplier);

  return finalCP * 10;
};

const getRarityMultiplier = (rarity: string) => {
  const multipliers: Record<string, number> = {
    normal: 1.10,
    rare: 1.50,
    epic: 2.25,
    legendary: 4.0
  };
  return multipliers[rarity] || 1.10;
};

const deletePokemon = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/pokemons/${pokemon.id}`);
};
</script>

<template>
  <Head :title="`${pokemon.name} - Détails`" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            DÉTAILS POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Informations complètes
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
                  <Button @click="router.visit(`/admin/pokemons/${pokemon.id}/edit`)" variant="primary" size="sm" class="w-full justify-start">
                    <component :is="Edit" :size="16" class="mr-2" /> Modifier
                  </Button>
                  <Button @click="deletePokemon" variant="error" size="sm" class="w-full justify-start">
                    <component :is="Trash2" :size="16" class="mr-2" /> Supprimer
                  </Button>
                  <Button @click="router.visit('/admin/pokemons')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                  </Button>
                  <Button @click="router.visit('/admin')" variant="ghost" size="sm" class="w-full justify-start">
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
                <div class="p-3 space-y-3">
                  <div class="text-center">
                    <div class="text-2xl font-bold text-success">{{ getTotalStats(pokemon) }}</div>
                    <div class="text-xs text-base-content/70">Total des stats</div>
                  </div>
                  <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                      <span class="text-base-content/70">PV:</span>
                      <span class="font-semibold">{{ pokemon.hp }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Attaque:</span>
                      <span class="font-semibold">{{ pokemon.attack }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Défense:</span>
                      <span class="font-semibold">{{ pokemon.defense }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Vitesse:</span>
                      <span class="font-semibold">{{ pokemon.speed }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Att. Spé:</span>
                      <span class="font-semibold">{{ pokemon.special_attack }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Déf. Spé:</span>
                      <span class="font-semibold">{{ pokemon.special_defense }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Users" :size="18" />
                    ÉVOLUTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-3">
                  <div v-if="preEvolutions.length > 0" class="text-center">
                    <div class="text-xs text-base-content/70 mb-1">Pré-évolutions</div>
                    <div class="space-y-2">
                      <div v-for="preEvo in preEvolutions" :key="preEvo.id" class="text-center">
                        <div class="relative w-12 h-12 mx-auto mb-1">
                          <img :src="`/images/pokemon-gifs/${preEvo.pokedex_id}.gif`" :alt="preEvo.name"
                               class="w-full h-full object-contain"
                               @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }" />
                        </div>
                        <div class="text-sm font-semibold">{{ preEvo.name }}</div>
                        <div class="text-xs text-base-content/60">#{{ preEvo.pokedex_id }}</div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center">
                    <div class="text-xs text-base-content/70 mb-1">Pré-évolutions</div>
                    <div class="text-sm text-base-content/50">Aucune</div>
                  </div>

                  <div class="border-t border-base-300/30 pt-3">
                    <div v-if="evolutions.length > 0" class="text-center">
                      <div class="text-xs text-base-content/70 mb-1">Évolutions</div>
                      <div class="space-y-2">
                        <div v-for="evo in evolutions" :key="evo.id" class="text-center">
                          <div class="relative w-12 h-12 mx-auto mb-1">
                            <img :src="`/images/pokemon-gifs/${evo.pokedex_id}.gif`" :alt="evo.name"
                                 class="w-full h-full object-contain"
                                 @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }" />
                          </div>
                          <div class="text-sm font-semibold">{{ evo.name }}</div>
                          <div class="text-xs text-base-content/60">#{{ evo.pokedex_id }}</div>
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-center">
                      <div class="text-xs text-base-content/70 mb-1">Évolutions</div>
                      <div class="text-sm text-base-content/50">Aucune</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Zap" :size="18" />
                  INFORMATIONS DÉTAILLÉES
                </h3>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div class="text-center mb-6">
                  <div class="relative w-32 h-32 mx-auto mb-3">
                    <img :src="getPokemonImage(pokemon)" :alt="pokemon.name" class="w-full h-full object-contain"
                         @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }" />
                    <div v-if="pokemon.is_shiny" class="absolute -top-2 -right-2">
                      <component :is="Sparkles" :size="24" class="text-yellow-500" />
                    </div>
                  </div>
                  <h2 class="text-2xl font-bold mb-1">{{ pokemon.name }}</h2>
                  <div class="text-sm text-base-content/60 mb-3 flex items-center justify-center gap-1">
                    <component :is="Hash" :size="14" />
                    #{{ pokemon.pokedex_id.toString().padStart(3, '0') }}
                  </div>

                  <div class="flex justify-center gap-2 mb-3">
                    <div v-for="type in pokemon.types" :key="type.name"
                         class="flex items-center gap-1 bg-base-200/50 rounded-lg px-3 py-1 border border-base-300/30">
                      <img :src="getTypeImage(type.name)" :alt="type.name" class="w-4 h-4 object-contain" />
                      <span class="text-sm font-semibold">{{ type.name }}</span>
                    </div>
                  </div>

                  <div class="flex justify-center gap-3 mb-3">
                    <span :class="`badge badge-lg bg-gradient-to-r ${getRarityColor(pokemon.rarity)} text-white border-0`">
                      {{ getRarityLabel(pokemon.rarity) }}
                    </span>
                    <span class="badge badge-lg bg-gradient-to-r from-warning to-warning/80 text-white border-0">
                      CP: {{ calculateCP(pokemon).toLocaleString() }}
                    </span>
                  </div>
                </div>

                <div class="bg-base-200/30 rounded-lg p-4 mb-6">
                  <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-2 flex items-center gap-2">
                    <component :is="Info" :size="16" />
                    Description
                  </h4>
                  <p class="text-base-content/80">{{ pokemon.description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                  <div class="bg-base-200/30 rounded-lg p-4">
                    <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-3">Caractéristiques</h4>
                    <div class="space-y-2 text-sm">
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Taille:</span>
                        <span class="font-semibold">{{ pokemon.height }} cm</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Poids:</span>
                        <span class="font-semibold">{{ pokemon.weight }} kg</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Génération:</span>
                        <span class="font-semibold">{{ pokemon.generation || 'Non définie' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Shiny:</span>
                        <span class="font-semibold">{{ pokemon.is_shiny ? 'Oui' : 'Non' }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="bg-base-200/30 rounded-lg p-4">
                    <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-3">Statistiques</h4>
                    <div class="space-y-2 text-sm">
                      <div class="flex justify-between">
                        <span class="text-base-content/70">PV:</span>
                        <span class="font-semibold">{{ pokemon.hp }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Attaque:</span>
                        <span class="font-semibold">{{ pokemon.attack }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Défense:</span>
                        <span class="font-semibold">{{ pokemon.defense }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Vitesse:</span>
                        <span class="font-semibold">{{ pokemon.speed }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Att. Spé:</span>
                        <span class="font-semibold">{{ pokemon.special_attack }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-base-content/70">Déf. Spé:</span>
                        <span class="font-semibold">{{ pokemon.special_defense }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="bg-base-200/30 rounded-lg p-4 mb-6">
                  <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-3 flex items-center gap-2">
                    <component :is="Shield" :size="16" />
                    Résistances
                  </h4>
                  <div v-if="pokemon.resistances.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div v-for="resistance in pokemon.resistances" :key="resistance.name"
                         class="bg-base-100/50 rounded-lg p-3 text-center border border-base-300/30">
                      <div class="flex items-center justify-center gap-2 mb-2">
                        <img :src="getTypeImage(resistance.name)" :alt="resistance.name" class="w-6 h-6 object-contain" />
                        <div class="text-sm font-semibold">{{ resistance.name }}</div>
                      </div>
                      <div class="text-lg font-bold" :class="{
                        'text-success': resistance.damage_relation === 'resistant' || resistance.damage_relation === 'twice_resistant',
                        'text-error': resistance.damage_relation === 'vulnerable',
                        'text-base-content': resistance.damage_relation === 'neutral'
                      }">
                        {{ resistance.damage_multiplier }}x
                      </div>
                      <div class="text-xs text-base-content/70">{{ getDamageRelationLabel(resistance.damage_relation) }}</div>
                    </div>
                  </div>
                  <div v-else class="text-center text-base-content/50">
                    Aucune résistance définie
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
            <strong>{{ pokemon.name }}</strong>
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

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { getTypeColor, getRarityColor } from '@/utils/pokemon';

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

defineProps<{
  pokemon: Pokemon;
  evolutions: Pokemon[];
  preEvolutions: Pokemon[];
}>();

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
  if (confirm('Êtes-vous sûr de vouloir supprimer ce Pokémon ?')) {
    router.delete(`/admin/pokemons/${props.pokemon.id}`);
  }
};
</script>

<template>
  <Head :title="`${pokemon.name} - Détails`" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent mb-1 tracking-wider">
            👁️ DÉTAILS POKÉMON
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Informations complètes
          </p>
        </div>
      </div>

      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📊</span>
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
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚡</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button
              @click="router.visit(`/admin/pokemons/${pokemon.id}/edit`)"
              variant="primary"
              size="sm"
              class="w-full"
            >
              ✏️ Modifier
            </Button>
            <Button
              @click="deletePokemon"
              variant="secondary"
              size="sm"
              class="w-full"
            >
              🗑️ Supprimer
            </Button>
            <div class="border-t border-base-300/30 pt-2">
              <Button
                @click="router.visit('/admin/pokemons')"
                variant="ghost"
                size="sm"
                class="w-full"
              >
                ← Liste Pokémon
              </Button>
              <Button
                @click="router.visit('/admin')"
                variant="ghost"
                size="sm"
                class="w-full mt-1"
              >
                🏠 Dashboard
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔗</span>
              ÉVOLUTIONS
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div v-if="preEvolutions.length > 0" class="text-center">
              <div class="text-xs text-base-content/70 mb-1">Pré-évolutions</div>
              <div class="space-y-2">
                <div v-for="preEvo in preEvolutions" :key="preEvo.id" class="text-center">
                  <div class="relative w-12 h-12 mx-auto mb-1">
                    <img 
                      :src="`/images/pokemon-gifs/${preEvo.pokedex_id}.gif`" 
                      :alt="preEvo.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
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
                      <img 
                        :src="`/images/pokemon-gifs/${evo.pokedex_id}.gif`" 
                        :alt="evo.name"
                        class="w-full h-full object-contain"
                        @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                      />
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

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[700px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📋</span>
              INFORMATIONS DÉTAILLÉES
            </h3>
          </div>

          <div class="flex-1 overflow-y-auto p-6">
            <!-- En-tête du Pokémon -->
            <div class="text-center mb-6">
              <div class="relative w-32 h-32 mx-auto mb-3">
                <img 
                  :src="getPokemonImage(pokemon)" 
                  :alt="pokemon.name"
                  class="w-full h-full object-contain"
                  @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                />
                <div v-if="pokemon.is_shiny" class="absolute -top-2 -right-2 text-2xl">✨</div>
              </div>
              <h2 class="text-2xl font-bold mb-1">{{ pokemon.name }}</h2>
              <div class="text-sm text-base-content/60 mb-3">#{{ pokemon.pokedex_id.toString().padStart(3, '0') }}</div>
              
              <div class="flex justify-center gap-2 mb-3">
                <div
                  v-for="type in pokemon.types"
                  :key="type.name"
                  class="flex items-center gap-1 bg-base-200/50 rounded-lg px-3 py-1 border border-base-300/30"
                >
                  <img 
                    :src="getTypeImage(type.name)" 
                    :alt="type.name"
                    class="w-4 h-4 object-contain"
                  />
                  <span class="text-sm font-semibold">{{ type.name }}</span>
                </div>
              </div>
              
              <div class="flex justify-center gap-3 mb-3">
                <span
                  :class="`badge badge-lg bg-gradient-to-r ${getRarityColor(pokemon.rarity)} text-white border-0`"
                >
                  {{ getRarityLabel(pokemon.rarity) }}
                </span>
                <span class="badge badge-lg bg-gradient-to-r from-warning to-warning/80 text-white border-0">
                  CP: {{ calculateCP(pokemon).toLocaleString() }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="bg-base-200/30 rounded-lg p-4 mb-6">
              <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-2">Description</h4>
              <p class="text-base-content/80">{{ pokemon.description }}</p>
            </div>

            <!-- Informations physiques -->
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
                    <span class="font-semibold">{{ pokemon.is_shiny ? '✨ Oui' : 'Non' }}</span>
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

            <!-- Résistances -->
            <div class="bg-base-200/30 rounded-lg p-4 mb-6">
              <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-3">Résistances</h4>
              <div v-if="pokemon.resistances.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="resistance in pokemon.resistances"
                  :key="resistance.name"
                  class="bg-base-100/50 rounded-lg p-3 text-center border border-base-300/30"
                >
                  <div class="flex items-center justify-center gap-2 mb-2">
                    <img 
                      :src="getTypeImage(resistance.name)" 
                      :alt="resistance.name"
                      class="w-6 h-6 object-contain"
                    />
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

            <!-- Évolutions -->
            <div class="bg-base-200/30 rounded-lg p-4">
              <h4 class="font-bold text-base-content/70 uppercase tracking-wider text-sm mb-3">Ligne d'évolution</h4>
              <div class="flex items-center justify-center gap-4">
                <div v-if="pokemon.preEvolution" class="text-center">
                  <div class="relative w-20 h-20 mx-auto mb-2">
                    <img 
                      :src="`/images/pokemon-gifs/${pokemon.preEvolution.pokedex_id}.gif`" 
                      :alt="pokemon.preEvolution.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                  </div>
                  <div class="text-sm font-semibold">{{ pokemon.preEvolution.name }}</div>
                  <div class="text-xs text-base-content/60">#{{ pokemon.preEvolution.pokedex_id }}</div>
                </div>
                
                <div class="text-2xl text-base-content/50">→</div>
                
                <div class="text-center">
                  <div class="relative w-20 h-20 mx-auto mb-2">
                    <img 
                      :src="getPokemonImage(pokemon)" 
                      :alt="pokemon.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                    <div v-if="pokemon.is_shiny" class="absolute -top-1 -right-1 text-lg">✨</div>
                  </div>
                  <div class="text-sm font-semibold">{{ pokemon.name }}</div>
                  <div class="text-xs text-base-content/60">#{{ pokemon.pokedex_id }}</div>
                </div>
                
                <div class="text-2xl text-base-content/50">→</div>
                
                <div v-if="pokemon.evolution" class="text-center">
                  <div class="relative w-20 h-20 mx-auto mb-2">
                    <img 
                      :src="`/images/pokemon-gifs/${pokemon.evolution.pokedex_id}.gif`" 
                      :alt="pokemon.evolution.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                  </div>
                  <div class="text-sm font-semibold">{{ pokemon.evolution.name }}</div>
                  <div class="text-xs text-base-content/60">#{{ pokemon.evolution.pokedex_id }}</div>
                </div>
                <div v-else class="text-center">
                  <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-base-300/20 to-base-300/10 flex items-center justify-center text-lg text-base-content/30 mb-2">
                    ?
                  </div>
                  <div class="text-sm text-base-content/50">Aucune</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 
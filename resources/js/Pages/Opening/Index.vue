<script setup lang="ts">
import PokemonCard from '@/Components/Cards/PokemonCard.vue';
import EvolutionAnimation from '@/Components/Game/EvolutionAnimation.vue';
import GachaBall from '@/Components/Game/GachaBall.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, getCurrentInstance, ref } from 'vue';

import type { PageProps } from '@/types';
import type { Inventory } from '@/types/inventory';
import type { User } from '@/types/user';

type Rarity = 'normal' | 'rare' | 'epic' | 'legendary';

interface InvocationPokemon {
  id: number;
  pokemon_id: number;
  name: string;
  level: number;
  star: number;
  is_shiny: boolean;
  rarity: Rarity;
  types: any[];
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  special_attack: number;
  special_defense: number;
}

interface Props extends PageProps {
  auth: {
    user: User;
  };
  inventory: Inventory[];
}

const { auth, inventory } = defineProps<Props>();
const instance = getCurrentInstance();
const page = computed(() => instance?.appContext.app.config.globalProperties.$page);

const isInvoking = ref(false);
const showEvolutionAnimation = ref(false);
const showGachaResults = ref(false);
const showFinalResults = ref(false);
const invocationResults = ref<InvocationPokemon[]>([]);
const revealedBalls = ref<boolean[]>([]);
const currentBallType = ref('');
const currentInventory = ref([...inventory]);
const loadingMessage = ref('Préparation de l\'invocation');
const transitionToResults = ref(false);
const ballsReady = ref(false);

const getInventoryQuantity = (itemName: string) => {
  const item = currentInventory.value.find(inv => inv.item?.name === itemName);
  return item ? item.quantity : 0;
};

const pokeballQuantity = computed(() => getInventoryQuantity('Pokeball'));
const masterballQuantity = computed(() => getInventoryQuantity('Masterball'));

const transformedResults = computed(() => {
  return invocationResults.value.map(pokemon => ({
    id: pokemon.id,
    user_id: auth.user.id,
    pokemon_id: pokemon.pokemon_id,
    level: pokemon.level,
    star: pokemon.star,
    nickname: undefined,
    is_favorite: false,
    is_in_team: false,
    obtained_at: new Date().toISOString(),
    pokemon: {
      id: pokemon.pokemon_id,
      name: pokemon.name,
      is_shiny: pokemon.is_shiny,
      rarity: pokemon.rarity,
      types: pokemon.types,
      hp: pokemon.hp,
      attack: pokemon.attack,
      defense: pokemon.defense,
      speed: pokemon.speed,
      special_attack: pokemon.special_attack,
      special_defense: pokemon.special_defense
    }
  }));
});

const getMaxRarity = (): Rarity => {
  if (!invocationResults.value.length) return 'normal';

  const rarityOrder: Record<Rarity, number> = { 'normal': 0, 'rare': 1, 'epic': 2, 'legendary': 3 };

  return invocationResults.value.reduce((maxRarity, pokemon) => {
    const currentRarityValue = rarityOrder[pokemon.rarity] ?? 0;
    const maxRarityValue = rarityOrder[maxRarity] ?? 0;

    return currentRarityValue > maxRarityValue ? pokemon.rarity : maxRarity;
  }, 'normal' as Rarity);
};

const hasShinyPokemon = () => {
  return invocationResults.value.some(pokemon => pokemon.is_shiny);
};

const canInvoke = (ballType: string, quantity: number) => {
  const available = ballType === 'Pokeball' ? pokeballQuantity.value : masterballQuantity.value;
  return available >= quantity && !isInvoking.value && !showEvolutionAnimation.value && !showGachaResults.value;
};

const updateInventoryQuantity = (itemName: string, usedQuantity: number) => {
  const itemIndex = currentInventory.value.findIndex(inv => inv.item?.name === itemName);
  if (itemIndex !== -1) {
    currentInventory.value[itemIndex].quantity = Math.max(0, currentInventory.value[itemIndex].quantity - usedQuantity);
  }
};

const startInvocation = async (ballType: string, quantity: number) => {
  if (!canInvoke(ballType, quantity)) return;

  isInvoking.value = true;
  currentBallType.value = ballType;
  loadingMessage.value = 'Invocation en cours...';

  try {
    const response = await fetch('/api/opening', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.value?.props?.csrf_token as string || '',
      },
      body: JSON.stringify({
        ball_type: ballType,
        quantity: quantity
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success) {
      invocationResults.value = data.pokemons;
      revealedBalls.value = new Array(data.pokemons.length).fill(false);

      updateInventoryQuantity(ballType, quantity);

      setTimeout(() => {
        isInvoking.value = false;

        if (quantity >= 1) {
          showEvolutionAnimation.value = true;
        } else {
          transitionToResults.value = true;
          setTimeout(() => {
            showGachaResults.value = true;
            setTimeout(() => {
              ballsReady.value = true;
            }, 50);
          }, 500);
        }
      }, 500);
    } else {
      throw new Error(data.message || 'Erreur lors de l\'invocation');
    }
  } catch (error) {
    isInvoking.value = false;
  }
};

const onEvolutionCompleted = () => {
  transitionToResults.value = true;
  showGachaResults.value = true;

  setTimeout(() => {
    ballsReady.value = true;
  }, 50);

  setTimeout(() => {
    showEvolutionAnimation.value = false;
  }, 400);
};

const revealBall = (index: number) => {
  revealedBalls.value[index] = true;
};

const revealAllBalls = () => {
  revealedBalls.value = revealedBalls.value.map(() => true);
};

const allBallsRevealed = computed(() => {
  return revealedBalls.value.every(revealed => revealed);
});

const closeResults = () => {
  showGachaResults.value = false;
  showFinalResults.value = false;
  showEvolutionAnimation.value = false;
  invocationResults.value = [];
  revealedBalls.value = [];
  currentBallType.value = '';
  isInvoking.value = false;
  transitionToResults.value = false;
  ballsReady.value = false;
};

const goBack = () => {
  router.visit('/me');
}
</script>

<template>

  <Head title="Invocation" />

  <div class="sm:h-screen sm:w-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 sm:h-screen  sm:w-screen">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center sm:mt-0 mt-14">
          <h1
            class="sm:text-2xl text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-1 tracking-wider">
            ⚡ INVOCATION
          </h1>
          <p class="sm:text-xs text-[10px] text-base-content/70 uppercase tracking-wider">
            Choisissez votre type de ball et la quantité
          </p>
        </div>
      </div>

      <div class="absolute sm:left-8 left-4 sm:top-20 top-4">
        <Button @click="goBack" variant="ghost" size="sm" class="!bg-base-100/60 !backdrop-blur-sm">
          ← Retour
        </Button>
      </div>

      <div class="flex flex-col md:flex-row justify-center items-start gap-8 mt-12 md:mt-24">
        <div class="flex-1 w-full max-w-md px-4 md:px-0 mt-8 md:mt-0 block sm:hidden mb-16">
          <div
            class="bg-base-100/70 backdrop-blur-md rounded-2xl border border-base-300/30 shadow-lg flex flex-col items-center justify-center py-12 px-6">
            <div class="text-7xl mb-4 animate-pulse">🎰</div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-2">
              Machine à Invocation</h2>
            <p class="text-base-content/70 text-center mb-2">
              Tente ta chance et découvre quels Pokémon tu vas obtenir !<br>
              Plus tu ouvres de balls, plus tu as de chances d'obtenir des Pokémon rares ou shiny.
            </p>
            <div class="mt-4 flex flex-col items-center gap-2">
              <span class="text-xs text-base-content/50">Bonne chance, Dresseur !</span>
            </div>
          </div>
        </div>
        <div class="w-full md:w-64 px-4 md:px-0">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6" />
                POKEBALL
              </h3>
            </div>
            <div class="p-3 text-center">
              <div class="text-2xl font-bold text-info mb-1">{{ pokeballQuantity }}</div>
              <div class="text-xs text-base-content/70">disponible{{ pokeballQuantity > 1 ? 's' : '' }}</div>
            </div>
            <div class="p-3 m-3 bg-base-100/40">
              <h4 class="text-xs font-bold tracking-wider mb-2 text-center">CHANCES</h4>
              <div class="space-y-1 text-xs">
                <div class="flex justify-between">
                  <span>Normal</span>
                  <span class="font-bold">70%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-info">Rare</span>
                  <span class="font-bold">27%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-secondary">Epic</span>
                  <span class="font-bold">2.7%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-warning">Légendaire</span>
                  <span class="font-bold">0.3%</span>
                </div>
              </div>
            </div>
            <div class="p-3 space-y-2">
              <Button @click="startInvocation('Pokeball', 1)" :disabled="!canInvoke('Pokeball', 1)" variant="secondary"
                      size="sm" class="w-full flex items-center justify-center gap-2"
                      :class="{ 'opacity-50 cursor-not-allowed': !canInvoke('Pokeball', 1) }">
                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6 inline-block" />
                Invoquer x1
              </Button>
              <Button @click="startInvocation('Pokeball', 10)" :disabled="!canInvoke('Pokeball', 10)" variant="primary"
                      size="sm" class="w-full flex items-center justify-center gap-2"
                      :class="{ 'opacity-50 cursor-not-allowed': !canInvoke('Pokeball', 10) }">
                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6 inline-block" />
                Invoquer x10
              </Button>
            </div>
          </div>
        </div>

        <div class="flex-1 w-full max-w-md px-4 md:px-0 mt-8 md:mt-0 hidden sm:block">
          <div
            class="bg-base-100/70 backdrop-blur-md rounded-2xl border border-base-300/30 shadow-lg flex flex-col items-center justify-center py-12 px-6">
            <div class="text-7xl mb-4 animate-pulse">🎰</div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-2">
              Machine à Invocation</h2>
            <p class="text-base-content/70 text-center mb-2">
              Tente ta chance et découvre quels Pokémon tu vas obtenir !<br>
              Plus tu ouvres de balls, plus tu as de chances d'obtenir des Pokémon rares ou shiny.
            </p>
            <div class="mt-4 flex flex-col items-center gap-2">
              <span class="text-xs text-base-content/50">Bonne chance, Dresseur !</span>
            </div>
          </div>
        </div>

        <div class="w-full md:w-64 px-4 md:px-0 mt-8 md:mt-0 mb-16">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6" />
                MASTERBALL
              </h3>
            </div>
            <div class="p-3 text-center">
              <div class="text-2xl font-bold text-warning mb-1">{{ masterballQuantity }}</div>
              <div class="text-xs text-base-content/70">disponible{{ masterballQuantity > 1 ? 's' : '' }}</div>
            </div>
            <div class="p-3 m-3 bg-base-100/40">
              <h4 class="text-xs font-bold tracking-wider mb-2 text-center">CHANCES</h4>
              <div class="space-y-1 text-xs">
                <div class="flex justify-between">
                  <span>Normal</span>
                  <span class="font-bold">34%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-info">Rare</span>
                  <span class="font-bold">60%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-secondary">Epic</span>
                  <span class="font-bold">5%</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-warning">Légendaire</span>
                  <span class="font-bold">1%</span>
                </div>
              </div>
            </div>
            <div class="p-3 space-y-2">
              <Button @click="startInvocation('Masterball', 1)" :disabled="!canInvoke('Masterball', 1)"
                      variant="secondary" size="sm" class="w-full flex items-center justify-center gap-2"
                      :class="{ 'opacity-50 cursor-not-allowed': !canInvoke('Masterball', 1) }">
                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6 inline-block" />
                Invoquer x1
              </Button>
              <Button @click="startInvocation('Masterball', 10)" :disabled="!canInvoke('Masterball', 10)"
                      variant="primary" size="sm" class="w-full flex items-center justify-center gap-2"
                      :class="{ 'opacity-50 cursor-not-allowed': !canInvoke('Masterball', 10) }">
                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6 inline-block" />
                Invoquer x10
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isInvoking" class="fixed inset-0 z-30 flex items-center justify-center bg-black/90 backdrop-blur-sm">
      <div class="text-center">
        <img src="/images/loading.gif" alt="Chargement..." class="w-32 h-32 mx-auto mb-6 object-contain" />
        <div class="text-2xl font-bold text-white animate-pulse">
          {{ loadingMessage }}
        </div>
      </div>
    </div>

    <EvolutionAnimation v-if="showEvolutionAnimation" :max-rarity="getMaxRarity()" :has-shiny="hasShinyPokemon()"
                        @completed="onEvolutionCompleted" />

    <div v-if="showGachaResults" class="fixed inset-0 z-40 flex items-center justify-center overflow-hidden"
         :class="{ 'animate-fadeIn': transitionToResults }">
      <img src="/images/invocation_background.png" alt="Background"
           class="absolute inset-0 w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/60"></div>

      <div class="relative text-center max-w-6xl mx-auto px-6 h-full flex flex-col justify-center">
        <h2 class="sm:text-3xl text-xl font-bold mb-2 text-secondary/80 animate-slideDown">
          Vos récompenses vous attendent !
        </h2>
        <p class="text-white/50 mb-20 animate-slideDown " style="animation-delay: 0.2s;">
          Survolez les balls pour apercevoir leur rareté, puis cliquez pour révéler !
        </p>
        <div :class="[
          'mb-8',
          invocationResults.length === 1
            ? 'flex justify-center'
            : 'grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-x-2 gap-y-24 sm:gap-x-4 sm:gap-y-8 md:gap-x-6 md:gap-y-8 lg:gap-12'
        ]">
          <div v-for="(pokemon, index) in invocationResults" :key="index" class="transition-all duration-400 ease-out"
               :class="{
                 'opacity-0 translate-y-8 scale-0': !ballsReady,
                 'opacity-100 translate-y-0 scale-100': ballsReady
               }" :style="{
                 transitionDelay: ballsReady ? `${index * 15}ms` : '0ms'
               }">
            <GachaBall :ball-type="currentBallType" :pokemon="pokemon" :index="index" :revealed="revealedBalls[index]"
                       @reveal="revealBall" />
          </div>
        </div>
        <div class="flex gap-4 justify-center sm:mt-20 mt-5 animate-slideUp" style="animation-delay: 1s;">
          <Button @click="revealAllBalls" variant="outline" size="lg" :disabled="allBallsRevealed"
                  class="bg-white/10 backdrop-blur-sm hover:bg-white/20">
            Tout révéler
          </Button>
          <Button v-if="allBallsRevealed" @click="closeResults" variant="ghost" size="lg"
                  class="bg-white/10 backdrop-blur-sm hover:bg-white/20">
            Fermer
          </Button>
        </div>
      </div>
    </div>

    <Modal :show="showFinalResults" @close="closeResults" max-width="7xl">
      <template #header>
        <div class="text-center">
          <h2 class="text-2xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent">
            🎉 Résumé de l'invocation
          </h2>
          <p class="text-base-content/70 mt-2">
            {{ transformedResults.length }} nouveau{{ transformedResults.length > 1 ? 'x' : '' }} Pokémon ajouté{{
              transformedResults.length > 1 ? 's' : '' }} à votre collection !
          </p>
        </div>
      </template>
      <template #default>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="(pokemon, index) in transformedResults" :key="index"
               class="transform transition-all duration-300 animate-slide-in-up"
               :style="{ animationDelay: `${index * 50}ms` }">
            <PokemonCard :entry="pokemon" size="large" variant="modal" :show-details="true" />
          </div>
        </div>
        <div class="text-center mt-8">
          <Button @click="closeResults" variant="primary" size="lg" class="min-w-48">
            🎉 Parfait !
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.8s ease-out;
}

.animate-slideDown {
  animation: slideDown 0.6s ease-out forwards;
  opacity: 0;
}

.animate-slideUp {
  animation: slideUp 0.6s ease-out forwards;
  opacity: 0;
}

.animate-slide-in-up {
  animation: slide-in-up 0.5s ease-out forwards;
}
</style>

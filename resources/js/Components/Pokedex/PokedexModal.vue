<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Pokemon } from '@/types/pokemon';
import type { Pokedex } from '@/types/pokedex';
import PokedexModalCard from './PokedexModalCard.vue';
import Modal from '@/Components/UI/Modal.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Button from '@/Components/UI/Button.vue';

interface Props {
  show: boolean;
  allPokemons: Pokemon[];
  userPokedex: Pokedex[];
  onClose: () => void;
}
const props = defineProps<Props>();

const selectedPokemon = ref<DisplayPokemon | null>(null);
const activeTab = ref<'normal' | 'shiny'>('normal');

const searchQuery = ref('');
const rarityFilter = ref<string>('all');
const ownershipFilter = ref<'all' | 'owned' | 'unowned'>('all');

const rarities = [
  { value: 'all', label: 'Toutes les raretés' },
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' },
];

const ownershipOptions = [
  { value: 'all', label: 'Tous' },
  { value: 'owned', label: 'Possédés' },
  { value: 'unowned', label: 'Non possédés' },
];

interface DisplayPokemon {
  pokedexInfo: Pokedex | null;
  pokemon: Pokemon;
  owned: boolean;
  count: number;
  all_duplicates: Pokedex[];
}

const basePokemonData = computed(() => {
  const userPokemonGroups = new Map<string, Pokedex[]>();
  props.userPokedex.forEach(p => {
    const key = `${p.pokemon.id}`;
    if (!userPokemonGroups.has(key)) userPokemonGroups.set(key, []);
    userPokemonGroups.get(key)!.push(p);
  });

  const bestUserPokemon = new Map<string, DisplayPokemon>();
  for (const [key, group] of userPokemonGroups.entries()) {
    const sortedGroup = [...group].sort((a, b) => {
      if (b.star !== a.star) return b.star - a.star;
      return b.level - a.level;
    });
    const best = sortedGroup[0];
    bestUserPokemon.set(key, {
      pokedexInfo: best, pokemon: best.pokemon, owned: true, count: group.length, all_duplicates: sortedGroup
    });
  }

  const finalDisplayMap = new Map<number, DisplayPokemon>();
  props.allPokemons.forEach(p => {
    finalDisplayMap.set(p.id, {
      pokedexInfo: null, pokemon: p, owned: false, count: 0, all_duplicates: []
    });
  });

  for (const [key, bestPokedexEntry] of bestUserPokemon.entries()) {
    finalDisplayMap.set(Number(key), bestPokedexEntry);
  }
  return Array.from(finalDisplayMap.values());
});

const filteredPokedex = computed(() => {
  return basePokemonData.value
    .filter(p => p.pokemon.is_shiny === (activeTab.value === 'shiny'))
    .filter(p => {
      if (!searchQuery.value) return true;
      return p.pokemon.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    })
    .filter(p => {
      if (rarityFilter.value === 'all') return true;
      return p.pokemon.rarity === rarityFilter.value;
    })
    .filter(p => {
      if (ownershipFilter.value === 'all') return true;
      return ownershipFilter.value === 'owned' ? p.owned : !p.owned;
    })
    .sort((a, b) => {
      const baseIdA = a.pokemon.id % 1000;
      const baseIdB = b.pokemon.id % 1000;
      return baseIdA - baseIdB;
    });
});

const pokedexProgress = computed(() => {
  const normalTotal = props.allPokemons.filter(p => !p.is_shiny).length;
  const shinyTotal = props.allPokemons.filter(p => p.is_shiny).length;
  const normalOwned = basePokemonData.value.filter(p => !p.pokemon.is_shiny && p.owned).length;
  const shinyOwned = basePokemonData.value.filter(p => p.pokemon.is_shiny && p.owned).length;
  return { normalTotal, shinyTotal, normalOwned, shinyOwned };
});


const handleCardClick = (pokemon: DisplayPokemon) => {
  if (pokemon.owned) selectedPokemon.value = pokemon;
};

const closeDetails = () => {
  selectedPokemon.value = null;
};

const switchTab = (tab: 'normal' | 'shiny') => {
  activeTab.value = tab;
};

</script>

<template>
  <Modal :show="show" max-width="7xl" @close="onClose">
    <template #header>
      <div class="w-full flex flex-col lg:flex-row gap-4 justify-between lg:items-center">
                
        <div class="flex-shrink-0">
          <h2 class="text-2xl font-bold">Mon Pokédex</h2>
          <div class="flex items-center gap-4 mt-1 text-xs text-base-content/70">
            <span>Normal: <span class="font-bold text-base-content">{{ pokedexProgress.normalOwned }}/{{ pokedexProgress.normalTotal }}</span></span>
            <span class="text-yellow-400">Shiny: <span class="font-bold">{{ pokedexProgress.shinyOwned }}/{{ pokedexProgress.shinyTotal }}</span> ✨</span>
          </div>
        </div>

        <div class="flex flex-col md:flex-row gap-2 items-center mr-12">
          <div class="flex items-center gap-2 flex-shrink-0">
            <Button 
              @click="switchTab('normal')" 
              :variant="activeTab === 'normal' ? 'primary' : 'secondary'"
              size="sm"
            >
              Normal
            </Button>
            <Button 
              @click="switchTab('shiny')" 
              :variant="activeTab === 'shiny' ? 'primary' : 'secondary'"
              size="sm"
            >
              Shiny
            </Button>
          </div>
          <div class="w-full grid grid-cols-1 sm:grid-cols-3 gap-2">
            <Input type="text" v-model="searchQuery" placeholder="Rechercher..." id="pokedex_search" size="sm"/>
            <Select v-model="rarityFilter" :options="rarities" id="pokedex_rarity" size="sm"/>
            <Select v-model="ownershipFilter" :options="ownershipOptions" id="pokedex_ownership" size="sm"/>
          </div>
        </div>

      </div>
    </template>
        
    <div class="pr-2 -mr-2">
      <div v-if="!selectedPokemon">
        <div class="h-[60vh] overflow-y-auto p-1">
          <div v-if="filteredPokedex.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <PokedexModalCard 
              v-for="p in filteredPokedex" 
              :key="p.pokemon.id"
              :display-pokemon="p"
              @click="handleCardClick(p)"
            />
          </div>
          <div v-else class="flex flex-col items-center justify-center h-full text-center text-base-content/70">
            <p class="text-5xl mb-4">😥</p>
            <h3 class="font-bold text-lg text-base-content">Aucun Pokémon trouvé</h3>
            <p class="text-sm">Essayez de modifier vos filtres de recherche.</p>
          </div>
        </div>
      </div>
      <div v-else-if="selectedPokemon" class="h-[60vh] overflow-y-auto p-4">
        <button @click="closeDetails" class="mb-6 bg-primary text-primary-content hover:bg-primary-focus p-2 rounded-lg flex items-center gap-2 text-sm font-bold">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
          Retour au Pokédex
        </button>
        <div class="flex flex-col lg:flex-row gap-8">
          <div class="flex-shrink-0 lg:w-1/3 text-center">
            <div class="relative inline-block">
              <img 
                :src="`/images/pokemon-gifs/${selectedPokemon.pokemon.is_shiny ? (selectedPokemon.pokemon.id - 1000) + '_S' : selectedPokemon.pokemon.id}.gif`" 
                :alt="selectedPokemon.pokemon.name"
                class="w-48 h-48 object-contain"
                style="image-rendering: pixelated;"
              >
              <div v-if="selectedPokemon.pokemon.is_shiny" class="absolute top-0 right-0 w-8 h-8 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border-2 border-yellow-500/30">
                <span class="text-yellow-400 text-2xl">✨</span>
              </div>
            </div>
            <h2 class="text-3xl font-bold mt-4">{{ selectedPokemon.pokedexInfo?.nickname || selectedPokemon.pokemon.name }}</h2>
            <p class="text-lg text-base-content/70">Niv. {{ selectedPokemon.pokedexInfo?.level }}</p>
          </div>
          <div class="flex-grow lg:w-2/3">
            <h3 class="text-xl font-bold mb-4 border-b border-base-300 pb-2">
              Tous les exemplaires capturés ({{ selectedPokemon.count }})
            </h3>
            <div class="space-y-3 pr-2">
              <div 
                v-for="duplicate in selectedPokemon.all_duplicates" 
                :key="duplicate.id"
                class="bg-base-200/50 p-4 rounded-lg flex items-center justify-between gap-4"
                :class="{'border-2 border-primary shadow-lg': duplicate.id === selectedPokemon.pokedexInfo?.id}"
              >
                <div class="flex items-center gap-4">
                  <div v-if="duplicate.pokemon.is_shiny" class="w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                    <span class="text-yellow-400 text-sm">✨</span>
                  </div>
                  <div>
                    <p class="font-bold">{{ duplicate.nickname || duplicate.pokemon.name }}</p>
                    <p class="text-sm text-base-content/70">Niveau {{ duplicate.level }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <RarityBadge :rarity="duplicate.pokemon.rarity" />
                  <StarsBadge :stars="duplicate.star" size="md" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template> 
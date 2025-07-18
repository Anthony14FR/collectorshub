<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Pokedex } from '@/types/pokedex';
import Modal from '@/Components/UI/Modal.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import PokedexModalCard from '@/Components/Pokedex/PokedexModalCard.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import { calculateTeamCP } from '@/utils/cp';

interface Props {
  show: boolean;
  onClose: () => void;
  userPokemons: Pokedex[];
}

const props = defineProps<Props>();

const localPokemons = ref<Pokedex[]>([]);
const processing = ref(false);
const searchQuery = ref('');
const rarityFilter = ref('all');
const shinyFilter = ref('all');
const sortFilter = ref('cp');
const forceRerenderKey = ref(0);

const rarities = [
  { value: 'all', label: 'Toutes les raretés' },
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' },
];

const shinyOptions = [
  { value: 'all', label: 'Tous' },
  { value: 'shiny', label: 'Shiny seulement' },
  { value: 'not_shiny', label: 'Non-Shiny' },
];

const sortOptions = [
  { value: 'cp', label: 'CP' },
  { value: 'name', label: 'Nom (A → Z)' },
  { value: 'rarity', label: 'Rareté' },
];

watch(() => props.userPokemons, (newPokemons) => {
  localPokemons.value = JSON.parse(JSON.stringify(newPokemons));
  forceRerenderKey.value++;
}, { immediate: true, deep: true });

const team = computed(() => localPokemons.value.filter(p => p.is_in_team).sort((a,b) => (a.team_position ?? 99) - (b.team_position ?? 99)));

const teamCP = computed(() => {
  return calculateTeamCP(team.value);
});

const filteredAvailablePokemons = computed(() => {
  let filtered = localPokemons.value
    .filter(p => !p.is_in_team)
    .filter(p => {
      if (!searchQuery.value) return true;
      return p.pokemon?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) || false;
    })
    .filter(p => {
      if (rarityFilter.value === 'all') return true;
      return p.pokemon?.rarity === rarityFilter.value;
    })
    .filter(p => {
      if (shinyFilter.value === 'all') return true;
      if (shinyFilter.value === 'shiny') return p.pokemon?.is_shiny === true;
      if (shinyFilter.value === 'not_shiny') return p.pokemon?.is_shiny === false;
      return false;
    });

  return filtered.sort((a, b) => {
    switch (sortFilter.value) {
    case 'cp':
      return (b.cp || 0) - (a.cp || 0);
    case 'level':
      return b.level - a.level;
    case 'name':
      const nameA = a.pokemon?.name || '';
      const nameB = b.pokemon?.name || '';
      return nameA.localeCompare(nameB);
    case 'rarity':
      const rarityOrder = { 'legendary': 4, 'epic': 3, 'rare': 2, 'normal': 1 };
      const rarityA = a.pokemon?.rarity as keyof typeof rarityOrder;
      const rarityB = b.pokemon?.rarity as keyof typeof rarityOrder;
      return (rarityOrder[rarityB] || 0) - (rarityOrder[rarityA] || 0);
    default:
      return 0;
    }
  });
});

const addToTeam = (pokemon: Pokedex) => {
  if (team.value.length >= 6 || processing.value) return;
    
  const existingPositions = team.value.map(p => p.team_position).filter(Boolean) as number[];
  let newPosition = 1;
  while(newPosition <= 6 && existingPositions.includes(newPosition)) {
    newPosition++;
  }

  if (newPosition > 6) return;

  processing.value = true;
  router.post(`/pokedex/${pokemon.id}/add-to-team`, { position: newPosition }, {
    preserveScroll: true,
    onSuccess: () => {
      pokemon.is_in_team = true;
      pokemon.team_position = newPosition;
      
      nextTick(() => {
        forceRerenderKey.value++;
      });
    },
    onFinish: () => { processing.value = false; }
  });
};

const removeFromTeam = (pokemon: Pokedex) => {
  if (processing.value) return;
  processing.value = true;
  router.post(`/pokedex/${pokemon.id}/remove-from-team`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      pokemon.is_in_team = false;
      pokemon.team_position = null;
      nextTick(() => {
        forceRerenderKey.value++;
      });
    },
    onFinish: () => { processing.value = false; }
  });
};

</script>

<template>
  <Modal :show="show" max-width="5xl" @close="onClose">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Gérer mon équipe</h2>
        <div class="mr-16" v-if="team.length > 0">
          <CPBadge :cp="teamCP" size="lg" variant="gradient" />
        </div>
      </div>
    </template>
    <div class="p-4 bg-base-100/70">
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4 text-base-content">Mon Équipe ({{ team.length }}/6)</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4" :key="`team-${forceRerenderKey}`">
          <div v-for="n in 6" :key="`team-slot-${n}-${forceRerenderKey}`" class="bg-base-200/50 rounded-xl border-2 border-dashed border-base-300/50 flex items-center justify-center p-2">
            <div v-if="team[n-1]" @click="removeFromTeam(team[n-1])" class="text-center w-full h-full relative group cursor-pointer">
              <PokedexModalCard v-if="team[n-1]?.pokemon" :displayPokemon="{pokedexInfo: team[n-1], pokemon: team[n-1].pokemon!, owned: true, count: 1 }" />
              <div class="absolute inset-0 bg-black/70 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10">
                <span class="text-white font-bold text-lg tracking-wider">Retirer</span>
              </div>
            </div>
            <span v-else class="text-4xl text-base-content/20">+</span>
          </div>
        </div>
      </div>

      <div>
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-base-content">Pokémon Disponibles</h3>
          <div class="flex gap-2">
            <Input type="text" v-model="searchQuery" placeholder="Rechercher..." size="sm"/>
            <Select v-model="rarityFilter" :options="rarities" size="sm"/>
            <Select v-model="shinyFilter" :options="shinyOptions" size="sm"/>
            <Select v-model="sortFilter" :options="sortOptions" size="sm"/>
          </div>
        </div>
                
        <div class="h-[40vh] overflow-y-auto p-2 bg-base-200/30 rounded-lg" :key="`available-${forceRerenderKey}`">
          <div v-if="filteredAvailablePokemons.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div v-for="p in filteredAvailablePokemons" :key="`pokemon-${p.id}-${forceRerenderKey}`" @click="addToTeam(p)" class="relative group" :class="{'opacity-50 cursor-not-allowed': team.length >= 6 || processing}">
              <PokedexModalCard v-if="p.pokemon" :displayPokemon="{pokedexInfo: p, pokemon: p.pokemon!, owned: true, count: 1 }" />
              <div v-if="team.length < 6" class="absolute inset-0 bg-black/70 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                <span class="text-white font-bold text-lg tracking-wider">Ajouter</span>
              </div>
            </div>
          </div>
          <div v-else class="flex items-center justify-center h-full">
            <p class="text-center text-base-content/50">Aucun Pokémon correspondant aux filtres.</p>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>
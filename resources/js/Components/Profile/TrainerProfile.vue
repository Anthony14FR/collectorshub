<script setup lang="ts">
import TeamPokemonCard from '@/Components/Profile/TeamPokemonCard.vue';
import Button from '@/Components/UI/Button.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import type { Pokedex } from '@/types/pokedex';
import type { User } from '@/types/user';
import { calculateTeamCP } from '@/utils/cp';
import { Shield } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
  user: User;
  trainerImageId?: number;
  onOpenPokedexModal?: () => void;
  teamPokemons?: Pokedex[];
}

const { user, trainerImageId = 2, onOpenPokedexModal, teamPokemons } = defineProps<Props>();

const displayLeftTeam = computed(() => {
  const team = teamPokemons ? [...teamPokemons] : [];
  return Array(3).fill(null).map((_, i) => team[i] || null);
});

const displayRightTeam = computed(() => {
  const team = teamPokemons ? [...teamPokemons] : [];
  return Array(3).fill(null).map((_, i) => team[i + 3] || null);
});

const teamCP = computed(() => {
  if (!teamPokemons || teamPokemons.length === 0) return 0;
  return calculateTeamCP(teamPokemons);
});

const showTeamCP = computed(() => {
  return teamPokemons && teamPokemons.length > 0;
});
</script>

<template>
  <div class="flex flex-col items-center space-y-4 mt-14">
    <div v-if="showTeamCP" class="mb-2">
      <CPBadge 
        :cp="teamCP"
        size="lg"
        variant="gradient"
        :show-label="true"
      />
    </div>

    <div class="relative flex items-center justify-center">
      <div class="absolute sm:-left-28 -left-16 flex flex-col space-y-2">
        <template v-for="(pokemon, index) in displayLeftTeam" :key="index">
          <TeamPokemonCard v-if="pokemon" :pokemon="pokemon" />
          <div v-else class="relative w-20 h-20
                              bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50">
            <img src="/images/items/pokeball.png" alt="placeholder" class="w-8 h-8 object-contain opacity-50" />
          </div>
        </template>
      </div>

      <div class="w-48 h-48">
        <img
          :src="
            user.avatar
              ? user.avatar
              : `/images/trainer/${trainerImageId}.png`
          "
          :alt="user.username"
          class="w-full h-full object-cover"
          style="image-rendering: pixelated"
        />
      </div>

      <div class="absolute sm:-right-28 -right-16 flex flex-col space-y-2">
        <template v-for="(pokemon, index) in displayRightTeam" :key="index + 3">
          <TeamPokemonCard v-if="pokemon" :pokemon="pokemon" />
          <div v-else class="relative w-20 h-20
                              bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50">
            <img src="/images/items/pokeball.png" alt="placeholder" class="w-8 h-8 object-contain opacity-50" />
          </div>
        </template>
      </div>
    </div>

    <div class="text-center">
      <h2 class="text-3xl font-bold text-base-content mb-2">
        {{ user.username }}
      </h2>
      <div class="text-lg text-base-content/70">Maître Dresseur</div>
      <Button
        v-if="onOpenPokedexModal"
        @click="onOpenPokedexModal"
        variant="secondary"
        size="sm"
        class="mt-4"
      >
        <Shield :size="17" class="mr-1" /> Gérer l'équipe
      </Button>
    </div>
  </div>
</template>
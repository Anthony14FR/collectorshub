<script setup lang="ts">
import { computed } from 'vue';
import type { User } from '@/types/user';
import Button from '@/Components/UI/Button.vue';
import TeamPokemonCard from '@/Components/Profile/TeamPokemonCard.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import type { Pokedex } from '@/types/pokedex';
import { calculateTeamCP } from '@/utils/cp';

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
  <div class="flex flex-col items-center space-y-4">
    <div v-if="showTeamCP" class="mb-2">
      <CPBadge 
        :cp="teamCP"
        size="lg"
        variant="gradient"
        :show-label="true"
      />
    </div>

    <div class="relative flex items-center justify-center">
      <div class="absolute -left-20 sm:-left-24 md:-left-28 lg:-left-20 xl:-left-24 2xl:-left-28 flex flex-col space-y-2">
        <template v-for="(pokemon, index) in displayLeftTeam" :key="index">
          <TeamPokemonCard v-if="pokemon" :pokemon="pokemon" />
          <div v-else class="relative w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-14 lg:h-14 xl:w-16 xl:h-16 2xl:w-20 2xl:h-20
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

      <div class="absolute -right-20 sm:-right-24 md:-right-28 lg:-right-20 xl:-right-24 2xl:-right-28 flex flex-col space-y-2">
        <template v-for="(pokemon, index) in displayRightTeam" :key="index + 3">
          <TeamPokemonCard v-if="pokemon" :pokemon="pokemon" />
          <div v-else class="relative w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-14 lg:h-14 xl:w-16 xl:h-16 2xl:w-20 2xl:h-20
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
        🛡️ Gérer l'équipe
      </Button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Pokedex } from '@/types/pokedex';
import Button from '@/Components/UI/Button.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';

interface Props {
  pokemons: Pokedex[];
}

const props = defineProps<Props>();

const team = ref<Pokedex[]>([]);

watch(() => props.pokemons, (newPokemons) => {
  team.value = newPokemons.filter(p => p.is_in_team);
}, { immediate: true });

const addToTeam = (pokemonId: number) => {
  router.post(`/pokedex/${pokemonId}/add-to-team`, {}, {
    preserveScroll: true,
    onSuccess: () => {
    }
  });
};

const removeFromTeam = (pokemonId: number) => {
  router.post(`/pokedex/${pokemonId}/remove-from-team`, {}, {
    preserveScroll: true,
    onSuccess: () => {
    }
  });
};

const isTeamFull = (): boolean => {
  return team.value.length >= 6;
}

</script>

<template>
  <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
    <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
      <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
        <span class="text-lg">🛡️</span>
        MON ÉQUIPE ({{ team.length }}/6)
      </h3>
    </div>
    <div class="p-3 space-y-3">
      <div v-if="team.length > 0" class="space-y-2">
        <div 
          v-for="member in team" 
          :key="member.id"
          class="bg-base-200/50 p-2 rounded-lg flex items-center justify-between gap-2"
        >
          <div class="flex items-center gap-2">
            <img 
              :src="`/images/pokemon-gifs/${member.pokemon.is_shiny ? (member.pokemon.id - 1000) + '_S' : member.pokemon.id}.gif`" 
              :alt="member.pokemon.name"
              class="w-8 h-8 object-contain"
              style="image-rendering: pixelated;"
            >
            <div>
              <p class="font-bold text-xs">{{ member.nickname || member.pokemon.name }}</p>
              <p class="text-xs text-base-content/70">Niv. {{ member.level }}</p>
            </div>
          </div>
          <Button @click="removeFromTeam(member.id)" size="sm" variant="outline" class="!border-error !text-error hover:!bg-error hover:!text-white">
            Retirer
          </Button>
        </div>
      </div>
      <div v-else class="text-center text-xs text-base-content/70 py-4">
        Votre équipe est vide.
      </div>
    </div>
  </div>
</template> 
<script setup lang="ts">
import SideSection from '@/Components/Layout/SideSection.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import type { PageProps } from '@/types';
import type { Leaderboards } from '@/types/leaderboard';
import type { Pokedex } from '@/types/pokedex';
import type { Pokemon } from '@/types/pokemon';
import type { Success, UserSuccess } from '@/types/success';
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props extends PageProps {
  auth: {
    user: User;
  };
  profile_user: User;
  pokedex?: Pokedex[];
  user_team?: Pokedex[];
  all_pokemons?: Pokemon[];
  leaderboards?: Leaderboards;
  claimed_successes?: UserSuccess[];
  successes?: Success[];
  progress?: {
    total: number;
    unlocked: number;
    claimed: number;
    unclaimed: number;
    percentage: number;
  };
}

const {
  profile_user,
  pokedex = [],
  user_team = [],
  leaderboards,
  claimed_successes = [],
} = defineProps<Props>();

const userTeamPokemons = computed(() => {
  if (!user_team || !Array.isArray(user_team)) return [];
  return user_team.slice(0, 6);
});

const formattedJoinDate = computed(() => {
  return new Date(profile_user.created_at).toLocaleDateString("fr-FR", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

const goBack = () => {
  window.history.back();
};

const goToMe = () => {
  router.visit('/me');
};
</script>

<template>

  <Head :title="`Profil de ${profile_user.username}`" />

  <div class="">
    <BackgroundEffects />

    <div class="relative z-50 h-screen w-screen overflow-x-hidden">
      <div
        class="lg:hidden flex items-center justify-between p-4 bg-base-100/60 backdrop-blur-sm border-b border-base-300/30 relative z-20">
        <Button @click="goBack" variant="outline" size="sm">
          ← Retour
        </Button>
        <h1 class="text-lg font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
          {{ profile_user.username }}
        </h1>
        <Button @click="goToMe" variant="ghost" size="sm">
          🏠
        </Button>
      </div>

      <div class="hidden lg:block h-screen w-screen relative">
        <div class="flex flex-col space-y-1 w-72 mx-auto">
          <div class="flex justify-center mt-4">
            <div class="z-20">
              <div
                class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 px-12 py-4 relative overflow-hidden shadow-2xl shadow-primary/10 min-w-[300px] xl:min-w-[600px] sm:min-w-[400px]">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                  <div class="absolute top-3 left-6 w-1 h-1 bg-success rounded-full animate-pulse opacity-60"></div>
                  <div
                    class="absolute top-6 right-8 w-1.5 h-1.5 bg-primary rounded-full animate-pulse delay-300 opacity-40">
                  </div>
                  <div
                    class="absolute bottom-4 left-10 w-1 h-1 bg-accent rounded-full animate-pulse delay-700 opacity-50">
                  </div>
                  <div
                    class="absolute top-8 right-12 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-30">
                  </div>
                </div>
                <div class="relative z-10 text-center">
                  <div
                    class="inline-flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-success/20 to-success/10 border border-success/30 rounded-full">
                    <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
                    <span class="font-bold tracking-wider text-sm">NIVEAU {{ profile_user.level || 1 }}</span>
                    <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
                  </div>
                </div>
                <div class="mt-4 mb-3">
                  <div class="h-2 bg-base-200/50 rounded-full overflow-hidden relative">
                    <div
                      class="h-full bg-gradient-to-r from-success/80 to-success rounded-full transition-all duration-1000"
                      :style="{ width: `${profile_user.experience_percentage || 0}%` }"></div>
                  </div>
                  <div class="flex justify-between mt-2 text-xs text-base-content/70">
                    <span>{{ profile_user.experience || 0 }} XP</span>
                    <span>{{ (profile_user.experience_for_next_level || 0) }} XP</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="z-20 mb-14">
            <TrainerProfile :user="profile_user" :trainer-image-id="2" :team-pokemons="userTeamPokemons" />
          </div>
        </div>

        <SideSection position="left" :top="true">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">🎯</span>
                NAVIGATION
              </h3>
            </div>
            <div class="p-3 space-y-2">
              <Button @click="goBack" variant="outline" size="sm" class="w-full">
                ← Retour
              </Button>
              <Button @click="goToMe" variant="ghost" size="sm" class="w-full">
                🏠 Mon Profil
              </Button>
            </div>
          </div>
        </SideSection>

        <SideSection position="left" :top="false" customClasses="top-52">
          <div class="space-y-4">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-primary/10 to-secondary/10 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">👤</span>
                  {{ profile_user.username }}
                </h3>
              </div>
              <div class="p-3 space-y-2">
                <div class="text-center mb-3">
                  <div class="text-xs text-base-content/60 mb-1">
                    Membre depuis {{ formattedJoinDate }}
                  </div>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Cash</span>
                  <span class="font-bold text-accent">{{ profile_user.cash || 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Pokémon</span>
                  <span class="font-bold text-warning">{{ pokedex.length }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Badges</span>
                  <span class="font-bold text-info">{{ claimed_successes.length }}</span>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <span class="text-lg">🏆</span>
                    BADGES
                  </h3>
                </div>
              </div>
              <div class="p-3">
                <div class="text-center mb-3">
                  <span class="text-xs text-base-content/70">{{ claimed_successes.length }} badges</span>
                </div>
                <div v-if="claimed_successes.length > 0" class="grid grid-cols-4 gap-1">
                  <div v-for="success in claimed_successes.slice(0, 8)" :key="success.id"
                       class="bg-gradient-to-br from-accent/20 to-accent/10 rounded-lg p-1 text-center"
                       :title="success.success.title">
                    <img :src="`/images/badges/${success.success.image}`" :alt="success.success.title"
                         class="w-full h-8 object-contain" />
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <div class="text-2xl mb-2">🎯</div>
                  <p class="text-sm text-base-content/50">Aucun badge</p>
                </div>
              </div>
            </div>
          </div>
        </SideSection>

        <SideSection position="right" :top="true">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">🥇</span>
                  CLASSEMENTS
                </h3>
              </div>
            </div>
            <div class="p-3 space-y-2">
              <div v-if="leaderboards">
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Niveau</span>
                  <span class="text-sm font-bold text-primary">#{{ leaderboards.experience?.current_user?.rank || 'N/A'
                  }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Pokémon</span>
                  <span class="text-sm font-bold text-secondary">#{{ leaderboards.pokemon_count?.current_user?.rank ||
                    'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Équipe PC</span>
                  <span class="text-sm font-bold text-accent">#{{ leaderboards.team_cp?.current_user?.rank || 'N/A'
                  }}</span>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-sm text-base-content/50">Aucun classement</p>
              </div>
            </div>
          </div>
        </SideSection>

        <SideSection position="right" :top="false" customClasses="top-48">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📊</span>
                COLLECTION
              </h3>
            </div>
            <div class="p-3 space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Pokémon</span>
                <span class="font-bold text-secondary">{{ pokedex.length }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Équipe</span>
                <span class="font-bold text-primary">{{ userTeamPokemons.length }}/6</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Badges</span>
                <span class="font-bold text-accent">{{ claimed_successes.length }}</span>
              </div>
            </div>
          </div>
        </SideSection>
      </div>

      <div class="lg:hidden p-4 space-y-4 pb-20 overflow-y-auto h-full">
        <div class="flex justify-center">
          <div
            class="bg-gradient-to-br from-base-100/60 to-base-200/40 w-full backdrop-blur-sm border-2 border-success/20 px-8 py-3 relative overflow-hidden shadow-lg rounded-xl">
            <div class="relative z-10 text-center">
              <div
                class="inline-flex items-center gap-2 px-3 py-1 bg-gradient-to-r from-success/20 to-success/10 border border-success/30 rounded-full">
                <div class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"></div>
                <span class="font-bold tracking-wider text-sm">NIVEAU {{ profile_user.level || 1 }}</span>
                <div class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"></div>
              </div>
            </div>
            <div class="mt-3 mb-2">
              <div class="h-1.5 bg-base-200/50 rounded-full overflow-hidden relative">
                <div
                  class="h-full bg-gradient-to-r from-success/80 to-success rounded-full transition-all duration-1000"
                  :style="{ width: `${profile_user.experience_percentage || 0}%` }"></div>
              </div>
              <div class="flex justify-between mt-1 text-xs text-base-content/70">
                <span>{{ profile_user.experience || 0 }} XP</span>
                <span>{{ (profile_user.experience_for_next_level || 0) }} XP</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-center bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/20 p-4">
          <div class="scale-90 -my-2">
            <TrainerProfile :user="profile_user" :trainer-image-id="2" :team-pokemons="userTeamPokemons" />
          </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 text-center border border-base-300/30">
            <div class="text-xl font-bold text-secondary mb-1">{{ pokedex.length }}</div>
            <div class="text-sm text-base-content/70">Pokémon</div>
          </div>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 text-center border border-base-300/30">
            <div class="text-xl font-bold text-accent mb-1">{{ claimed_successes.length }}</div>
            <div class="text-sm text-base-content/70">Badges</div>
          </div>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 text-center border border-base-300/30">
            <div class="text-xl font-bold text-primary mb-1">{{ userTeamPokemons.length }}/6</div>
            <div class="text-sm text-base-content/70">Équipe</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
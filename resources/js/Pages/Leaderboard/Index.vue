<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import PodiumCard from '@/Components/Leaderboard/PodiumCard.vue';
import LeaderboardRow from '@/Components/Leaderboard/LeaderboardRow.vue';
import type { PageProps } from '@/types';
import type { LeaderboardData } from '@/types/leaderboard';

interface Props extends PageProps {
  levelLeaderboard: LeaderboardData;
  cpLeaderboard: LeaderboardData;
  pokemonLeaderboard: LeaderboardData;
}

const { levelLeaderboard, cpLeaderboard, pokemonLeaderboard } = defineProps<Props>();

const activeTab = ref<'level' | 'cp' | 'pokemon'>('level');
const currentPage = ref(1);
const itemsPerPage = 20;

const currentLeaderboard = computed(() => {
  switch(activeTab.value) {
  case 'level': return levelLeaderboard;
  case 'cp': return cpLeaderboard;
  case 'pokemon': return pokemonLeaderboard;
  }
});

const topThree = computed(() => {
  return currentLeaderboard.value.top.slice(0, 3);
});

const restOfLeaderboard = computed(() => {
  return currentLeaderboard.value.top.slice(3);
});

const paginatedLeaderboard = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return restOfLeaderboard.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(restOfLeaderboard.value.length / itemsPerPage);
});

const tabLabels = {
  level: 'Niveau',
  cp: 'Team CP',
  pokemon: 'Pokémon'
};

const changeTab = (tab: 'level' | 'cp' | 'pokemon') => {
  activeTab.value = tab;
  currentPage.value = 1;
};

const changePage = (page: number) => {
  currentPage.value = page;
  document.getElementById('leaderboard-list')?.scrollIntoView({ behavior: 'smooth' });
};
</script>

<template>
  <Head title="Classements" />

  <div class="relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-8 px-4 mb-10">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏆 CLASSEMENTS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            TOP DRESSEURS
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-8">
          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-4">
              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider">NAVIGATION</h3>
                </div>
                <div class="p-3">
                  <Button @click="router.visit('/me')" variant="secondary" size="sm" class="w-full">
                    ← Retour à l'accueil
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider">VOS POSITIONS</h3>
                </div>
                <div class="p-3 space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-base-content">NIVEAU</span>
                    <span class="text-sm font-bold text-base-content">#{{ levelLeaderboard.current_user.rank }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-base-content">TEAM CP</span>
                    <span class="text-sm font-bold text-base-content">#{{ cpLeaderboard.current_user.rank }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-base-content">POKÉMON</span>
                    <span class="text-sm font-bold text-base-content">#{{ pokemonLeaderboard.current_user.rank }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="flex flex-wrap gap-2 mb-6">
              <Button
                v-for="(label, tab) in tabLabels"
                :key="tab"
                @click="changeTab(tab as 'level' | 'cp' | 'pokemon')"
                :variant="activeTab === tab ? 'primary' : 'secondary'"
                size="sm"
                class="flex-1 sm:flex-none"
              >
                {{ label }}
              </Button>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 sm:p-6 mb-6">
              <h2 class="text-lg font-bold text-center mb-6 text-base-content">
                🏆 Top 3 - {{ tabLabels[activeTab] }}
              </h2>
              
              <div class="flex flex-col gap-6 sm:hidden">
                <div v-if="topThree[0]" class="flex justify-center">
                  <PodiumCard 
                    :user="topThree[0]" 
                    :position="1" 
                    :type="activeTab"
                  />
                </div>
                <div v-if="topThree[1]" class="flex justify-center">
                  <PodiumCard 
                    :user="topThree[1]" 
                    :position="2" 
                    :type="activeTab"
                  />
                </div>
                <div v-if="topThree[2]" class="flex justify-center">
                  <PodiumCard 
                    :user="topThree[2]" 
                    :position="3" 
                    :type="activeTab"
                  />
                </div>
              </div>
              
              <div class="hidden sm:flex justify-center items-end gap-8">
                <div v-if="topThree[1]">
                  <PodiumCard 
                    :user="topThree[1]" 
                    :position="2" 
                    :type="activeTab"
                  />
                </div>
                
                <div v-if="topThree[0]">
                  <PodiumCard 
                    :user="topThree[0]" 
                    :position="1" 
                    :type="activeTab"
                  />
                </div>
                
                <div v-if="topThree[2]">
                  <PodiumCard 
                    :user="topThree[2]" 
                    :position="3" 
                    :type="activeTab"
                  />
                </div>
              </div>
            </div>

            <div id="leaderboard-list" class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                  <h3 class="text-sm font-bold tracking-wider">
                    CLASSEMENT COMPLET
                  </h3>
                  <div class="text-xs text-base-content/70">
                    {{ restOfLeaderboard.length }} dresseurs • Page {{ currentPage }}/{{ totalPages }}
                  </div>
                </div>
              </div>
              
              <div class="p-4 space-y-3">
                <div class="space-y-3 mb-4">
                  <LeaderboardRow
                    v-for="user in topThree"
                    :key="user.id"
                    :user="user"
                    :type="activeTab"
                    :is-current-user="user.id === $page.props.auth?.user?.id"
                  />
                </div>
                
                <div class="flex items-center gap-4 py-2">
                  <div class="flex-1 h-px bg-gradient-to-r from-transparent via-base-content/20 to-transparent"></div>
                  <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider">Reste du classement</span>
                  <div class="flex-1 h-px bg-gradient-to-r from-transparent via-base-content/20 to-transparent"></div>
                </div>
                
                <div class="space-y-3">
                  <LeaderboardRow
                    v-for="user in paginatedLeaderboard"
                    :key="user.id"
                    :user="user"
                    :type="activeTab"
                    :is-current-user="user.id === $page.props.auth?.user?.id"
                  />
                </div>

                <div v-if="totalPages > 1" class="flex justify-center pt-4">
                  <Pagination
                    :current-page="currentPage"
                    :total-pages="totalPages"
                    @page-changed="changePage"
                  />
                </div>
              </div>
            </div>

            <div 
              v-if="currentLeaderboard.current_user && currentLeaderboard.current_user.rank > 100"
              class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mt-4"
            >
              <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider">
                  VOTRE POSITION
                </h3>
              </div>
              <div class="p-4">
                <LeaderboardRow
                  :user="currentLeaderboard.current_user"
                  :type="activeTab"
                  :is-current-user="true"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
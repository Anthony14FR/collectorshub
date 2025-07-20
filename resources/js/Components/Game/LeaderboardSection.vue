<script setup lang="ts">
import type { Leaderboards } from '@/types/leaderboard';
import { Award, BookOpen, Coins, Medal, Star, Trophy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type TabType = 'cash' | 'experience' | 'pokemon_count';

interface Props {
  leaderboards: Leaderboards;
}

const { leaderboards } = defineProps<Props>();

const tabs: TabType[] = ['cash', 'experience', 'pokemon_count'];
const activeTab = ref<TabType>('cash');

const currentLeaderboard = computed(() => {
  return leaderboards[activeTab.value] || {
    top: [],
    current_user: {
      rank: 0,
      id: 0,
      username: '',
      level: 0,
      value: 0
    }
  };
});

const formatValue = (value: number | undefined, type: TabType) => {
  const safeValue = value || 0;
  if (type === 'cash') {
    return safeValue.toLocaleString() + ' ₽';
  } else if (type === 'experience') {
    return safeValue.toLocaleString() + ' XP';
  } else {
    return safeValue.toString();
  }
};

const getTabTitle = (tab: TabType) => {
  switch (tab) {
  case 'cash':
    return 'Argent';
  case 'experience':
    return 'Expérience';
  case 'pokemon_count':
    return 'Pokémons';
  default:
    return '';
  }
};

const getTabIcon = (tab: TabType) => {
  switch (tab) {
  case 'cash':
    return Coins;
  case 'experience':
    return Star;
  case 'pokemon_count':
    return BookOpen;
  default:
    return Trophy;
  }
};
</script>

<template>
  <div class="bg-base-100/80 backdrop-blur-sm rounded-xl p-4 border border-base-300">
    <div class="tabs tabs-boxed mb-4">
      <button
        v-for="tab in tabs"
        :key="tab"
        :class="[
          'tab flex items-center gap-2',
          activeTab === tab ? 'tab-active' : ''
        ]"
        @click="activeTab = tab"
      >
        <component :is="getTabIcon(tab)" :size="16" />
        <span class="hidden sm:inline">{{ getTabTitle(tab) }}</span>
      </button>
    </div>

    <div class="space-y-4">
      <div class="bg-primary/10 rounded-lg p-3 border border-primary/20">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-primary">Votre position</span>
          <span class="text-lg font-bold text-primary">#{{ currentLeaderboard.current_user.rank }}</span>
        </div>
        <div class="flex items-center justify-between mt-1">
          <span class="text-sm text-base-content/70">{{ currentLeaderboard.current_user.username }}</span>
          <span class="text-sm font-semibold">{{ formatValue(currentLeaderboard.current_user.value, activeTab) }}</span>
        </div>
      </div>

      <div class="space-y-2 max-h-64 overflow-y-auto">
        <div class="text-sm font-medium text-base-content/70 mb-2">Top 100</div>
        <div
          v-for="entry in currentLeaderboard.top"
          :key="entry.id"
          :class="[
            'flex items-center justify-between p-2 rounded-lg transition-colors',
            entry.id === currentLeaderboard.current_user.id 
              ? 'bg-primary/20 border border-primary/30' 
              : 'bg-base-200/50 hover:bg-base-200/70'
          ]"
        >
          <div class="flex items-center gap-3">
            <div :class="[
              'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold',
              entry.rank === 1 ? 'bg-yellow-500 text-white' :
              entry.rank === 2 ? 'bg-gray-400 text-white' :
              entry.rank === 3 ? 'bg-orange-600 text-white' :
              'bg-base-300 text-base-content'
            ]">
              <Trophy v-if="entry.rank === 1" :size="12" />
              <Medal v-else-if="entry.rank === 2" :size="12" />
              <Award v-else-if="entry.rank === 3" :size="12" />
              <span v-else>{{ entry.rank }}</span>
            </div>
            <div>
              <div class="text-sm font-medium">{{ entry.username }}</div>
              <div class="text-xs text-base-content/60">Niveau {{ entry.level }}</div>
            </div>
          </div>
          <div class="text-sm font-semibold">
            {{ formatValue(entry.value, activeTab) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
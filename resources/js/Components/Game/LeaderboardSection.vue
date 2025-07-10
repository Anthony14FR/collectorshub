<script setup lang="ts">
import { ref, computed } from 'vue';
import type { Leaderboards } from '@/types/leaderboard';

interface Props {
    leaderboards: Leaderboards;
}

const { leaderboards } = defineProps<Props>();

const activeTab = ref<'cash' | 'experience' | 'pokemon_count'>('cash');

const currentLeaderboard = computed(() => {
    return leaderboards[activeTab.value];
});

const formatValue = (value: number, type: string) => {
    if (type === 'cash') {
        return value.toLocaleString() + ' ₽';
    } else if (type === 'experience') {
        return value.toLocaleString() + ' XP';
    } else {
        return value.toString();
    }
};

const getTabTitle = (tab: string) => {
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

const getTabIcon = (tab: string) => {
    switch (tab) {
        case 'cash':
            return '💰';
        case 'experience':
            return '⭐';
        case 'pokemon_count':
            return '📚';
        default:
            return '';
    }
};
</script>

<template>
    <div class="bg-base-100/80 backdrop-blur-sm rounded-xl p-4 border border-base-300">
        <div class="tabs tabs-boxed mb-4">
            <button
                v-for="tab in ['cash', 'experience', 'pokemon_count']"
                :key="tab"
                :class="[
                    'tab flex items-center gap-2',
                    activeTab === tab ? 'tab-active' : ''
                ]"
                @click="activeTab = tab as any"
            >
                <span>{{ getTabIcon(tab) }}</span>
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
                            {{ entry.rank }}
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
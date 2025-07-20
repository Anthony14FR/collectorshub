<template>
  <div class="text-xs">
    <div v-if="config.category === 'level_rewards'" class="space-y-2">
      <div v-for="(reward, key) in config.value" :key="key" class="flex items-center justify-between p-2 bg-base-200/50 rounded">
        <div class="flex items-center gap-2">
          <span class="font-bold text-primary">{{ getMilestoneLabel(key) }}</span>
        </div>
        <div class="flex items-center gap-3 text-xs">
          <span v-if="reward.cash > 0" class="flex items-center gap-1">
            <component :is="DollarSign" :size="12" class="text-success" />
            {{ reward.cash }}
          </span>
          <span v-if="reward.pokeballs > 0" class="flex items-center gap-1">
            <component :is="Circle" :size="12" class="text-info" />
            {{ reward.pokeballs }}
          </span>
          <span v-if="reward.masterballs > 0" class="flex items-center gap-1">
            <component :is="Sparkles" :size="12" class="text-warning" />
            {{ reward.masterballs }}
          </span>
        </div>
      </div>
    </div>

    <div v-else-if="config.category === 'rarity_probabilities'" class="space-y-2">
      <div v-for="(ball, ballType) in config.value" :key="ballType" class="border border-base-300/30 rounded p-2">
        <div class="font-bold text-primary mb-2">{{ ballType }}</div>
        <div class="grid grid-cols-2 gap-2">
          <div v-for="(prob, rarity) in ball" :key="rarity" class="flex items-center justify-between p-1 bg-base-200/30 rounded">
            <span class="flex items-center gap-1">
              <div class="w-2 h-2 rounded-full" :class="getRarityColor(rarity)"></div>
              {{ getRarityLabel(rarity) }}
            </span>
            <span class="font-mono">{{ prob }}%</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="config.category === 'xp_rewards'" class="space-y-2">
      <div v-for="(rewards, rarity) in config.value" :key="rarity" class="border border-base-300/30 rounded p-2">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-3 h-3 rounded-full" :class="getRarityColor(rarity)"></div>
          <span class="font-bold text-primary">{{ getRarityLabel(rarity) }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-xs">
          <div class="flex items-center justify-between p-1 bg-base-200/30 rounded">
            <span>Base</span>
            <span class="font-mono">{{ rewards.base }}</span>
          </div>
          <div class="flex items-center justify-between p-1 bg-base-200/30 rounded">
            <span>Shiny</span>
            <span class="font-mono">{{ rewards.shiny }}</span>
          </div>
          <div class="flex items-center justify-between p-1 bg-base-200/30 rounded">
            <span>Bonus</span>
            <span class="font-mono">{{ rewards.first_catch_bonus }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-xs font-mono text-base-content/80 overflow-x-auto">
      {{ JSON.stringify(config.value, null, 2) }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { DollarSign, Circle, Sparkles } from 'lucide-vue-next'

interface GameConfig {
  id: number
  category: string
  key: string
  value: any
  description: string
}

interface Props {
  config: GameConfig
}

defineProps<Props>()

const getMilestoneLabel = (key: string): string => {
  const labels: Record<string, string> = {
    'milestone_5': 'Niveau 5',
    'milestone_10': 'Niveau 10',
    'milestone_25': 'Niveau 25',
    'milestone_50': 'Niveau 50',
    'regular_level': 'Niveau normal'
  }
  return labels[key] || key
}

const getRarityLabel = (rarity: string): string => {
  const labels: Record<string, string> = {
    'normal': 'Normal',
    'rare': 'Rare',
    'epic': 'Épique',
    'legendary': 'Légendaire'
  }
  return labels[rarity] || rarity
}

const getRarityColor = (rarity: string): string => {
  const colors: Record<string, string> = {
    'normal': 'bg-gray-400',
    'rare': 'bg-blue-400',
    'epic': 'bg-purple-400',
    'legendary': 'bg-yellow-400'
  }
  return colors[rarity] || 'bg-gray-400'
}
</script>

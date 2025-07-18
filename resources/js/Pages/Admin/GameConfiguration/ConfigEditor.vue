<template>
  <div class="space-y-6">
    <div v-if="config.category === 'level_rewards'" class="space-y-4">
      <div class="text-lg font-bold text-primary mb-4">🎯 Récompenses de niveau</div>

      <div v-for="(_, key) in value" :key="key" class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-lg">{{ getMilestoneIcon(String(key)) }}</span>
          <h4 class="font-bold text-base-content">{{ getMilestoneLabel(String(key)) }}</h4>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">💰 Cash</label>
            <input v-model.number="value[key].cash" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">⚪ Pokéballs</label>
            <input v-model.number="value[key].pokeballs" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">🟣 Masterballs</label>
            <input v-model.number="value[key].masterballs" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="config.category === 'rarity_probabilities'" class="space-y-4">
      <div class="text-lg font-bold text-primary mb-4">🎲 Probabilités de rareté</div>

      <div v-for="(ball, ballType) in value" :key="ballType"
           class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-lg">{{ getBallIcon(String(ballType)) }}</span>
          <h4 class="font-bold text-base-content">{{ String(ballType) }}</h4>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div v-for="(_, rarity) in ball" :key="rarity" class="flex items-center gap-3">
            <div class="flex items-center gap-2 min-w-20">
              <span class="w-3 h-3 rounded-full" :class="getRarityColor(String(rarity))"></span>
              <span class="text-sm font-medium">{{ getRarityLabel(String(rarity)) }}</span>
            </div>
            <div class="flex-1">
              <input v-model.number="value[ballType][rarity]" type="number" step="0.1" min="0" max="100"
                     class="input input-bordered input-sm w-full" />
            </div>
            <span class="text-xs text-base-content/70">%</span>
          </div>
        </div>

        <div class="mt-3 p-2 bg-base-300/30 rounded text-xs">
          <span class="font-medium">Total: {{ getTotalProbability(ball) }}%</span>
          <span v-if="getTotalProbability(ball) !== 100" class="text-error ml-2">
            (doit être égal à 100%)
          </span>
        </div>
      </div>
    </div>

    <div v-else-if="config.category === 'xp_rewards'" class="space-y-4">
      <div class="text-lg font-bold text-primary mb-4">⭐ Gains d'XP</div>

      <div v-for="(_, rarity) in value" :key="rarity" class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
        <div class="flex items-center gap-2 mb-3">
          <span class="w-4 h-4 rounded-full" :class="getRarityColor(String(rarity))"></span>
          <h4 class="font-bold text-base-content">{{ getRarityLabel(String(rarity)) }}</h4>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">Base XP</label>
            <input v-model.number="value[rarity].base" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">Shiny XP</label>
            <input v-model.number="value[rarity].shiny" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-base-content mb-1">Bonus première capture</label>
            <input v-model.number="value[rarity].first_catch_bonus" type="number" min="0"
                   class="input input-bordered input-sm w-full" />
          </div>
        </div>
      </div>
    </div>

    <div v-else class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
      <div class="text-lg font-bold text-primary mb-4">⚙️ Configuration JSON</div>
      <textarea v-model="jsonValue" class="textarea textarea-bordered w-full h-64 font-mono text-sm"
                placeholder="Configuration JSON..."></textarea>
      <div v-if="jsonError" class="mt-2 p-2 bg-error/10 border border-error/20 rounded text-error text-sm">
        {{ jsonError }}
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-4 border-t border-base-300/20">
      <Button @click="$emit('cancel')" variant="outline">
        Annuler
      </Button>
      <Button @click="handleSave" variant="primary" :disabled="hasErrors">
        Sauvegarder
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/Components/UI/Button.vue'
import { computed, ref, watch } from 'vue'

interface GameConfig {
  id: number
  category: string
  key: string
  value: any
  description: string
}

interface Props {
  config: GameConfig
  value: any
}

interface Emits {
  (e: 'update:value', value: any): void
  (e: 'save'): void
  (e: 'cancel'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const jsonValue = ref('')
const jsonError = ref('')

const hasErrors = computed(() => {
  if (props.config.category === 'rarity_probabilities') {
    return Object.values(props.value).some((ball: any) => getTotalProbability(ball) !== 100)
  }
  return !!jsonError.value
})

watch(() => props.value, (newValue) => {
  if (props.config.category !== 'rarity_probabilities' && props.config.category !== 'level_rewards' && props.config.category !== 'xp_rewards') {
    jsonValue.value = JSON.stringify(newValue, null, 2)
  }
}, { immediate: true })

watch(jsonValue, (newValue) => {
  if (props.config.category !== 'rarity_probabilities' && props.config.category !== 'level_rewards' && props.config.category !== 'xp_rewards') {
    try {
      const parsed = JSON.parse(newValue)
      emit('update:value', parsed)
      jsonError.value = ''
    } catch (error) {
      jsonError.value = 'JSON invalide'
    }
  }
})

const getMilestoneIcon = (key: string): string => {
  const icons: Record<string, string> = {
    'milestone_5': '🎯',
    'milestone_10': '🏆',
    'milestone_25': '👑',
    'milestone_50': '💎',
    'regular_level': '⭐'
  }
  return icons[key] || '⚙️'
}

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

const getBallIcon = (ballType: string): string => {
  const icons: Record<string, string> = {
    'Pokeball': '⚪',
    'Masterball': '🟣'
  }
  return icons[ballType] || '🎾'
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

const getTotalProbability = (ball: any): number => {
  return Object.values(ball).reduce((total: number, prob: any) => total + Number(prob), 0)
}

const handleSave = () => {
  if (hasErrors.value) return
  emit('save')
}
</script>
<script setup lang="ts">
interface GameConfig {
  id: number;
  category: string;
  key: string;
  value: any;
  description: string;
}

interface Props {
  config: GameConfig;
}

const props = defineProps<Props>();

const getMilestoneLabel = (key: string): string => {
  const labels: Record<string, string> = {
    'milestone_5': 'Niveau 5',
    'milestone_10': 'Niveau 10',
    'milestone_25': 'Niveau 25',
    'milestone_50': 'Niveau 50',
    'milestone_100': 'Niveau 100',
    'regular_level': 'Niveau normal'
  };
  return labels[key] || key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const getRarityLabel = (rarity: string): string => {
  const labels: Record<string, string> = {
    'normal': 'Normal',
    'rare': 'Rare',
    'epic': 'Épique',
    'legendary': 'Légendaire'
  };
  return labels[rarity] || rarity;
};

const getRarityColor = (rarity: string): string => {
  const colors: Record<string, string> = {
    'normal': 'bg-base-content text-base-100',
    'rare': 'bg-info text-info-content',
    'epic': 'bg-warning text-warning-content',
    'legendary': 'bg-error text-error-content'
  };
  return colors[rarity] || 'bg-base-200 text-base-content';
};

const getBallTypeIcon = (ballType: string): string => {
  const icons: Record<string, string> = {
    'Pokeball': '⚪',
    'Greatball': '🔵',
    'Ultraball': '🟡',
    'Masterball': '🟣'
  };
  return icons[ballType] || '⚪';
};

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('fr-FR').format(amount);
};

const formatPercentage = (value: number): string => {
  return `${value}%`;
};

const formatRate = (value: number): string => {
  if (value < 1) {
    return `${(value * 100).toFixed(2)}%`;
  }
  return `${value}`;
};

const isRewardsConfig = (): boolean => {
  return props.config.category === 'level_rewards' ||
    props.config.key.includes('reward') ||
    (typeof props.config.value === 'object' &&
      props.config.value !== null &&
      ('cash' in props.config.value || 'pokeballs' in props.config.value || 'milestones' in props.config.value));
};

const isProbabilityConfig = (): boolean => {
  return props.config.category === 'rarity_probabilities' ||
    props.config.key.includes('probability') ||
    props.config.key.includes('rate') ||
    (typeof props.config.value === 'object' &&
      props.config.value !== null &&
      Object.values(props.config.value).every((v: any) => typeof v === 'number' && v >= 0 && v <= 100));
};

const isXpConfig = (): boolean => {
  return props.config.category === 'xp_rewards' ||
    props.config.key.includes('xp') ||
    (typeof props.config.value === 'object' &&
      props.config.value !== null &&
      ('base_xp' in props.config.value || 'bonus_xp' in props.config.value));
};

const isShinyConfig = (): boolean => {
  return props.config.category === 'shiny_rate' ||
    props.config.key.includes('shiny') ||
    (typeof props.config.value === 'number' && props.config.value > 0 && props.config.value <= 1);
};

const isBallTypesConfig = (): boolean => {
  return typeof props.config.value === 'object' &&
    props.config.value !== null &&
    Object.keys(props.config.value).some(key => ['Pokeball', 'Greatball', 'Ultraball', 'Masterball'].includes(key));
};
</script>

<template>
  <div class="space-y-3">
    <div v-if="isRewardsConfig() && config.value.milestones" class="space-y-3">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🏆</span>
        Récompenses par niveau
      </div>
      <div class="grid grid-cols-1 gap-2">
        <div
          v-for="(rewards, milestone) in config.value.milestones"
          :key="milestone"
          class="bg-base-200/50 rounded-lg p-3 border border-base-300/20"
        >
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-primary">{{ getMilestoneLabel(milestone) }}</span>
            <div class="flex gap-1">
              <span v-if="rewards.cash" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-warning/20 text-warning">
                💰 {{ formatCurrency(rewards.cash) }}
              </span>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div v-if="rewards.pokeballs" class="flex items-center justify-between bg-info/10 rounded p-2">
              <span>⚪ Pokéballs</span>
              <span class="font-mono font-bold">{{ rewards.pokeballs }}</span>
            </div>
            <div v-if="rewards.masterballs" class="flex items-center justify-between bg-error/10 rounded p-2">
              <span>🟣 Masterballs</span>
              <span class="font-mono font-bold">{{ rewards.masterballs }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="isRewardsConfig() && typeof config.value === 'object'" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🎁</span>
        Récompenses
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div v-if="config.value.cash" class="bg-warning/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-warning">{{ formatCurrency(config.value.cash) }}</div>
          <div class="text-xs text-base-content/70">Cash</div>
        </div>
        <div v-if="config.value.pokeballs" class="bg-info/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-info">{{ config.value.pokeballs }}</div>
          <div class="text-xs text-base-content/70">Pokéballs</div>
        </div>
        <div v-if="config.value.masterballs" class="bg-error/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-error">{{ config.value.masterballs }}</div>
          <div class="text-xs text-base-content/70">Masterballs</div>
        </div>
        <div v-if="config.value.first_catch_bonus" class="bg-success/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-success">{{ config.value.first_catch_bonus }}</div>
          <div class="text-xs text-base-content/70">Bonus 1ère capture</div>
        </div>
      </div>
    </div>

    <div v-else-if="isBallTypesConfig()" class="space-y-3">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🎯</span>
        Probabilités par type de balle
      </div>
      <div class="space-y-2">
        <div
          v-for="(probabilities, ballType) in config.value"
          :key="ballType"
          class="bg-base-200/50 rounded-lg p-3 border border-base-300/20"
        >
          <div class="flex items-center gap-2 mb-2">
            <span class="text-lg">{{ getBallTypeIcon(ballType) }}</span>
            <span class="text-xs font-bold text-base-content">{{ ballType }}</span>
          </div>
          <div class="grid grid-cols-2 gap-1">
            <div
              v-for="(probability, rarity) in probabilities"
              :key="rarity"
              class="flex items-center justify-between text-xs"
            >
              <div class="flex items-center gap-1">
                <div :class="['w-2 h-2 rounded-full', getRarityColor(rarity)]"></div>
                <span>{{ getRarityLabel(rarity) }}</span>
              </div>
              <span class="font-mono font-bold">{{ formatPercentage(probability) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="isProbabilityConfig()" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🎲</span>
        Probabilités de rareté
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div
          v-for="(probability, rarity) in config.value"
          :key="rarity"
          class="bg-base-200/50 rounded-lg p-2 text-center border border-base-300/20"
        >
          <div class="flex items-center justify-center gap-1 mb-1">
            <div :class="['w-3 h-3 rounded-full', getRarityColor(rarity)]"></div>
            <span class="text-xs font-bold">{{ getRarityLabel(rarity) }}</span>
          </div>
          <div class="text-lg font-bold font-mono">{{ formatPercentage(probability) }}</div>
        </div>
      </div>
    </div>

    <div v-else-if="isXpConfig()" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>⭐</span>
        Gains d'expérience
      </div>
      <div class="grid grid-cols-2 gap-2">
        <div v-if="config.value.base_xp" class="bg-info/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-info">{{ config.value.base_xp }}</div>
          <div class="text-xs text-base-content/70">XP de base</div>
        </div>
        <div v-if="config.value.bonus_xp" class="bg-success/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-success">{{ config.value.bonus_xp }}</div>
          <div class="text-xs text-base-content/70">XP bonus</div>
        </div>
        <div v-if="config.value.first_catch_bonus" class="bg-warning/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-warning">{{ config.value.first_catch_bonus }}</div>
          <div class="text-xs text-base-content/70">Bonus 1ère capture</div>
        </div>
        <div v-if="config.value.shiny_bonus" class="bg-error/10 rounded-lg p-2 text-center">
          <div class="text-lg font-bold text-error">{{ config.value.shiny_bonus }}</div>
          <div class="text-xs text-base-content/70">Bonus shiny</div>
        </div>
      </div>
    </div>

    <div v-else-if="isShinyConfig()" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>✨</span>
        Taux de Pokémon shiny
      </div>
      <div class="bg-gradient-to-r from-warning/20 to-error/20 rounded-lg p-4 text-center border border-warning/30">
        <div class="text-2xl font-bold font-mono text-warning mb-1">
          {{ formatRate(config.value) }}
        </div>
        <div class="text-xs text-base-content/70">
          {{ config.value < 1 ? 'Probabilité par capture' : 'Taux par capture' }}
        </div>
      </div>
    </div>

    <div v-else-if="typeof config.value === 'number'" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>📊</span>
        Valeur numérique
      </div>
      <div class="bg-secondary/10 rounded-lg p-4 text-center border border-secondary/30">
        <div class="text-2xl font-bold font-mono text-secondary mb-1">
          {{ config.value < 1 ? formatRate(config.value) : formatCurrency(config.value) }}
        </div>
        <div class="text-xs text-base-content/70">{{ config.key }}</div>
      </div>
    </div>

    <div v-else-if="typeof config.value === 'string'" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>📝</span>
        Valeur textuelle
      </div>
      <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/20">
        <div class="font-mono text-sm text-base-content">{{ config.value }}</div>
      </div>
    </div>

    <div v-else-if="typeof config.value === 'boolean'" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🔘</span>
        Valeur booléenne
      </div>
      <div class="bg-base-200/50 rounded-lg p-3 text-center border border-base-300/20">
        <div :class="['text-2xl font-bold mb-1', config.value ? 'text-success' : 'text-error']">
          {{ config.value ? '✅ Activé' : '❌ Désactivé' }}
        </div>
      </div>
    </div>

    <div v-else-if="Array.isArray(config.value)" class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>📋</span>
        Liste ({{ config.value.length }} élément{{ config.value.length > 1 ? 's' : '' }})
      </div>
      <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/20 max-h-32 overflow-y-auto">
        <div class="space-y-1">
          <div
            v-for="(item, index) in config.value"
            :key="index"
            class="flex items-center justify-between text-xs bg-base-100/50 rounded p-2"
          >
            <span class="text-base-content/70">#{{ index + 1 }}</span>
            <span class="font-mono text-base-content">{{ JSON.stringify(item) }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="space-y-2">
      <div class="text-xs font-semibold text-base-content/80 mb-2 flex items-center gap-2">
        <span>🗂️</span>
        Configuration JSON
      </div>
      <div class="bg-base-200/50 rounded-lg p-3 border border-base-300/20 max-h-48 overflow-y-auto">
        <pre class="text-xs font-mono text-base-content whitespace-pre-wrap">{{ JSON.stringify(config.value, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

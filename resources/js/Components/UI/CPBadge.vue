<script setup lang="ts">
interface Props {
  cp: number;
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
  variant?: 'filled' | 'outlined' | 'gradient';
  showLabel?: boolean;
}

const { cp, size = 'md', variant = 'gradient', showLabel = true } = defineProps<Props>();

const sizeClasses = {
  xs: 'h-4 px-1.5 text-xs',
  sm: 'h-5 px-2 text-xs', 
  md: 'h-6 px-3 text-sm',
  lg: 'h-7 px-3 text-base',
  xl: 'h-8 px-4 text-lg'
};

const getVariantClasses = () => {
  switch(variant) {
    case 'filled':
      return 'bg-orange-500/20 text-orange-400 border-orange-500/30';
    case 'outlined':
      return 'bg-transparent text-orange-400 border-orange-500/50 border-2';
    case 'gradient':
      return 'bg-gradient-to-r from-orange-500/20 to-yellow-500/20 text-orange-300 border-orange-400/40 shadow-lg shadow-orange-500/10';
    default:
      return 'bg-orange-500/20 text-orange-400 border-orange-500/30';
  }
};

const formatCP = (cp: number): string => {
  if (cp >= 1000000) {
    return `${(cp / 1000000).toFixed(1)}M`;
  } else if (cp >= 1000) {
    return `${(cp / 1000).toFixed(1)}K`;
  }
  return cp.toString();
};
</script>

<template>
  <div 
    :class="[
      'w-auto backdrop-blur-sm rounded-full flex items-center justify-center border font-bold tracking-wide transition-all duration-300',
      sizeClasses[size],
      getVariantClasses()
    ]"
  >
    <span class="mr-1">⚔️</span>
    <span v-if="showLabel" class="mr-1">CP</span>
    <span class="font-mono">{{ formatCP(cp) }}</span>
  </div>
</template>
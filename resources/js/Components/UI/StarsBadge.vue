<script setup lang="ts">
interface Props {
  stars: number;
  size?: 'xs' | 'sm' | 'md' | 'lg';
  maxStars?: number;
}

const { stars, size = 'sm', maxStars = 5 } = defineProps<Props>();

const sizeClasses = {
  xs: 'h-4 px-1.5 text-xs',
  sm: 'h-5 px-2 text-xs',
  md: 'h-6 px-3 text-sm',
  lg: 'h-7 px-3 text-base'
};

const displayStars = Math.min(Math.max(stars, 0), maxStars);
</script>

<template>
  <div 
    :class="[
      'w-auto bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30',
      sizeClasses[size]
    ]"
  >
    <span class="text-yellow-400 font-bold">{{ displayStars }}</span>
    <span class="text-yellow-400 ml-1">⭐</span>
  </div>
</template> 
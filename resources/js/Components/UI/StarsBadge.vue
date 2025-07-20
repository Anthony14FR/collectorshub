<script setup lang="ts">
import { Star } from 'lucide-vue-next';

interface Props {
  stars: number;
  size?: 'xxs' | 'xs' | 'sm' | 'md' | 'lg';
  maxStars?: number;
}

const { stars, size = 'sm', maxStars = 6 } = defineProps<Props>();

const sizeClasses = {
  xxs: 'h-3 px-1.5 text-[8px]',
  xs: 'h-4 px-1.5 text-xs',
  sm: 'h-5 px-2 text-xs',
  md: 'h-6 px-3 text-sm',
  lg: 'h-7 px-3 text-base'
};

const displayStars = Math.min(Math.max(stars, 0), maxStars);
const isMaxStars = displayStars === 6;
</script>

<template>
  <div 
    :class="[
      'w-auto backdrop-blur-sm rounded-full flex items-center justify-center border relative overflow-hidden',
      isMaxStars 
        ? 'bg-gradient-to-r from-purple-500/20 via-pink-500/20 to-yellow-500/20 border-purple-500/30' 
        : 'bg-yellow-500/20 border-yellow-500/30',
      sizeClasses[size]
    ]"
  >
    <div v-if="isMaxStars" class="absolute inset-0 pointer-events-none">
      <div class="sparkle sparkle-1"></div>
      <div class="sparkle sparkle-2"></div>
      <div class="sparkle sparkle-3"></div>
      <div class="sparkle sparkle-4"></div>
      <div class="sparkle sparkle-5"></div>
    </div>

    <span v-if="!isMaxStars" class="text-yellow-400 font-bold">{{ displayStars }}</span>
    <span 
      v-if="isMaxStars" 
      class="text-transparent bg-gradient-to-r from-purple-400 via-pink-400 to-yellow-400 bg-clip-text font-bold ultimate-star relative z-10 text-xl flex items-center justify-center"
    >
      <Star :size="16" class="fill-current" />
    </span>
    <span 
      v-else
      class="text-yellow-400 ml-1 flex items-center justify-center"
    >
      <Star :size="12" class="fill-current" />
    </span>
  </div>
</template>

<style scoped>
.ultimate-star {
  background-size: 200% 200%;
  animation: rainbow 3s ease-in-out infinite;
}

@keyframes rainbow {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.sparkle {
  position: absolute;
  width: 2px;
  height: 2px;
  background: white;
  border-radius: 50%;
  opacity: 0;
  animation: sparkle 2s infinite;
}

.sparkle-1 {
  top: 20%;
  left: 15%;
  animation-delay: 0s;
}

.sparkle-2 {
  top: 70%;
  left: 80%;
  animation-delay: 0.4s;
}

.sparkle-3 {
  top: 30%;
  right: 20%;
  animation-delay: 0.8s;
}

.sparkle-4 {
  bottom: 25%;
  left: 25%;
  animation-delay: 1.2s;
}

.sparkle-5 {
  top: 50%;
  right: 15%;
  animation-delay: 1.6s;
}

@keyframes sparkle {
  0%, 100% {
    opacity: 0;
    transform: scale(0);
  }
  50% {
    opacity: 1;
    transform: scale(1);
  }
}

.sparkle::before {
  content: '';
  position: absolute;
  top: -1px;
  left: -1px;
  right: -1px;
  bottom: -1px;
  background: linear-gradient(45deg, transparent, rgba(255,255,255,0.8), transparent);
  border-radius: 50%;
  animation: twinkle 2s infinite;
}

@keyframes twinkle {
  0%, 100% {
    opacity: 0;
    transform: rotate(0deg);
  }
  50% {
    opacity: 1;
    transform: rotate(180deg);
  }
}
</style> 
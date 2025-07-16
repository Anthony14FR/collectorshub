<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const userBackground = computed(() => page.props.auth?.user?.background);
</script>

<template>
  <div v-if="userBackground" class="fixed inset-0 z-0">
    <img 
      :src="userBackground" 
      alt="Background" 
      class="w-full h-full object-cover"
    />
    <div class="absolute inset-0 bg-black/50"></div>
  </div>
  <div v-else class="fixed inset-0 z-0 bg-gradient-to-br from-primary/10 via-base-100 to-secondary/10">
    <div class="absolute inset-0 overflow-hidden">
      <div
        class="absolute w-96 h-96 rounded-full bg-primary/5 blur-3xl animate-float"
        style="top: -10%; left: -10%; animation-delay: 0s"
      ></div>
      <div
        class="absolute w-96 h-96 rounded-full bg-secondary/5 blur-3xl animate-float-reverse"
        style="bottom: -10%; right: -10%; animation-delay: 2s"
      ></div>
      <div
        class="absolute w-64 h-64 rounded-full bg-accent/5 blur-3xl animate-float"
        style="top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 4s"
      ></div>
    </div>
  </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0) scale(1);
  }
  33% {
    transform: translateY(-30px) translateX(20px) scale(1.05);
  }
  66% {
    transform: translateY(20px) translateX(-20px) scale(0.95);
  }
}

@keyframes float-reverse {
  0%, 100% {
    transform: translateY(0) translateX(0) scale(1);
  }
  33% {
    transform: translateY(30px) translateX(-20px) scale(0.95);
  }
  66% {
    transform: translateY(-20px) translateX(20px) scale(1.05);
  }
}

.animate-float {
  animation: float 20s ease-in-out infinite;
}

.animate-float-reverse {
  animation: float-reverse 25s ease-in-out infinite;
}
</style>
<script setup>
import Button from '@/Components/UI/Button.vue'
import { Link, router } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref } from 'vue'

const scrolled = ref(false)

const handleScroll = () => {
  scrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const goToLogin = () => {
  router.visit('/login');
};
</script>

<template>
  <nav class="fixed top-4 left-4 right-4 z-50 rounded-3xl transition-all duration-500 ease-out hidden md:block" :style="{
    background: scrolled
      ? 'linear-gradient(135deg, rgb(var(--color-primary) / 0.1), rgb(var(--color-secondary) / 0.05), rgb(var(--color-accent) / 0.1))'
      : 'transparent',
    backdropFilter: scrolled ? 'blur(20px)' : 'blur(0px)',
    boxShadow: scrolled ? '0 8px 32px rgb(var(--color-primary) / 0.15), inset 0 1px 0 rgb(var(--color-primary) / 0.2)' : 'none',
    border: scrolled ? '1px solid rgb(var(--color-primary) / 0.2)' : 'none',
    transition: 'all 500ms ease-out'
  }">
    <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none" v-if="scrolled">
      <div class="absolute top-2 left-8 w-1.5 h-1.5 bg-primary rounded-full animate-pulse opacity-60"></div>
      <div class="absolute top-4 right-12 w-1 h-1 bg-secondary rounded-full animate-pulse delay-300 opacity-50"></div>
      <div class="absolute bottom-3 left-16 w-2 h-2 bg-accent rounded-full animate-pulse delay-700 opacity-40"></div>
      <div class="absolute top-3 right-20 w-1 h-1 bg-info rounded-full animate-pulse delay-500 opacity-60"></div>
      <div class="absolute bottom-2 right-8 w-1.5 h-1.5 bg-success rounded-full animate-pulse delay-1000 opacity-50">
      </div>
    </div>

    <div class="relative px-8 py-4 flex items-center justify-between">
      <Link href="/" class="flex items-center group">
        <div class="relative">
          <div class="mr-4 group-hover:scale-110 transition-all duration-300">
            <img src="/images/logo.png" alt="Logo"
                 class="w-12 h-12 object-contain rounded-2xl shadow-lg shadow-primary/30">
          </div>
          <div class="absolute -top-1 -right-1 w-3 h-3 bg-accent rounded-full animate-pulse opacity-70"></div>
        </div>
        <div class="flex flex-col">
          <span
            class="text-lg font-bold bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent group-hover:from-accent group-hover:via-primary group-hover:to-secondary transition-all duration-300">
            Collector's Hub
          </span>
          <span class="text-xs text-base-content/60 -mt-1">Collection Pokémon</span>
        </div>
      </Link>

      <div class="flex items-center">
        <Link href="/login">
          <Button variant="primary" icon="user" size="md">
            Connexion
          </Button>
        </Link>
      </div>
    </div>
  </nav>

  <div class="fixed top-6 right-6 z-50 md:hidden">
    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button"
           class="group btn btn-circle w-14 h-14 bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20 backdrop-blur-2xl shadow-2xl shadow-primary/20 cursor-pointer hover:scale-105 transition-all duration-300">
        <svg class="w-6 h-6 text-primary group-hover:text-secondary transition-colors duration-300" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </div>
      <ul tabindex="0"
          class="menu menu-sm dropdown-content mt-4 z-[1] p-4 shadow-2xl bg-gradient-to-br from-base-100/95 via-base-100/90 to-base-100/95 backdrop-blur-2xl rounded-3xl w-64"> 
        <li>
          <Button variant="primary" icon="user" size="sm" @click="goToLogin"> 
            Connexion
          </Button>
        </li>
      </ul>
    </div>
  </div>
</template>



<style scoped></style>
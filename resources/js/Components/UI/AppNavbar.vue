<script setup>
import Button from '@/Components/UI/Button.vue'
import { Link } from '@inertiajs/vue3'
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
      <RouterLink to="/" class="flex items-center group">
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
      </RouterLink>

      <div class="flex items-center gap-2">
        <RouterLink to="/collection"
                    class="group relative overflow-hidden px-6 py-3 text-sm font-medium text-base-content/70 hover:text-primary transition-all duration-300 cursor-pointer rounded-2xl hover:bg-gradient-to-r hover:from-primary/5 hover:to-secondary/5 before:absolute before:inset-0 before:bg-gradient-to-r before:from-primary/0 before:via-primary/10 before:to-primary/0 before:-translate-x-full hover:before:translate-x-full before:transition-transform before:duration-700 before:rounded-2xl"
                    active-class="!text-primary !bg-gradient-to-r !from-primary/10 !to-secondary/10">
          <span class="relative z-10 flex items-center gap-3">
            <div
              class="w-5 h-5 rounded-lg bg-primary/15 flex items-center justify-center group-hover:bg-primary/25 group-hover:scale-110 transition-all duration-300">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
              </svg>
            </div>
            Collection
          </span>
        </RouterLink>

        <RouterLink to="/gacha"
                    class="group relative overflow-hidden px-6 py-3 text-sm font-medium text-base-content/70 hover:text-secondary transition-all duration-300 cursor-pointer rounded-2xl hover:bg-gradient-to-r hover:from-secondary/5 hover:to-accent/5 before:absolute before:inset-0 before:bg-gradient-to-r before:from-secondary/0 before:via-secondary/10 before:to-secondary/0 before:-translate-x-full hover:before:translate-x-full before:transition-transform before:duration-700 before:rounded-2xl"
                    active-class="!text-secondary !bg-gradient-to-r !from-secondary/10 !to-accent/10">
          <span class="relative z-10 flex items-center gap-3">
            <div
              class="w-5 h-5 rounded-lg bg-secondary/15 flex items-center justify-center group-hover:bg-secondary/25 group-hover:scale-110 transition-all duration-300">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                </path>
              </svg>
            </div>
            Gacha
          </span>
        </RouterLink>
      </div>

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
        <li class="mb-2">
          <RouterLink to="/"
                      class="group text-base-content/70 hover:text-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer p-4 rounded-2xl">
            <div class="flex items-center gap-4">
              <div
                class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                  </path>
                </svg>
              </div>
              <span class="font-medium">Accueil</span>
            </div>
          </RouterLink>
        </li>
        <li class="mb-2">
          <RouterLink to="/collection"
                      class="group text-base-content/70 hover:text-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer p-4 rounded-2xl">
            <div class="flex items-center gap-4">
              <div
                class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                  </path>
                </svg>
              </div>
              <span class="font-medium">Collection</span>
            </div>
          </RouterLink>
        </li>
        <li class="mb-4">
          <RouterLink to="/gacha"
                      class="group text-base-content/70 hover:text-secondary hover:bg-secondary/5 transition-all duration-300 cursor-pointer p-4 rounded-2xl">
            <div class="flex items-center gap-4">
              <div
                class="w-8 h-8 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7">
                  </path>
                </svg>
              </div>
              <span class="font-medium">Gacha</span>
            </div>
          </RouterLink>
        </li>
        <div class="divider opacity-30"></div>
        <li>
          <Button variant="primary" icon="user" size="sm">
            Connexion
          </Button>
        </li>
      </ul>
    </div>
  </div>
</template>



<style scoped></style>
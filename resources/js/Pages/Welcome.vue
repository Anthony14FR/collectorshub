<template>
  <div data-theme="pokemon">
    <AppNavbar />
    
    <section class="relative h-screen overflow-hidden bg-base-100">
      <div class="absolute inset-0 z-[1]" data-speed="0">
        <img
          src="/images/background.png"
          alt="Background"
          class="w-full h-full object-cover object-center"
        />
      </div>

      <div class="absolute inset-0 z-[10] -mb-20" data-speed="-0.1">
        <img
          src="/images/mountains.png"
          alt="Mountains"
          class="w-full h-full object-cover object-bottom"
        />
      </div>

      <div class="absolute inset-0 z-[5] flex items-center justify-center -mt-96" data-speed="-0.8">
        <h1
          class="text-base-100/80 text-4xl sm:text-5xl md:text-6xl lg:text-8xl xl:text-[10rem] 2xl:text-[12rem] font-black drop-shadow-2xl text-center px-4"
        >
          COLLECTOR'S HUB
        </h1>
      </div>

      <div class="absolute inset-0 z-[20] flex items-end justify-center" data-speed="0.6">
        <img
          src="/images/Foreground1.png"
          alt="Foreground 1"
          class="w-full h-auto object-cover object-bottom"
          style="margin-bottom: -1px;"
        />
      </div>

      <div
        class="absolute inset-0 z-[30] flex flex-col items-center justify-end h-[200vh]"
        data-speed="1.0"
      >
        <img 
          src="/images/Foreground2.png" 
          alt="Foreground 2" 
          class="w-full h-auto object-cover object-bottom" 
          style="margin-bottom: -1px;"
        />
        <div class="w-full h-screen bg-base-100 -mt-1"></div>
      </div>
    </section>

    <section class="min-h-screen flex items-center justify-center relative overflow-hidden pt-20 md:pt-0" data-aos="fade-up" data-aos-duration="1200">
      <div class="absolute inset-0 z-0">
        <img 
          src="/images/section-2-background 1.jpg" 
          alt="Pokémon background" 
          class="w-full h-full object-cover object-center opacity-10"
        />
      </div>

      <div class="absolute inset-0 z-[1]">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-secondary/10 rounded-full blur-2xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 right-1/3 w-32 h-32 bg-accent/10 rounded-full blur-xl animate-pulse delay-500"></div>
        <div class="absolute top-1/3 left-1/2 w-20 h-20 bg-info/5 rounded-full blur-lg animate-bounce"></div>
        <div class="absolute bottom-1/3 left-1/4 w-24 h-24 bg-success/8 rounded-full blur-2xl animate-pulse delay-700"></div>
      </div>

      <div class="absolute inset-0 pointer-events-none overflow-hidden z-[2]">
        <div class="absolute top-16 left-16 w-3 h-3 bg-primary rounded-full animate-pulse opacity-60"></div>
        <div class="absolute top-32 right-20 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-50"></div>
        <div class="absolute bottom-24 left-32 w-4 h-4 bg-accent rounded-full animate-pulse delay-1000 opacity-40"></div>
        <div class="absolute top-48 right-48 w-2.5 h-2.5 bg-info rounded-full animate-pulse delay-300 opacity-55"></div>
        <div class="absolute bottom-32 right-16 w-3 h-3 bg-success rounded-full animate-pulse delay-800 opacity-45"></div>
        
        <div class="absolute top-24 left-48 w-2 h-2 bg-primary/80 rounded-full animate-pulse delay-200 opacity-50"></div>
        <div class="absolute top-64 right-32 w-1.5 h-1.5 bg-secondary/80 rounded-full animate-pulse delay-600 opacity-60"></div>
        <div class="absolute bottom-48 left-24 w-2 h-2 bg-accent/80 rounded-full animate-pulse delay-900 opacity-40"></div>
        <div class="absolute top-80 right-64 w-1.5 h-1.5 bg-info/80 rounded-full animate-pulse delay-400 opacity-50"></div>
        <div class="absolute bottom-16 right-40 w-2 h-2 bg-success/80 rounded-full animate-pulse delay-1200 opacity-45"></div>
        
        <div class="absolute top-40 left-64 w-1 h-1 bg-primary/60 rounded-full animate-pulse delay-100 opacity-70"></div>
        <div class="absolute top-56 right-56 w-1 h-1 bg-secondary/60 rounded-full animate-pulse delay-700 opacity-50"></div>
        <div class="absolute bottom-40 left-56 w-1 h-1 bg-accent/60 rounded-full animate-pulse delay-1100 opacity-60"></div>
        <div class="absolute top-72 right-80 w-1 h-1 bg-info/60 rounded-full animate-pulse delay-350 opacity-40"></div>
        <div class="absolute bottom-56 right-24 w-1 h-1 bg-success/60 rounded-full animate-pulse delay-950 opacity-55"></div>
        
        <div class="absolute top-20 left-80 w-2 h-2 bg-gradient-to-br from-primary to-secondary rounded-full animate-bounce opacity-50"></div>
        <div class="absolute top-60 right-72 w-1.5 h-1.5 bg-gradient-to-br from-secondary to-accent rounded-full animate-bounce delay-500 opacity-40"></div>
        <div class="absolute bottom-20 left-72 w-2 h-2 bg-gradient-to-br from-accent to-info rounded-full animate-bounce delay-1000 opacity-45"></div>
      </div>

      <div class="absolute bottom-0 left-0 right-0 z-10 pointer-events-none hidden md:block">
        <img 
          src="/images/Foreground1_old.png" 
          alt="Foreground decoration" 
          class="w-full h-auto object-cover object-bottom"
        />
      </div>

      <div class="text-center z-10 max-w-5xl mx-auto px-6">
        <Badge 
          variant="success" 
          size="lg" 
          pill 
          class="mb-6 md:mb-8 shadow-2xl shadow-primary/20" 
          data-aos="zoom-in" 
          data-aos-delay="200"
        >
          <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
          <span class="font-semibold text-sm md:text-base">Collector's Hub</span>
          <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
        </Badge>
        
        <div class="relative mb-6 md:mb-8">
          <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-black mb-4 md:mb-6 relative z-10" data-aos="fade-up" data-aos-delay="400">
            <span class="bg-gradient-to-r from-success via-primary to-secondary bg-clip-text text-transparent drop-shadow-2xl animate-pulse">
              REJOIGNEZ
            </span>
          </h2>
          
          <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-4 left-8 w-3 h-3 bg-accent rounded-full blur-sm animate-pulse opacity-40"></div>
            <div class="absolute top-12 right-12 w-2 h-2 bg-info rounded-full blur-sm animate-pulse opacity-50 delay-500"></div>
            <div class="absolute bottom-8 left-16 w-4 h-4 bg-secondary rounded-full blur-md animate-pulse opacity-30 delay-1000"></div>
            <div class="absolute top-8 right-24 w-2.5 h-2.5 bg-primary rounded-full blur-sm animate-pulse opacity-45 delay-700"></div>
          </div>
        </div>
        
        <h3 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-base-content/90 mb-4 md:mb-6" data-aos="fade-up" data-aos-delay="450">
          L'AVENTURE ULTIME
        </h3>
        
        <div class="flex items-center justify-center gap-4 mb-8 md:mb-12" data-aos="fade-up" data-aos-delay="450">
          <div class="h-px bg-gradient-to-r from-transparent via-primary/60 to-primary/20 w-16 md:w-24"></div>
          <div class="w-3 h-3 bg-gradient-to-br from-primary to-secondary rounded-full shadow-lg shadow-primary/50 animate-pulse"></div>
          <div class="h-px bg-gradient-to-l from-transparent via-primary/60 to-primary/20 w-16 md:w-24"></div>
        </div>
        
        <div class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-3xl p-8 mb-12 relative overflow-hidden shadow-2xl shadow-primary/10" data-aos="fade-up" data-aos-delay="500">
          <div class="absolute top-6 right-6 w-6 h-6 border-2 border-success/30 rounded-full animate-pulse"></div>
          <div class="absolute bottom-6 left-6 w-4 h-4 border-2 border-primary/40 rounded-full animate-pulse delay-500"></div>
          <div class="absolute top-8 left-8 w-2 h-2 bg-accent rounded-full blur-sm animate-pulse opacity-60 delay-1000"></div>
          <div class="absolute bottom-8 right-8 w-3 h-3 bg-info rounded-full blur-sm animate-pulse opacity-50 delay-300"></div>
          
          <p class="text-xl md:text-2xl text-base-content/90 leading-relaxed mb-8">
            Des milliers de dresseurs vous attendent ! Commencez votre collection dès maintenant 
            et devenez le maître Pokémon ultime dans cette aventure épique.
          </p>
          
          <div class="flex flex-wrap justify-center gap-6 text-base-content/70 mb-8">
            <div class="flex items-center gap-2 group">
              <span class="text-success text-lg group-hover:scale-110 transition-transform">✓</span>
              <span>Gratuit</span>
            </div>
            <div class="flex items-center gap-2 group">
              <span class="text-success text-lg group-hover:scale-110 transition-transform">✓</span>
              <span>Pas de publicité</span>
            </div>
            <div class="flex items-center gap-2 group">
              <span class="text-success text-lg group-hover:scale-110 transition-transform">✓</span>
              <span>Mises à jour régulières</span>
            </div>
            <div class="flex items-center gap-2 group">
              <span class="text-success text-lg group-hover:scale-110 transition-transform">✓</span>
              <span>Communauté active</span>
            </div>
          </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 justify-center" data-aos="fade-up" data-aos-delay="400">
          <Button variant="primary" icon="lightning" size="md">
            Commencer Maintenant
          </Button>
          <Button variant="secondary" icon="collection" size="md">
            Voir la collection
          </Button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import Lenis from 'lenis'
import AOS from 'aos'
import 'aos/dist/aos.css'
import AppNavbar from '@/Components/UI/AppNavbar.vue'
import Button from '@/Components/UI/Button.vue'
import Badge from '@/Components/UI/Badge.vue'

let lenis = null

onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 100,
  })

  lenis = new Lenis({
    autoRaf: true,
  })

  lenis.on('scroll', (e) => {
    document.querySelectorAll('[data-speed]').forEach((el) => {
      const speed = el.getAttribute('data-speed')
      const yPos = -(e.scroll * speed)
      el.style.transform = `translateY(${yPos}px)`
    })
  })
})

onUnmounted(() => {
  lenis?.destroy()
})
</script>

<style scoped>
</style>

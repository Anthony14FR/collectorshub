<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

interface Props {
    ballType: string;
    quantity: number;
}

const { ballType, quantity } = defineProps<Props>();

const animationPhase = ref(0);
const particles = ref<Array<{id: number, x: number, y: number, delay: number}>>([]);

const animationConfig = computed(() => {
    if (ballType === 'Masterball') {
        return {
            ballEmoji: '🏐',
            primaryColor: 'from-warning to-error',
            secondaryColor: 'from-error to-warning',
            glowColor: 'shadow-warning/50',
            particleColor: 'bg-gradient-to-r from-warning to-error',
            ringColor: 'border-warning',
            lightColor: 'bg-warning/80'
        };
    }

    return {
        ballEmoji: '⚾',
        primaryColor: 'from-primary to-secondary',
        secondaryColor: 'from-secondary to-primary',
        glowColor: 'shadow-primary/50',
        particleColor: 'bg-gradient-to-r from-primary to-secondary',
        ringColor: 'border-primary',
        lightColor: 'bg-primary/80'
    };
});

const generateParticles = () => {
    const newParticles = [];
    for (let i = 0; i < 50; i++) {
        newParticles.push({
            id: i,
            x: Math.random() * 100,
            y: Math.random() * 100,
            delay: Math.random() * 2
        });
    }
    particles.value = newParticles;
};

onMounted(() => {
    generateParticles();

    // Séquence d'animation
    setTimeout(() => animationPhase.value = 1, 100);   // Phase de charge
    setTimeout(() => animationPhase.value = 2, 1000);  // Phase d'invocation
    setTimeout(() => animationPhase.value = 3, 2000);  // Phase finale
});
</script>

<template>
    <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/80 backdrop-blur-sm">
        <!-- Particules d'ambiance -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div
                v-for="particle in particles"
                :key="particle.id"
                :class="[
                    'absolute w-2 h-2 rounded-full opacity-60 animate-ping',
                    animationConfig.particleColor
                ]"
                :style="{
                    left: `${particle.x}%`,
                    top: `${particle.y}%`,
                    animationDelay: `${particle.delay}s`
                }"
            />
        </div>

        <!-- Animation principale -->
        <div class="relative">
            <!-- Cercles d'énergie -->
            <div
                v-for="i in 5"
                :key="i"
                :class="[
                    'absolute inset-0 rounded-full border-4 opacity-30',
                    animationConfig.ringColor,
                    animationPhase >= 1 ? 'animate-ping' : ''
                ]"
                :style="{
                    width: `${100 + i * 50}px`,
                    height: `${100 + i * 50}px`,
                    marginLeft: `${-25 * i}px`,
                    marginTop: `${-25 * i}px`,
                    animationDelay: `${i * 0.2}s`,
                    animationDuration: '1.5s'
                }"
            />

            <!-- Ball centrale -->
            <div
                :class="[
                    'relative z-10 w-32 h-32 rounded-full flex items-center justify-center text-6xl transition-all duration-1000 shadow-2xl',
                    'bg-gradient-to-br',
                    animationConfig.primaryColor,
                    animationPhase >= 2 ? animationConfig.glowColor : '',
                    animationPhase >= 2 ? 'animate-bounce' : '',
                    animationPhase >= 3 ? 'scale-150 opacity-0' : ''
                ]"
            >
                {{ animationConfig.ballEmoji }}
            </div>

            <!-- Rayons d'énergie -->
            <div
                v-if="animationPhase >= 2"
                class="absolute inset-0 pointer-events-none"
            >
                <div
                    v-for="i in 8"
                    :key="i"
                    :class="[
                        'absolute w-1 h-20 rounded-full opacity-80 animate-pulse',
                        animationConfig.lightColor
                    ]"
                    :style="{
                        left: '50%',
                        top: '50%',
                        transformOrigin: 'center',
                        transform: `translate(-50%, -50%) rotate(${i * 45}deg)`,
                        animationDelay: `${i * 0.1}s`
                    }"
                />
            </div>
        </div>

        <!-- Texte d'animation -->
        <div class="absolute bottom-1/4 left-1/2 -translate-x-1/2 text-center">
            <div
                :class="[
                    'text-2xl font-bold mb-4 bg-gradient-to-r bg-clip-text text-transparent transition-all duration-500',
                    animationConfig.primaryColor
                ]"
            >
                <span v-if="animationPhase === 1">⚡ Chargement de l'énergie...</span>
                <span v-else-if="animationPhase === 2">🌟 Invocation en cours...</span>
                <span v-else-if="animationPhase === 3">🎊 Invocation terminée !</span>
                <span v-else>✨ Préparation...</span>
            </div>

            <div class="text-base-content/70 text-sm">
                Invocation {{ quantity > 1 ? `x${quantity}` : 'simple' }} avec {{ ballType }}
            </div>

            <!-- Barre de progression -->
            <div class="w-64 h-2 bg-base-100/30 rounded-full mt-4 overflow-hidden">
                <div
                    :class="[
                        'h-full rounded-full transition-all duration-1000 bg-gradient-to-r',
                        animationConfig.primaryColor
                    ]"
                    :style="{
                        width: `${(animationPhase / 3) * 100}%`
                    }"
                />
            </div>
        </div>

        <!-- Effets d'arrière-plan -->
        <div
            v-if="animationPhase >= 2"
            class="absolute inset-0 pointer-events-none"
        >
            <!-- Vagues d'énergie -->
            <div
                v-for="i in 3"
                :key="i"
                :class="[
                    'absolute inset-0 rounded-full border-2 opacity-20 animate-ping',
                    animationConfig.ringColor
                ]"
                :style="{
                    animationDelay: `${i * 0.3}s`,
                    animationDuration: '2s'
                }"
            />
        </div>
    </div>
</template>

<style scoped>
@keyframes energyPulse {
    0%, 100% {
        transform: scale(1) rotate(0deg);
        opacity: 0.8;
    }
    50% {
        transform: scale(1.2) rotate(180deg);
        opacity: 1;
    }
}

.animate-energy {
    animation: energyPulse 2s ease-in-out infinite;
}
</style>

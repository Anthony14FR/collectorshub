<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

interface Props {
    maxRarity: 'normal' | 'rare' | 'epic' | 'legendary';
    hasShiny: boolean;
    onComplete: () => void;
}

const { maxRarity, hasShiny, onComplete } = defineProps<Props>();

const currentStage = ref(0);
const isEvolving = ref(false);
const evolutionFailed = ref(false);
const showShinyEffect = ref(false);
const shinyIntensity = ref(0);
const isVisible = ref(false);
const evolutionIntensity = ref(0);
const dustParticles = ref<Array<{id: number, x: number, y: number, delay: number, duration: number}>>([]);
const showLoading = ref(false);
const currentMessage = ref('Une énergie mystérieuse se dégage...');
const textVisible = ref(true);
const pokemonVisible = ref(true);

const evolutions = [
    { image: '/images/pokemon-gifs/4.gif' },
    { image: '/images/pokemon-gifs/5.gif' },
    { image: '/images/pokemon-gifs/6.gif' }
];

const currentPokemon = computed(() => evolutions[currentStage.value]);

const shouldEvolveToStage = (stage: number) => {
    if (stage === 1) return ['epic', 'legendary'].includes(maxRarity);
    if (stage === 2) return maxRarity === 'legendary';
    return false;
};

const skipAnimation = () => {
    onComplete();
};

const updateMessage = async (newMessage: string) => {
    textVisible.value = false;
    await new Promise(resolve => setTimeout(resolve, 300));
    currentMessage.value = newMessage;
    await new Promise(resolve => setTimeout(resolve, 100));
    textVisible.value = true;
    await new Promise(resolve => setTimeout(resolve, 200));
};

const evolvePokemon = async () => {
    pokemonVisible.value = false;
    await new Promise(resolve => setTimeout(resolve, 500));
    currentStage.value++;
    await new Promise(resolve => setTimeout(resolve, 100));
    pokemonVisible.value = true;
    await new Promise(resolve => setTimeout(resolve, 500));
};

const addDustParticle = () => {
    const newParticle = {
        id: Date.now() + Math.random(),
        x: 20 + Math.random() * 60,
        y: 20 + Math.random() * 60,
        delay: Math.random() * 2,
        duration: 1 + Math.random()
    };
    dustParticles.value.push(newParticle);
};

const startEvolution = async () => {
    await new Promise(resolve => setTimeout(resolve, 500));
    isVisible.value = true;
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    if (shouldEvolveToStage(1)) {
        await evolveToNextStage();
        await new Promise(resolve => setTimeout(resolve, 1000));
        if (shouldEvolveToStage(2)) {
            await evolveToNextStage();
        } else {
            await failEvolution();
        }
    } else {
        await failEvolution();
    }
    
    if (hasShiny) {
        await new Promise(resolve => setTimeout(resolve, 800));
        await updateMessage('Énergie brillante détectée !');
        
        showShinyEffect.value = true;
        
        const shinyInterval = setInterval(() => {
            if (shinyIntensity.value < 100) {
                shinyIntensity.value += 3;
            } else {
                clearInterval(shinyInterval);
            }
        }, 30);
        
        await new Promise(resolve => setTimeout(resolve, 2200));
    }
    
    await new Promise(resolve => setTimeout(resolve, 1000));
    onComplete();
};

const evolveToNextStage = async () => {
    await updateMessage('Évolution en cours');
    isEvolving.value = true;
    showLoading.value = true;
    
    const intensityInterval = setInterval(() => {
        if (evolutionIntensity.value < 100) {
            evolutionIntensity.value += 2.5;
            if (Math.random() < 0.3) addDustParticle();
        }
    }, 30);
    
    await new Promise(resolve => setTimeout(resolve, 1600));
    
    clearInterval(intensityInterval);
    showLoading.value = false;
    
    await evolvePokemon();
    
    isEvolving.value = false;
    
    const quickFadeInterval = setInterval(() => {
        if (evolutionIntensity.value > 0) {
            evolutionIntensity.value -= 25;
            dustParticles.value = dustParticles.value.slice(3);
        } else {
            clearInterval(quickFadeInterval);
            dustParticles.value = [];
        }
    }, 20);
    
    if (currentStage.value === 1) {
        await updateMessage('Une énergie épique émane de cette créature !');
    } else if (currentStage.value === 2) {
        await updateMessage('Une puissance légendaire se révèle !');
    }
    
    await new Promise(resolve => setTimeout(resolve, 800));
};

const failEvolution = async () => {
    await updateMessage('Évolution en cours');
    isEvolving.value = true;
    showLoading.value = true;
    
    const intensityInterval = setInterval(() => {
        if (evolutionIntensity.value < 75) {
            evolutionIntensity.value += 2;
            if (Math.random() < 0.25) addDustParticle();
        }
    }, 35);
    
    await new Promise(resolve => setTimeout(resolve, 1400));
    
    clearInterval(intensityInterval);
    showLoading.value = false;
    
    evolutionFailed.value = true;
    updateMessage('L\'évolution a échoué...');
    
    const fastFadeInterval = setInterval(() => {
        if (evolutionIntensity.value > 0) {
            evolutionIntensity.value -= 8;
            dustParticles.value = dustParticles.value.slice(2);
        } else {
            clearInterval(fastFadeInterval);
            dustParticles.value = [];
        }
    }, 30);
    
    await new Promise(resolve => setTimeout(resolve, 1200));
    isEvolving.value = false;
    await new Promise(resolve => setTimeout(resolve, 1500));
};

onMounted(() => {
    startEvolution();
});
</script>

<template>
    <div class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-sm flex items-center justify-center">
        <button 
            @click="skipAnimation"
            class="absolute top-6 right-6 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg px-4 py-2 text-white/80 hover:text-white transition-all duration-300 text-sm font-medium z-[10000]"
        >
            Passer →
        </button>

        <div 
            class="relative text-center transition-all duration-1200 ease-out"
            :class="{ 'opacity-0 transform translate-y-8 scale-95': !isVisible, 'opacity-100 transform translate-y-0 scale-100': isVisible }"
        >
            <div class="mb-12 h-16 flex items-center justify-center transition-all duration-1000 ease-out" :style="{ transitionDelay: isVisible ? '400ms' : '0ms' }">
                <h2 
                    class="text-3xl font-bold transition-all duration-400 ease-out"
                    :class="[
                        { 'opacity-0 transform translate-y-4': !textVisible, 'opacity-100 transform translate-y-0': textVisible },
                        currentMessage === 'L\'évolution a échoué...' ? 'text-red-300/90' : 'text-white/90'
                    ]"
                >
                    {{ currentMessage }}
                </h2>
            </div>
            
            <div class="relative mx-auto mb-8">
                <div 
                    v-if="isEvolving"
                    class="absolute -inset-12 rounded-full transition-all duration-700 ease-out"
                    :style="{
                        background: `radial-gradient(circle, rgba(255,255,255,${0.1 + (evolutionIntensity * 0.003)}) 0%, rgba(255,255,255,${0.05 + (evolutionIntensity * 0.001)}) 70%, transparent 100%)`,
                        filter: 'blur(15px)',
                        opacity: evolutionIntensity / 100
                    }"
                />
                
                <div 
                    v-if="isEvolving"
                    class="absolute -inset-6 rounded-full border-2 transition-all duration-700 ease-out"
                    :style="{
                        borderColor: `rgba(255,255,255,${0.2 + (evolutionIntensity * 0.004)})`,
                        filter: 'blur(8px)',
                        opacity: evolutionIntensity / 100
                    }"
                />
                
                <div 
                    v-if="isEvolving"
                    class="absolute -inset-16 pointer-events-none"
                >
                    <div 
                        v-for="particle in dustParticles" 
                        :key="particle.id"
                        class="absolute w-1 h-1 bg-white/60 rounded-full animate-ping"
                        :style="{
                            left: `${particle.x}%`,
                            top: `${particle.y}%`,
                            animationDelay: `${particle.delay}s`,
                            animationDuration: `${particle.duration}s`,
                            opacity: Math.min(evolutionIntensity / 100, 0.8)
                        }"
                    />
                </div>
                
                <div 
                    v-if="showShinyEffect"
                    class="absolute -inset-20 pointer-events-none transition-all duration-1000 ease-out animate-shinyContainerFadeIn"
                    :style="{
                        opacity: shinyIntensity / 100
                    }"
                >
                    <div 
                        v-for="i in 15" 
                        :key="`shiny-${i}`"
                        class="absolute w-2 h-2 bg-yellow-400 rounded-full animate-shinyExplosion"
                        :style="{
                            left: '50%',
                            top: '50%',
                            animationDelay: `${i * 100}ms`,
                            '--angle': `${(360 / 15) * i}deg`,
                            opacity: Math.min(shinyIntensity / 100, 0.9)
                        }"
                    />
                </div>
                
                <div 
                    class="relative flex items-center justify-center transition-all duration-1200 ease-out"
                    :class="{
                        'animate-fastShake': evolutionFailed
                    }"
                    :style="{
                        filter: isEvolving ? `brightness(${1 + (evolutionIntensity * 0.012)}) contrast(${1 - (evolutionIntensity * 0.004)})` : 'brightness(1) contrast(1)',
                        transitionDelay: isVisible ? '700ms' : '0ms'
                    }"
                >
                    <img 
                        :src="currentPokemon.image"
                        alt="Pokemon"
                        class="w-80 h-80 object-contain filter drop-shadow-2xl transition-all duration-500 ease-out"
                        style="image-rendering: pixelated;"
                        :class="[
                            { 'opacity-0 transform scale-90': !pokemonVisible, 'opacity-100 transform scale-100': pokemonVisible }
                        ]"
                        :style="{
                            filter: showShinyEffect ? `drop-shadow(0 0 ${15 + (shinyIntensity * 0.2)}px rgba(255, 215, 0, ${0.2 + (shinyIntensity * 0.003)})) brightness(${1 + (shinyIntensity * 0.002)})` : isEvolving ? `brightness(${1 + (evolutionIntensity * 0.008)}) contrast(${1 - (evolutionIntensity * 0.002)}) saturate(${1 + (evolutionIntensity * 0.005)})` : 'drop-shadow(0 0 20px rgba(0, 0, 0, 0.3)) brightness(1)'
                        }"
                    />
                </div>
            </div>
            
            <div class="min-h-[60px] flex items-center justify-center transition-all duration-800 ease-out" :style="{ transitionDelay: isVisible ? '1000ms' : '0ms' }">
                <div v-if="showLoading" class="text-white/70 text-lg transition-all duration-600 ease-out">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-white/60 rounded-full animate-pulse" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-white/60 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        <div class="w-2 h-2 bg-white/60 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fastShake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-6px); }
    40% { transform: translateX(6px); }
    60% { transform: translateX(-4px); }
    80% { transform: translateX(4px); }
}

@keyframes shinyExplosion {
    0% {
        opacity: 1;
        transform: translate(-50%, -50%) rotate(var(--angle)) translateX(0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translate(-50%, -50%) rotate(var(--angle)) translateX(200px) scale(0);
    }
}

@keyframes shinyContainerFadeIn {
    0% { 
        opacity: 0; 
        transform: scale(0.8); 
    }
    100% { 
        opacity: 1; 
        transform: scale(1); 
    }
}

.animate-fastShake {
    animation: fastShake 0.4s ease-in-out 2;
}

.animate-shinyExplosion {
    animation: shinyExplosion 1.5s ease-out forwards;
}

.animate-shinyContainerFadeIn {
    animation: shinyContainerFadeIn 1s ease-out;
}
</style>
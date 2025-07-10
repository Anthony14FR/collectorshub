<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

interface Props {
    maxRarity: 'normal' | 'rare' | 'epic' | 'legendary';
    hasShiny: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    completed: []
}>();

const currentStage = ref(0);
const isEvolving = ref(false);
const evolutionFailed = ref(false);
const showShinyEffect = ref(false);
const shinyIntensity = ref(0);
const isVisible = ref(false);
const evolutionIntensity = ref(0);
const dustParticles = ref<Array<{id: number, x: number, y: number, delay: number, duration: number}>>([]);
const shinyParticles = ref<Array<{id: number, x: number, y: number, delay: number}>>([]);
const shinyWaves = ref<Array<{id: number, delay: number}>>([]);
const showLoading = ref(false);
const currentMessage = ref('Une énergie mystérieuse se dégage...');
const textVisible = ref(true);
const pokemonVisible = ref(true);
const isCompleted = ref(false);
const containerHeight = ref(400);

const activeIntervals = ref<NodeJS.Timeout[]>([]);

// Différentes lignes d'évolution de starters à 3 évolutions
const evolutionLines = [
    // Salamèche → Reptincel → Dracaufeu
    [
        { image: '/images/pokemon-gifs/4.gif', name: 'Salamèche' },
        { image: '/images/pokemon-gifs/5.gif', name: 'Reptincel' },
        { image: '/images/pokemon-gifs/6.gif', name: 'Dracaufeu' }
    ],
    // Carapuce → Carabaffe → Tortank
    [
        { image: '/images/pokemon-gifs/7.gif', name: 'Carapuce' },
        { image: '/images/pokemon-gifs/8.gif', name: 'Carabaffe' },
        { image: '/images/pokemon-gifs/9.gif', name: 'Tortank' }
    ],
    // Bulbizarre → Herbizarre → Florizarre
    [
        { image: '/images/pokemon-gifs/1.gif', name: 'Bulbizarre' },
        { image: '/images/pokemon-gifs/2.gif', name: 'Herbizarre' },
        { image: '/images/pokemon-gifs/3.gif', name: 'Florizarre' }
    ],
    // Chenipan → Chrysacier → Papilusion
    [
        { image: '/images/pokemon-gifs/10.gif', name: 'Chenipan' },
        { image: '/images/pokemon-gifs/11.gif', name: 'Chrysacier' },
        { image: '/images/pokemon-gifs/12.gif', name: 'Papilusion' }
    ],
    // Aspicot → Coconfort → Dardargnan
    [
        { image: '/images/pokemon-gifs/13.gif', name: 'Aspicot' },
        { image: '/images/pokemon-gifs/14.gif', name: 'Coconfort' },
        { image: '/images/pokemon-gifs/15.gif', name: 'Dardargnan' }
    ]
];

// Choisir aléatoirement une ligne d'évolution
const selectedEvolutionLine = evolutionLines[Math.floor(Math.random() * evolutionLines.length)];
const evolutions = selectedEvolutionLine;

const currentPokemon = computed(() => evolutions[currentStage.value]);

const shouldEvolveToStage = (stage: number) => {
    if (stage === 1) return ['epic', 'legendary'].includes(props.maxRarity);
    if (stage === 2) return props.maxRarity === 'legendary';
    return false;
};

const cleanupAll = () => {
    activeIntervals.value.forEach(interval => clearInterval(interval));
    activeIntervals.value = [];
    currentStage.value = 0;
    isEvolving.value = false;
    evolutionFailed.value = false;
    showShinyEffect.value = false;
    shinyIntensity.value = 0;
    isVisible.value = false;
    evolutionIntensity.value = 0;
    dustParticles.value = [];
    shinyParticles.value = [];
    shinyWaves.value = [];
    showLoading.value = false;
    currentMessage.value = 'Une énergie mystérieuse se dégage...';
    textVisible.value = true;
    pokemonVisible.value = true;
    isCompleted.value = false;
};

const completeAnimation = () => {
    if (isCompleted.value) return;
    isCompleted.value = true;

    // Commencer le fadeOut immédiatement
    isVisible.value = false;

    // Émettre après le début du fadeOut pour que la page parent puisse réagir
    setTimeout(() => {
        emit('completed');

        // Nettoyer après le fadeOut complet
        setTimeout(() => {
            cleanupAll();
        }, 1000);
    }, 500); // Délai pour laisser le fadeOut commencer
};

const skipAnimation = () => {
    completeAnimation();
};

const addInterval = (interval: NodeJS.Timeout) => {
    activeIntervals.value.push(interval);
};

const updateMessage = async (newMessage: string) => {
    containerHeight.value = 420;
    textVisible.value = false;
    await new Promise(resolve => setTimeout(resolve, 300));
    currentMessage.value = newMessage;
    await new Promise(resolve => setTimeout(resolve, 100));
    textVisible.value = true;
    await new Promise(resolve => setTimeout(resolve, 300));
    containerHeight.value = 400;
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

const addShinyParticle = () => {
    // Génère des particules autour du Pokémon dans une zone plus large
    const angle = Math.random() * Math.PI * 2;
    const distance = 20 + Math.random() * 35;
    const newParticle = {
        id: Date.now() + Math.random(),
        x: 50 + Math.cos(angle) * distance,
        y: 50 + Math.sin(angle) * distance,
        delay: Math.random() * 0.8
    };
    shinyParticles.value.push(newParticle);

    setTimeout(() => {
        shinyParticles.value = shinyParticles.value.filter(p => p.id !== newParticle.id);
    }, 2500);
};

const addShinyWave = () => {
    const newWave = {
        id: Date.now() + Math.random(),
        delay: Math.random() * 0.3
    };
    shinyWaves.value.push(newWave);

    setTimeout(() => {
        shinyWaves.value = shinyWaves.value.filter(w => w.id !== newWave.id);
    }, 2000);
};

const startEvolution = async () => {
    await new Promise(resolve => setTimeout(resolve, 500));
    // Rendre le composant visible immédiatement
    isVisible.value = true;
    // Attendre qu'il soit bien affiché avant de démarrer le script
    await new Promise(resolve => setTimeout(resolve, 1000));
    await updateMessage(`${evolutions[0].name} ressent une énergie mystérieuse...`);
    await new Promise(resolve => setTimeout(resolve, 1500));

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

    if (props.hasShiny) {
        await new Promise(resolve => setTimeout(resolve, 800));
        await updateMessage('Énergie brillante détectée !');

        showShinyEffect.value = true;

        const waveInterval = setInterval(() => {
            addShinyWave();
        }, 400);
        addInterval(waveInterval);

        const particleInterval = setInterval(() => {
            for (let i = 0; i < 3; i++) {
                addShinyParticle();
            }
        }, 100);
        addInterval(particleInterval);

        const shinyInterval = setInterval(() => {
            if (shinyIntensity.value < 100) {
                shinyIntensity.value += 3;
            } else {
                clearInterval(shinyInterval);
                clearInterval(particleInterval);
                clearInterval(waveInterval);
                const index = activeIntervals.value.indexOf(shinyInterval);
                if (index > -1) activeIntervals.value.splice(index, 1);
                const particleIndex = activeIntervals.value.indexOf(particleInterval);
                if (particleIndex > -1) activeIntervals.value.splice(particleIndex, 1);
                const waveIndex = activeIntervals.value.indexOf(waveInterval);
                if (waveIndex > -1) activeIntervals.value.splice(waveIndex, 1);
            }
        }, 30);
        addInterval(shinyInterval);

        await new Promise(resolve => setTimeout(resolve, 2200));
    }

    await new Promise(resolve => setTimeout(resolve, 500)); // Réduit de 1000ms à 500ms
    completeAnimation();
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
    addInterval(intensityInterval);

    await new Promise(resolve => setTimeout(resolve, 1600));

    clearInterval(intensityInterval);
    const intensityIndex = activeIntervals.value.indexOf(intensityInterval);
    if (intensityIndex > -1) activeIntervals.value.splice(intensityIndex, 1);

    showLoading.value = false;

    await evolvePokemon();

    const quickFadeInterval = setInterval(() => {
        if (evolutionIntensity.value > 0) {
            evolutionIntensity.value -= 10;
            dustParticles.value = dustParticles.value.slice(3);
        } else {
            clearInterval(quickFadeInterval);
            const fadeIndex = activeIntervals.value.indexOf(quickFadeInterval);
            if (fadeIndex > -1) activeIntervals.value.splice(fadeIndex, 1);
            dustParticles.value = [];
            isEvolving.value = false; // Désactive l'aura après le fondu
        }
    }, 30);
    addInterval(quickFadeInterval);

    if (currentStage.value === 1) {
        await updateMessage(`${evolutions[1].name} ! Une énergie épique émane de cette créature !`);
    } else if (currentStage.value === 2) {
        await updateMessage(`${evolutions[2].name} ! Une puissance légendaire se révèle !`);
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
    addInterval(intensityInterval);

    await new Promise(resolve => setTimeout(resolve, 1400));

    clearInterval(intensityInterval);
    const intensityIndex = activeIntervals.value.indexOf(intensityInterval);
    if (intensityIndex > -1) activeIntervals.value.splice(intensityIndex, 1);

    showLoading.value = false;

    evolutionFailed.value = true;
    updateMessage('L\'évolution a échoué...');

    const fastFadeInterval = setInterval(() => {
        if (evolutionIntensity.value > 0) {
            evolutionIntensity.value -= 8;
            dustParticles.value = dustParticles.value.slice(2);
        } else {
            clearInterval(fastFadeInterval);
            const fadeIndex = activeIntervals.value.indexOf(fastFadeInterval);
            if (fadeIndex > -1) activeIntervals.value.splice(fadeIndex, 1);
            dustParticles.value = [];
            isEvolving.value = false; // Désactive l'aura après le fondu
        }
    }, 30);
    addInterval(fastFadeInterval);

    await new Promise(resolve => setTimeout(resolve, 1200));
};

onMounted(() => {
    startEvolution();
});

onUnmounted(() => {
    cleanupAll();
});
</script>

<template>
    <div
        class="fixed inset-0 z-[9999] flex items-center justify-center transition-all duration-700 ease-out"
        :class="{
            'opacity-0': !isVisible,
            'opacity-100': isVisible
        }"
    >
        <img
            src="/images/invocation_background.png"
            alt="Background"
            class="absolute inset-0 w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/40"></div>

        <button
            @click="skipAnimation"
            class="absolute top-6 right-6 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/40 rounded-lg px-5 py-2.5 text-white hover:text-white transition-all duration-300 text-sm font-medium z-[10000] transform hover:scale-105 hover:shadow-lg"
            style="box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);"
        >
            Passer →
        </button>

        <div
            class="relative transition-all duration-700 ease-out transform"
            :class="{
                'opacity-0 translate-y-12 scale-90': !isVisible,
                'opacity-100 translate-y-0 scale-100': isVisible
            }"
            :style="{ height: `${containerHeight}px` }"
        >
            <div class="flex flex-col items-center justify-center h-full">
                <div class="mb-8 h-16 flex items-center justify-center transition-all duration-1000 ease-out" :style="{ transitionDelay: isVisible ? '400ms' : '0ms' }">
                    <h2
                        class="text-3xl font-bold transition-opacity duration-500 ease-out"
                        :class="[
                            { 'opacity-0': !textVisible, 'opacity-100': textVisible },
                            currentMessage === 'L\'évolution a échoué...' ? 'text-red-300/90' : 'text-white/90'
                        ]"
                        :style="{
                            textShadow: '0 1px 6px rgba(0, 0, 0, 0.6), 0 0 10px rgba(255, 255, 255, 0.05)'
                        }"
                    >
                        {{ currentMessage }}
                    </h2>
                </div>

                <div class="relative">
                    <div
                        v-if="isEvolving"
                        class="absolute -inset-12 rounded-full transition-all duration-700 ease-out animate-pulseGlow"
                        :style="{
                            background: `radial-gradient(circle, rgba(255,255,255,${0.15 + (evolutionIntensity * 0.003)}) 0%, rgba(255,255,255,${0.05 + (evolutionIntensity * 0.001)}) 60%, transparent 100%)`,
                            filter: 'blur(20px)',
                            opacity: evolutionIntensity / 100
                        }"
                    />

                    <div
                        v-if="isEvolving"
                        class="absolute -inset-8 rounded-full transition-all duration-700 ease-out animate-rotateGlow"
                        :style="{
                            background: `conic-gradient(from 0deg, transparent, rgba(255,255,255,${0.1 + (evolutionIntensity * 0.002)}), transparent, rgba(255,255,255,${0.1 + (evolutionIntensity * 0.002)}), transparent)`,
                            filter: 'blur(10px)',
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
                            class="absolute w-1 h-1 bg-white/60 rounded-full animate-floatUp"
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
                        class="relative inline-flex items-center justify-center transition-all duration-1200 ease-out"
                        :class="{
                            'animate-errorShake': evolutionFailed
                        }"
                        :style="{
                            transitionDelay: isVisible ? '700ms' : '0ms'
                        }"
                    >
                        <!-- Aura dorée autour du Pokémon -->
                        <div
                            v-if="showShinyEffect"
                            class="absolute -inset-8 rounded-full pointer-events-none"
                            :style="{
                                background: `radial-gradient(circle, transparent 40%, rgba(255, 215, 0, ${0.3 * (shinyIntensity / 100)}) 70%, rgba(255, 193, 7, ${0.15 * (shinyIntensity / 100)}) 90%, transparent 100%)`,
                                filter: `blur(10px)`,
                                opacity: shinyIntensity / 100
                            }"
                        />

                        <img
                            :src="currentPokemon.image"
                            alt="Pokemon"
                            class="w-60 h-60 object-contain transition-all duration-500 ease-out relative z-10"
                            style="image-rendering: pixelated;"
                            :class="[
                                { 'opacity-0 scale-75': !pokemonVisible, 'opacity-100 scale-100': pokemonVisible }
                            ]"
                            :style="{
                                filter: showShinyEffect
                                    ? `brightness(${1.1 + (shinyIntensity * 0.003)}) contrast(${1.1 + (shinyIntensity * 0.002)}) saturate(${1.2 + (shinyIntensity * 0.003)})`
                                    : isEvolving
                                        ? `brightness(${1.2 + (evolutionIntensity * 0.008)}) contrast(${1.1 + (evolutionIntensity * 0.002)}) saturate(${1.1 + (evolutionIntensity * 0.005)})`
                                        : 'brightness(1) contrast(1) saturate(1)'
                            }"
                        />


                    </div>
                </div>

                <!-- Poussière dorée flottante - SORTIE du conteneur pour éviter l'overflow -->
                <div
                    v-if="showShinyEffect"
                    class="fixed inset-0 pointer-events-none z-[9998]"
                >
                    <div
                        v-for="particle in shinyParticles"
                        :key="`shiny-${particle.id}`"
                        class="absolute w-0.5 h-0.5 bg-yellow-300 rounded-full animate-shinyDust"
                        :style="{
                            left: `calc(50% + ${(particle.x - 50) * 2.4}px)`,
                            top: `calc(50% + ${(particle.y - 50) * 2.4}px)`,
                            animationDelay: `${particle.delay}s`,
                            boxShadow: `0 0 3px rgba(255, 215, 0, 1)`
                        }"
                    />
                </div>

                <div class="h-16 flex items-center justify-center transition-all duration-800 ease-out mt-4" :style="{ transitionDelay: isVisible ? '1000ms' : '0ms' }">
                    <div v-if="showLoading" class="text-white/70 text-lg transition-all duration-600 ease-out">
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 bg-white/70 rounded-full animate-loadingDot" style="animation-delay: 0s"></div>
                            <div class="w-2.5 h-2.5 bg-white/70 rounded-full animate-loadingDot" style="animation-delay: 0.15s"></div>
                            <div class="w-2.5 h-2.5 bg-white/70 rounded-full animate-loadingDot" style="animation-delay: 0.3s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes errorShake {
    0%, 100% { transform: translateX(0) rotate(0deg); }
    10% { transform: translateX(-8px) rotate(-1deg); }
    20% { transform: translateX(8px) rotate(1deg); }
    30% { transform: translateX(-6px) rotate(-1deg); }
    40% { transform: translateX(6px) rotate(0.5deg); }
    50% { transform: translateX(-4px) rotate(-0.5deg); }
    60% { transform: translateX(4px) rotate(0deg); }
}

@keyframes floatUp {
    0% {
        opacity: 0;
        transform: translateY(0) scale(0);
    }
    20% {
        opacity: 1;
        transform: translateY(-20px) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateY(-80px) scale(0.5);
    }
}

@keyframes shinyDust {
    0% {
        opacity: 0;
        transform: translateY(0) scale(0);
    }
    20% {
        opacity: 1;
        transform: translateY(-5px) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateY(-15px) scale(0.3);
    }
}



@keyframes floatSmooth {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes pulseGlow {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes rotateGlow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes loadingDot {
    0%, 80%, 100% {
        transform: scale(0.8) translateY(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1.2) translateY(-10px);
        opacity: 1;
    }
}

.animate-errorShake {
    animation: errorShake 0.6s ease-in-out;
}

.animate-floatUp {
    animation: floatUp 2s ease-out forwards;
}

.animate-shinyDust {
    animation: shinyDust 2s ease-out forwards;
}



.animate-floatSmooth {
    animation: floatSmooth 4s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-pulseGlow {
    animation: pulseGlow 2s ease-in-out infinite;
}

.animate-rotateGlow {
    animation: rotateGlow 4s linear infinite;
}

.animate-loadingDot {
    animation: loadingDot 1.2s ease-in-out infinite;
}
</style>

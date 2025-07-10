<script setup lang="ts">
import { ref, computed } from 'vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';

interface Props {
    ballType: string;
    pokemon: any;
    index: number;
    revealed: boolean;
}

const { ballType, pokemon, index, revealed } = defineProps<Props>();
const emit = defineEmits<{
    reveal: [index: number];
}>();

const pingParticles = Array.from({ length: 16 }, (_, i) => ({
    left: `${Math.random() * 100}%`,
    top: `${Math.random() * 100}%`,
    animationDelay: `${(Math.random() * 2.5) + (i * 0.1)}s`,
    animationDuration: `${1.2 + Math.random() * 1.8}s`,
    transitionInDelay: `${i * 40}ms`,
    transitionOutDelay: `${(16 - i) * 25}ms`
}));

const sparkleParticles = Array.from({ length: 8 }, (_, i) => ({
    left: `${10 + Math.random() * 80}%`,
    top: `${10 + Math.random() * 80}%`,
    animationDelay: `${Math.random() * 2.5}s`,
    animationDuration: `${2 + Math.random() * 2}s`,
    transitionInDelay: `${i * 60}ms`,
    transitionOutDelay: `${(8 - i) * 40}ms`
}));

const revealedShinyParticles = Array.from({ length: 10 }, () => ({
    left: `${Math.random() * 100}%`,
    top: `${Math.random() * 100}%`,
    animationDelay: `${Math.random() * 3.5}s`,
    animationDuration: `${1.8 + Math.random() * 2.2}s`
}));

const isHovered = ref(false);

const ballImage = computed(() => {
    return ballType === 'Masterball' ? '/images/items/masterball.png' : '/images/items/pokeball.png';
});

const rarityConfig = computed(() => {
    const configs = {
        normal: {
            aura: 'shadow-[0_0_25px_rgba(255,255,255,0.7)]',
            border: 'border-white',
            glow: 'drop-shadow(0 0 35px rgba(255,255,255,1))',
            bgGlow: 'bg-gradient-radial from-white',
            particle: 'bg-white'
        },
        rare: {
            aura: 'shadow-[0_0_30px_rgba(59,130,246,0.8)]',
            border: 'border-blue-400',
            glow: 'drop-shadow(0 0 25px rgba(59,130,246,1))',
            bgGlow: 'bg-gradient-radial from-blue-400',
            particle: 'bg-blue-400'
        },
        epic: {
            aura: 'shadow-[0_0_35px_rgba(168,85,247,0.8)]',
            border: 'border-purple-400',
            glow: 'drop-shadow(0 0 30px rgba(168,85,247,1))',
            bgGlow: 'bg-gradient-radial from-purple-400',
            particle: 'bg-purple-400'
        },
        legendary: {
            aura: 'shadow-[0_0_40px_rgba(251,146,60,0.8)]',
            border: 'border-orange-400',
            glow: 'drop-shadow(0 0 35px rgba(251,146,60,1))',
            bgGlow: 'bg-gradient-radial from-orange-400',
            particle: 'bg-orange-400'
        }
    };
    const rarityKey = pokemon.rarity as keyof typeof configs;
    return configs[rarityKey] || configs.normal;
});

const pokemonImageUrl = computed(() => {
    const pokemonId = pokemon.pokemon_id;

    if (pokemon.is_shiny) {
        const shinyId = pokemonId - 1000;
        return `/images/pokemon-gifs/${shinyId}_S.gif`;
    } else {
        return `/images/pokemon-gifs/${pokemonId}.gif`;
    }
});

const handleClick = () => {
    if (!revealed) {
        emit('reveal', index);
    }
};
</script>

<template>
    <div
        class="relative w-32 h-32 mx-auto cursor-pointer transition-all duration-700 ease-in-out hover:scale-110"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        @click="handleClick"
        :style="{ animationDelay: `${index * 100}ms` }"
    >
        <div
            :class="[
                'absolute -inset-2 rounded-full transition-all duration-700 ease-out z-0',
                rarityConfig.aura,
                rarityConfig.bgGlow,
                isHovered && !revealed ? 'opacity-60 scale-110' : 'opacity-0 scale-100'
            ]"
            style="filter: blur(15px);"
        />

        <div
            :class="[
                'absolute -inset-1 rounded-full transition-all duration-700 ease-out z-0',
                rarityConfig.aura,
                isHovered && !revealed ? 'opacity-40 scale-105' : 'opacity-0 scale-100'
            ]"
            style="filter: blur(5px);"
        />

        <div v-if="pokemon.is_shiny" class="absolute inset-0 pointer-events-none z-10">
            <div
                v-for="(particle, i) in pingParticles"
                :key="`ping-${i}`"
                class="absolute w-1.5 h-1.5 bg-gradient-radial from-yellow-300 to-yellow-500 rounded-full animate-customPing"
                :style="{
                    left: particle.left,
                    top: particle.top,
                    animationDelay: particle.animationDelay,
                    animationDuration: particle.animationDuration,
                    boxShadow: '0 0 10px rgba(255, 215, 0, 0.9)',
                    opacity: isHovered && !revealed ? 0.9 : 0,
                    transition: 'opacity 0.5s ease-out',
                    transitionDelay: isHovered ? particle.transitionInDelay : particle.transitionOutDelay
                }"
            />

            <div
                v-for="(particle, i) in sparkleParticles"
                :key="`sparkle-${i}`"
                class="absolute w-1 h-1 bg-gradient-radial from-yellow-300 to-yellow-600 rounded-full animate-sparkle"
                :style="{
                    left: particle.left,
                    top: particle.top,
                    animationDelay: particle.animationDelay,
                    animationDuration: particle.animationDuration,
                    boxShadow: '0 0 15px rgba(255, 215, 0, 1)',
                    opacity: isHovered && !revealed ? 1 : 0,
                    transition: 'opacity 0.4s ease-out',
                    transitionDelay: isHovered ? particle.transitionInDelay : particle.transitionOutDelay
                }"
            />
        </div>

        <div v-if="revealed && pokemon.is_shiny" class="absolute inset-0 pointer-events-none z-10">
            <div
                v-for="(particle, i) in revealedShinyParticles"
                :key="`revealed-${i}`"
                class="absolute w-1.5 h-1.5 bg-gradient-radial from-yellow-300 to-yellow-500 rounded-full animate-customPing"
                :style="{
                    left: particle.left,
                    top: particle.top,
                    animationDelay: particle.animationDelay,
                    animationDuration: particle.animationDuration,
                    boxShadow: '0 0 8px rgba(255, 215, 0, 0.7)'
                }"
            />
        </div>

        <div v-if="revealed" :class="['absolute -inset-5 rounded-full opacity-50 animate-pulseStrong z-0', rarityConfig.aura]" style="filter: blur(25px);" />

        <div
            :class="[
                'relative w-full h-full rounded-full flex items-center justify-center transition-all duration-1000 ease-in-out transform z-20',
                revealed ? 'bg-transparent scale-110' : '',
                !revealed && isHovered ? 'scale-105' : ''
            ]"
            :style="{
                border: 'none',
                outline: 'none',
                transition: 'all 0.7s ease-in-out'
            }"
        >
            <div
                v-if="!revealed"
                :class="[
                    'transition-all duration-800 ease-in-out',
                    isHovered ? 'animate-floatBall' : ''
                ]"
            >
                <img
                    :src="ballImage"
                    :alt="ballType"
                    class="w-20 h-20 object-contain"
                    :style="{
                        filter: isHovered ? `brightness(1.3) contrast(1.2) ${rarityConfig.glow}` : 'brightness(1) contrast(1) drop-shadow(0px 0px 0px transparent)',
                        transition: 'filter 0.6s ease-out'
                    }"
                />
            </div>

            <div
                v-else
                class="text-center animate-fadeInScale z-20"
                :style="{ animationDelay: '0.3s' }"
            >
                <div class="relative mb-2">
                    <img
                        :src="pokemonImageUrl"
                        :alt="pokemon.name"
                        class="w-24 h-24 object-contain mx-auto animate-bounceIn"
                        style="image-rendering: pixelated;"
                    />

                    <div
                        v-if="pokemon.is_shiny"
                        class="absolute top-0 right-0 text-yellow-400 text-xl animate-sparkleStatic drop-shadow-[0_0_8px_rgba(255,215,0,0.8)]"
                    >
                        ✨
                    </div>
                </div>

                <div class="text-xs font-bold animate-slideUp text-white drop-shadow-lg" :style="{ animationDelay: '0.5s' }">
                    {{ pokemon.name }}
                </div>
                <div class="text-xs text-white/80 animate-slideUp drop-shadow-md" :style="{ animationDelay: '0.6s' }">
                    Niv. {{ pokemon.level }}
                </div>

                <RarityBadge
                    :rarity="pokemon.rarity"
                    size="xs"
                    class="mt-1 inline-block animate-slideUp font-bold"
                    :style="{ animationDelay: '0.7s' }"
                />
            </div>
        </div>

        <div
            v-if="!revealed && isHovered"
            class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-xs text-center text-white font-bold whitespace-nowrap transition-opacity duration-500 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]"
        >
            Cliquer pour révéler
        </div>
    </div>
</template>

<style scoped>
@keyframes sparkle {
    0%, 100% { opacity: 0; transform: scale(0.8) rotate(0deg); }
    50% { opacity: 1; transform: scale(1.2) rotate(180deg); }
}

@keyframes customPing {
    0% { opacity: 1; transform: scale(0.8); }
    75%, 100% { opacity: 0; transform: scale(1.6); }
}

@keyframes sparkleStatic {
    0%, 100% { opacity: 0.7; transform: scale(0.9) rotate(0deg); }
    50% { opacity: 1; transform: scale(1.1) rotate(360deg); }
}

@keyframes fadeInScale {
    0% { opacity: 0; transform: scale(0.3); }
    100% { opacity: 1; transform: scale(1); }
}

@keyframes bounceIn {
    0% { opacity: 0; transform: scale(0.2); }
    50% { opacity: 1; transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}

@keyframes slideUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes floatBall {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-8px) rotate(5deg); }
}

@keyframes pulseStrong {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

@keyframes borderPulse {
    0%, 100% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.05); opacity: 1; }
}

@keyframes rotateAura {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-sparkle {
    animation: sparkle 3s ease-in-out infinite;
}

.animate-customPing {
    animation: customPing 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-sparkleStatic {
    animation: sparkleStatic 2s ease-in-out infinite;
}

.animate-fadeInScale {
    animation: fadeInScale 0.7s ease-out forwards;
    opacity: 0;
}

.animate-bounceIn {
    animation: bounceIn 0.8s ease-out forwards;
    opacity: 0;
}

.animate-slideUp {
    animation: slideUp 0.5s ease-out forwards;
    opacity: 0;
}

.animate-floatBall {
    animation: floatBall 3s ease-in-out infinite;
}

.animate-pulseStrong {
    animation: pulseStrong 2s ease-in-out infinite;
}

.animate-borderPulse {
    animation: borderPulse 1.5s ease-in-out infinite;
}

.animate-rotateAura {
    animation: rotateAura 3s linear infinite;
}

.bg-gradient-radial {
    background: radial-gradient(circle, var(--tw-gradient-from), var(--tw-gradient-to));
}

.bg-gradient-conic {
    background: conic-gradient(var(--tw-gradient-stops));
}

.border-3 {
    border-width: 3px;
}
</style>

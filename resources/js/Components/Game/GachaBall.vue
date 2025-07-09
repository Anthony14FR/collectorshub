<script setup lang="ts">
import { ref, computed } from 'vue';

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

const isHovered = ref(false);

const ballImage = computed(() => {
    return ballType === 'Masterball' ? '/images/items/masterball.png' : '/images/items/pokeball.png';
});

const rarityConfig = computed(() => {
    const configs = {
        normal: {
            aura: 'shadow-white/70',
            border: 'border-white/50',
            glow: 'drop-shadow-[0_0_20px_rgba(255,255,255,0.6)]',
        },
        rare: {
            aura: 'shadow-blue-400/80',
            border: 'border-blue-400/60',
            glow: 'drop-shadow-[0_0_20px_rgba(59,130,246,0.8)]',
        },
        epic: {
            aura: 'shadow-purple-400/80',
            border: 'border-purple-400/60',
            glow: 'drop-shadow-[0_0_20px_rgba(168,85,247,0.8)]',
        },
        legendary: {
            aura: 'shadow-orange-400/90',
            border: 'border-orange-400/70',
            glow: 'drop-shadow-[0_0_25px_rgba(251,146,60,0.9)]',
        }
    };
    return configs[pokemon.rarity] || configs.normal;
});

const pokemonImageUrl = computed(() => {
    const pokemonId = pokemon.pokemon_id;
    console.log(`🖼️ Image pour ${pokemon.name} (ID: ${pokemonId}, Shiny: ${pokemon.is_shiny})`);

    if (pokemon.is_shiny) {
        const shinyId = pokemonId - 1000;
        const shinyUrl = `/images/pokemon-gifs/${shinyId}_S.gif`;
        console.log(`✨ URL Shiny: ${shinyUrl}`);
        return shinyUrl;
    } else {
        const normalUrl = `/images/pokemon-gifs/${pokemonId}.gif`;
        console.log(`🔸 URL Normal: ${normalUrl}`);
        return normalUrl;
    }
});

const handleClick = () => {
    if (!revealed) {
        console.log(`🎁 Révélation ${pokemon.name} (${pokemon.rarity}${pokemon.is_shiny ? ' ✨' : ''})`);
        emit('reveal', index);
    }
};

// Debug des auras
const debugAura = () => {
    console.log(`🔮 Aura ${pokemon.name}:`, {
        rarity: pokemon.rarity,
        config: rarityConfig.value,
        isHovered: isHovered.value,
        revealed: revealed
    });
};
</script>

<template>
    <div
        class="relative w-32 h-32 mx-auto cursor-pointer transition-all duration-500 hover:scale-110"
        @mouseenter="() => { isHovered = true; debugAura(); }"
        @mouseleave="isHovered = false"
        @click="handleClick"
        :style="{ animationDelay: `${index * 100}ms` }"
    >
        <!-- TEST: Aura ultra visible pour debug -->
        <div
            v-if="isHovered && !revealed"
            class="absolute -inset-6 rounded-full border-4 border-red-500 opacity-100 scale-150 animate-pulse z-0"
            style="filter: blur(0px); box-shadow: 0 0 50px red;"
        />

        <!-- Aura de rareté au hover - MAINTENANT VISIBLE -->
        <div
            :class="[
                'absolute -inset-4 rounded-full transition-all duration-700 ease-in-out z-0',
                rarityConfig.aura,
                isHovered && !revealed ? 'opacity-100 scale-125 animate-pulse' : 'opacity-0 scale-100'
            ]"
            style="filter: blur(15px);"
        />

        <!-- Aura secondaire pour plus de visibilité -->
        <div
            :class="[
                'absolute -inset-2 rounded-full border-2 transition-all duration-700 ease-in-out z-0',
                rarityConfig.border,
                isHovered && !revealed ? 'opacity-80 scale-110' : 'opacity-0 scale-100'
            ]"
            style="filter: blur(8px);"
        />

        <!-- Aura shiny spéciale au hover -->
        <div
            v-if="pokemon.is_shiny"
            :class="[
                'absolute -inset-3 rounded-full border-2 border-yellow-300/70 transition-all duration-700 ease-in-out z-0',
                isHovered && !revealed ? 'opacity-90 scale-115 animate-pulse' : 'opacity-0 scale-100'
            ]"
            style="filter: blur(10px); box-shadow: 0 0 30px rgba(255, 255, 0, 0.6);"
        />

        <!-- Aura permanente pour les shiny révélés -->
        <div
            v-if="revealed && pokemon.is_shiny"
            class="absolute -inset-2 rounded-full border border-yellow-300/40 animate-pulse opacity-60 z-0"
            style="filter: blur(8px); box-shadow: 0 0 20px rgba(255, 255, 0, 0.4);"
        />

        <!-- Aura de rareté permanente pour les révélés -->
        <div
            v-if="revealed"
            :class="[
                'absolute -inset-3 rounded-full opacity-40 animate-pulse z-0',
                rarityConfig.aura
            ]"
            style="filter: blur(12px);"
        />

        <!-- Particules pour les shiny au hover -->
        <div v-if="pokemon.is_shiny" class="absolute inset-0 pointer-events-none">
            <div
                v-for="i in 12"
                :key="i"
                :class="[
                    'absolute w-1 h-1 bg-white/60 rounded-full animate-ping transition-all duration-1000 ease-out',
                    isHovered && !revealed ? 'opacity-80' : 'opacity-0'
                ]"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${(Math.random() * 2) + (i * 0.1)}s`,
                    animationDuration: `${1.5 + Math.random() * 1.5}s`,
                    transitionDelay: `${i * 50}ms`
                }"
            />

            <!-- Particules supplémentaires qui bougent -->
            <div
                v-for="i in 6"
                :key="`move-${i}`"
                :class="[
                    'absolute w-0.5 h-0.5 bg-yellow-400/70 rounded-full animate-sparkle transition-all duration-800 ease-out',
                    isHovered && !revealed ? 'opacity-90' : 'opacity-0'
                ]"
                :style="{
                    left: `${20 + Math.random() * 60}%`,
                    top: `${20 + Math.random() * 60}%`,
                    animationDelay: `${Math.random() * 2}s`,
                    transitionDelay: `${i * 100}ms`
                }"
            />
        </div>

        <!-- Particules permanentes pour les shiny révélés -->
        <div v-if="revealed && pokemon.is_shiny" class="absolute inset-0 pointer-events-none">
            <div
                v-for="i in 8"
                :key="`revealed-${i}`"
                class="absolute w-1 h-1 bg-yellow-400/30 rounded-full animate-ping"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 3}s`,
                    animationDuration: `${2 + Math.random() * 2}s`
                }"
            />
        </div>

        <!-- Ball ou Pokémon révélé -->
        <div
            :class="[
                'relative w-full h-full rounded-full flex items-center justify-center transition-all duration-700 ease-out transform z-10',
                revealed ? 'bg-transparent scale-110' : '',
                !revealed ? 'bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm' : '',
                !revealed && isHovered ? 'scale-105' : ''
            ]"
            :style="{
                border: 'none',
                outline: 'none',
                filter: !revealed && isHovered ? rarityConfig.glow : 'none'
            }"
        >
            <!-- Ball image -->
            <div
                v-if="!revealed"
                :class="[
                    'transition-all duration-500 ease-out',
                    isHovered ? 'animate-float' : ''
                ]"
            >
                <img
                    :src="ballImage"
                    :alt="ballType"
                    class="w-20 h-20 object-contain"
                />
            </div>

            <!-- Pokémon révélé avec animation -->
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

                    <!-- Effet shiny SANS rotation -->
                    <div
                        v-if="pokemon.is_shiny"
                        class="absolute top-0 right-0 text-yellow-400 text-lg animate-sparkleStatic"
                    >
                        ✨
                    </div>
                </div>

                <div class="text-xs font-bold animate-slideUp" :style="{ animationDelay: '0.5s' }">
                    {{ pokemon.name }}
                </div>
                <div class="text-xs text-base-content/70 animate-slideUp" :style="{ animationDelay: '0.6s' }">
                    Niv. {{ pokemon.level }}
                </div>

                <!-- Badge de rareté avec animation -->
                <div
                    :class="[
                        'text-xs px-2 py-1 rounded-full mt-1 inline-block animate-slideUp',
                        pokemon.rarity === 'legendary' ? 'bg-orange-400/20 text-orange-400' :
                        pokemon.rarity === 'epic' ? 'bg-purple-400/20 text-purple-400' :
                        pokemon.rarity === 'rare' ? 'bg-blue-400/20 text-blue-400' :
                        'bg-white/20 text-white'
                    ]"
                    :style="{ animationDelay: '0.7s' }"
                >
                    {{ pokemon.rarity }}
                </div>
            </div>
        </div>

        <!-- Instruction au hover -->
        <div
            v-if="!revealed && isHovered"
            class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs text-center text-base-content/70 whitespace-nowrap"
        >
            Cliquer pour révéler
        </div>
    </div>
</template>

<style scoped>
@keyframes sparkle {
    0%, 100% { opacity: 0.3; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.2); }
}

@keyframes sparkleStatic {
    0%, 100% { opacity: 0.4; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.1); }
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

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
}

@keyframes glowPulse {
    0%, 100% { box-shadow: 0 0 15px currentColor; }
    50% { box-shadow: 0 0 30px currentColor, 0 0 45px currentColor; }
}

.animate-sparkle {
    animation: sparkle 3s ease-in-out infinite;
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

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-glow {
    animation: glowPulse 2s ease-in-out infinite;
}

/* Hover effects */
.hover-glow:hover {
    filter: drop-shadow(0 0 20px currentColor);
}

/* Transition pour les révélations */
.reveal-transition {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Transitions fluides pour les auras */
.aura-transition {
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
}

/* Particules fluides */
.particles-transition {
    transition: opacity 0.3s ease-in-out;
}
</style>

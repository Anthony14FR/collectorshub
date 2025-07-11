<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';

interface Props {
    pokemon: any;
    delay: number;
}

const { pokemon, delay } = defineProps<Props>();

const isRevealed = ref(false);
const showSpecialEffects = ref(false);

const cardConfig = computed(() => {
    const isShiny = pokemon.is_shiny;
    const rarity = pokemon.rarity;

    if (isShiny) {
        return {
            borderGradient: 'from-yellow-400 via-yellow-500 to-yellow-600',
            bgGradient: 'from-yellow-50/10 via-yellow-100/10 to-yellow-50/10',
            shadowColor: 'shadow-yellow-500/50',
            glowColor: 'shadow-yellow-400/60',
            textColor: 'text-yellow-400',
            particleColor: 'bg-yellow-400',
            specialClass: 'animate-pulse',
            effectEmoji: '✨'
        };
    }

    switch (rarity) {
        case 'legendary':
            return {
                borderGradient: 'from-warning via-error to-warning',
                bgGradient: 'from-warning/10 via-error/10 to-warning/10',
                shadowColor: 'shadow-warning/50',
                glowColor: 'shadow-warning/80',
                textColor: 'text-warning',
                particleColor: 'bg-warning',
                specialClass: 'animate-pulse',
                effectEmoji: '👑'
            };
        case 'epic':
            return {
                borderGradient: 'from-secondary via-accent to-secondary',
                bgGradient: 'from-secondary/10 via-accent/10 to-secondary/10',
                shadowColor: 'shadow-secondary/40',
                glowColor: 'shadow-secondary/60',
                textColor: 'text-secondary',
                particleColor: 'bg-secondary',
                specialClass: '',
                effectEmoji: '💎'
            };
        case 'rare':
            return {
                borderGradient: 'from-info via-primary to-info',
                bgGradient: 'from-info/10 via-primary/10 to-info/10',
                shadowColor: 'shadow-info/30',
                glowColor: 'shadow-info/50',
                textColor: 'text-info',
                particleColor: 'bg-info',
                specialClass: '',
                effectEmoji: '⭐'
            };
        default:
            return {
                borderGradient: 'from-base-300 via-base-400 to-base-300',
                bgGradient: 'from-base-100/10 via-base-200/10 to-base-100/10',
                shadowColor: 'shadow-base-300/20',
                glowColor: 'shadow-base-300/40',
                textColor: 'text-base-content',
                particleColor: 'bg-base-300',
                specialClass: '',
                effectEmoji: ''
            };
    }
});

const pokemonImageUrl = computed(() => {
    const baseUrl = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/';
    const suffix = pokemon.is_shiny ? 'shiny/' : '';
    return `${baseUrl}${suffix}${pokemon.pokedex_id}.png`;
});

const isSpecial = computed(() => {
    return pokemon.is_shiny || pokemon.rarity === 'legendary' || pokemon.rarity === 'epic';
});

onMounted(() => {
    setTimeout(() => {
        isRevealed.value = true;
        if (isSpecial.value) {
            showSpecialEffects.value = true;
        }
    }, delay);
});
</script>

<template>
    <div
        :class="[
            'relative group transition-all duration-1000 transform',
            isRevealed ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'
        ]"
    >
        <div
            v-if="showSpecialEffects && isSpecial"
            class="absolute inset-0 pointer-events-none z-10"
        >
            <div
                v-for="i in 8"
                :key="i"
                :class="[
                    'absolute w-0.5 h-0.5 rounded-full opacity-60 animate-pulse',
                    cardConfig.particleColor
                ]"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 2}s`,
                    animationDuration: `${2 + Math.random()}s`
                }"
            />
        </div>

        <div
            v-if="pokemon.rarity === 'legendary'"
            class="absolute -inset-2 rounded-xl opacity-30"
            :class="'bg-gradient-to-r ' + cardConfig.borderGradient"
            style="filter: blur(4px);"
        />

        <div
            :class="[
                'relative bg-gradient-to-br backdrop-blur-sm rounded-xl border-2 p-4 transition-all duration-300 hover:scale-105',
                'bg-gradient-to-br ' + cardConfig.bgGradient,
                'border-gradient-to-r ' + cardConfig.borderGradient,
                isSpecial ? 'shadow-xl ' + cardConfig.shadowColor : 'shadow-lg shadow-base-300/20'
            ]"
        >
            <div class="absolute top-2 right-2 text-2xl z-10">
                {{ cardConfig.effectEmoji }}
            </div>

            <div class="relative mb-3">
                <div
                    :class="[
                        'w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-2 transition-all duration-300',
                        'bg-gradient-to-br ' + cardConfig.bgGradient,
                        'border-2 border-gradient-to-r ' + cardConfig.borderGradient,
                        isSpecial ? 'shadow-md ' + cardConfig.shadowColor : 'shadow-sm shadow-base-300/20'
                    ]"
                >
                    <img
                        :src="pokemonImageUrl"
                        :alt="pokemon.name"
                        class="w-14 h-14 object-contain transition-transform duration-300 group-hover:scale-110"
                        @error="$event.target.style.display = 'none'"
                    />
                </div>

                <div
                    v-if="pokemon.is_shiny"
                    class="absolute inset-0 rounded-full opacity-20"
                    style="background: radial-gradient(circle, rgba(255,215,0,0.3) 0%, transparent 70%);"
                />
            </div>

            <h3
                :class="[
                    'text-lg font-bold text-center mb-1 transition-colors duration-300',
                    cardConfig.textColor
                ]"
            >
                {{ pokemon.name }}
                <span v-if="pokemon.is_shiny" class="text-yellow-400 ml-1">✨</span>
            </h3>

            <div class="text-center mb-2">
                <div class="text-xs text-base-content/70">
                    #{{ pokemon.pokedex_id }} • Niveau {{ pokemon.level || 1 }}
                </div>
            </div>

            <div class="flex justify-center mb-2">
                <RarityBadge :rarity="pokemon.rarity" size="xs" />
            </div>

            <div class="flex flex-wrap gap-1 justify-center mb-3">
                <PokemonTypeBadge
                    v-for="type in pokemon.types"
                    :key="type.name"
                    :type="type"
                    size="xs"
                />
            </div>

            <div class="grid grid-cols-2 gap-1 text-xs">
                <div class="bg-base-100/30 rounded-lg p-1.5 text-center">
                    <div class="font-semibold text-error">{{ pokemon.hp }}</div>
                    <div class="text-base-content/70">HP</div>
                </div>
                <div class="bg-base-100/30 rounded-lg p-1.5 text-center">
                    <div class="font-semibold text-warning">{{ pokemon.attack }}</div>
                    <div class="text-base-content/70">ATK</div>
                </div>
                <div class="bg-base-100/30 rounded-lg p-1.5 text-center">
                    <div class="font-semibold text-info">{{ pokemon.defense }}</div>
                    <div class="text-base-content/70">DEF</div>
                </div>
                <div class="bg-base-100/30 rounded-lg p-1.5 text-center">
                    <div class="font-semibold text-success">{{ pokemon.speed }}</div>
                    <div class="text-base-content/70">SPD</div>
                </div>
            </div>

            <div
                v-if="isSpecial"
                class="mt-3 text-center"
            >
                <div
                    :class="[
                        'text-xs font-semibold px-2 py-1 rounded-full',
                        pokemon.is_shiny ? 'bg-yellow-400/20 text-yellow-400' :
                        pokemon.rarity === 'legendary' ? 'bg-warning/20 text-warning' :
                        'bg-secondary/20 text-secondary'
                    ]"
                >
                    {{ pokemon.is_shiny ? '✨ Shiny !' :
                       pokemon.rarity === 'legendary' ? '👑 Légendaire !' :
                       pokemon.rarity === 'epic' ? '💎 Épique !' : '' }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: 200px 0;
    }
}

.animate-shimmer {
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    background-size: 200px 100%;
    animation: shimmer 2s infinite;
}
</style>

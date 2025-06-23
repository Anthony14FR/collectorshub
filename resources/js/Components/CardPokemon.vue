<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Pokemon {
    id: number;
    pokedex_id: number;
    name: string;
    types: any;
    rarity: string;
    is_shiny?: boolean;
    hp?: number;
    attack?: number;
    defense?: number;
    generation?: number;
    level?: number;
    nickname?: string;
}

interface Props {
    pokemon: Pokemon;
}

const props = defineProps<Props>();

const getRarityColor = (rarity: string) => {
    const colors: { [key: string]: string } = {
        common: 'from-slate-400 to-slate-600',
        uncommon: 'from-emerald-400 to-emerald-600',
        rare: 'from-sky-400 to-sky-600',
        epic: 'from-violet-400 to-violet-600',
        legendary: 'from-amber-400 to-amber-600'
    };
    return colors[rarity] || colors.common;
};

const getTypeColor = (type: string) => {
    const colors: { [key: string]: string } = {
        fire: 'from-red-500 to-orange-500',
        water: 'from-blue-500 to-cyan-500',
        grass: 'from-green-500 to-lime-500',
        electric: 'from-yellow-400 to-yellow-600',
        psychic: 'from-pink-500 to-purple-500',
        ice: 'from-cyan-300 to-blue-400',
        dragon: 'from-purple-600 to-indigo-600',
        dark: 'from-gray-700 to-gray-900',
        fairy: 'from-pink-300 to-pink-500',
        fighting: 'from-red-600 to-red-800',
        poison: 'from-purple-500 to-purple-700',
        ground: 'from-yellow-600 to-amber-600',
        flying: 'from-indigo-400 to-blue-500',
        bug: 'from-green-400 to-green-600',
        rock: 'from-yellow-700 to-stone-600',
        ghost: 'from-purple-700 to-indigo-800',
        steel: 'from-gray-400 to-gray-600',
        normal: 'from-gray-400 to-gray-500'
    };
    return colors[type?.toLowerCase()] || colors.normal;
};

const pokemonTypes = computed(() => {
    if (Array.isArray(props.pokemon.types) && props.pokemon.types.every(t => typeof t === 'object' && t.name)) {
        return props.pokemon.types.map(t => ({ ...t, isObject: true }));
    }
    return props.pokemon.types.map((t: string) => ({ name: t, isObject: false }));
});

</script>

<template>
    <div class="pokemon-card">
        <div class="card-header">
            <div class="pokemon-id">#{{ String(pokemon.pokedex_id).padStart(3, '0') }}</div>
            <div v-if="pokemon.is_shiny" class="shiny-indicator">✨</div>
        </div>

        <div class="pokemon-image-placeholder">
            <Link :href="`/pokemon/${pokemon.id}`">
            <img :src="`/images/pokemon/${pokemon.id}.png`" :alt="`Image de ${pokemon.name}`" class="pokemon-image">
            </Link>
        </div>

        <div class="pokemon-info">
            <h3 class="pokemon-name">{{ pokemon.nickname || pokemon.name }}</h3>
            <p v-if="pokemon.level" class="pokemon-level">Niv. {{ pokemon.level }}</p>

            <div class="pokemon-types">
                <template v-for="type in pokemonTypes" :key="type.name">
                    <img v-if="type.isObject" :src="type.image" :alt="type.name" :title="type.name"
                        class="type-image" />
                    <span v-else class="type-chip bg-gradient-to-r" :class="getTypeColor(type.name)">
                        {{ type.name }}
                    </span>
                </template>
            </div>

            <div v-if="pokemon.hp && pokemon.attack && pokemon.defense" class="pokemon-stats">
                <div class="stat-row">
                    <span class="stat-label">HP:</span>
                    <span class="stat-value">{{ pokemon.hp }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">ATK:</span>
                    <span class="stat-value">{{ pokemon.attack }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">DEF:</span>
                    <span class="stat-value">{{ pokemon.defense }}</span>
                </div>
            </div>

            <div class="pokemon-rarity">
                <span class="rarity-chip bg-gradient-to-r" :class="getRarityColor(pokemon.rarity)">
                    {{ pokemon.rarity }}
                </span>
            </div>

            <div v-if="pokemon.generation" class="pokemon-generation">
                Génération {{ pokemon.generation }}
            </div>
        </div>

        <div class="card-actions">
            <Link :href="`/pokemon/${pokemon.id}`" class="view-details-btn">
            Voir détails
            </Link>
        </div>
    </div>
</template>

<style scoped>
.pokemon-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 3px solid #333;
    display: flex;
    flex-direction: column;
}

.pokemon-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: rgba(0, 0, 0, 0.05);
}

.pokemon-id {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #666;
    font-size: 14px;
}

.shiny-indicator {
    font-size: 20px;
    animation: pulse 2s infinite;
}

.pokemon-image-placeholder {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    position: relative;
    padding: 10px;
}

.pokemon-image {
    max-height: 100%;
    width: auto;
    max-width: 100%;
    image-rendering: pixelated;
    image-rendering: -moz-crisp-edges;
    image-rendering: crisp-edges;
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
    transition: transform 0.3s ease;
}

.pokemon-image:hover {
    transform: scale(1.1);
}

.pokemon-info {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
}

.pokemon-name {
    font-family: 'Courier New', monospace;
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin: 0 0 10px 0;
    text-align: center;
}

.pokemon-level {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
    font-weight: bold;
    text-align: center;
}

.pokemon-types {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.type-image {
    height: 28px;
    width: auto;
    filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
    transition: transform 0.2s ease;
}

.type-image:hover {
    transform: scale(1.15);
}

.type-chip {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
}

.pokemon-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
}

.stat-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 12px;
}

.stat-label {
    font-weight: bold;
    color: #666;
    font-size: 10px;
    text-transform: uppercase;
}

.stat-value {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

.pokemon-rarity {
    text-align: center;
    margin-bottom: 10px;
}

.rarity-chip {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    border: 2px solid rgba(0, 0, 0, 0.2);
}

.pokemon-generation {
    text-align: center;
    font-size: 12px;
    color: #666;
    margin-bottom: 15px;
}

.card-actions {
    padding: 15px;
    background: rgba(0, 0, 0, 0.05);
    margin-top: auto;
}

.view-details-btn {
    display: block;
    width: 100%;
    background: linear-gradient(145deg, #00b894, #00a085);
    border: 3px solid #00965a;
    border-radius: 8px;
    padding: 12px;
    color: white;
    text-decoration: none;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    text-align: center;
    transition: all 0.2s ease;
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3);
}

.view-details-btn:hover {
    transform: translateY(-2px);
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.7;
        transform: scale(1);
    }

    50% {
        opacity: 1;
        transform: scale(1.15);
    }
}
</style>
<script setup lang="ts">
import type { PokemonCard } from '@/types/pokemon';
import { getRarityColor, getTypeColor } from '@/utils/pokemon';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    pokemon: PokemonCard;
}

const props = defineProps<Props>();

const pokemonTypes = computed(() => {
    return props.pokemon.types;
});

</script>

<template>
    <div class="pokemon-card">
        <div class="card-header">
            <div class="pokemon-id">#{{ String(pokemon.pokedex_id).padStart(3, '0') }}</div>
            <div v-if="pokemon.is_shiny" class="shiny-indicator">✨</div>
        </div>

        <div class="pokemon-info">
            <h3 class="pokemon-name">{{ pokemon.nickname || pokemon.name }}</h3>
            <p v-if="pokemon.level" class="pokemon-level">Niv. {{ pokemon.level }}</p>

            <div class="pokemon-image">
                <img :src="`/images/pokemon-gifs/${pokemon.is_shiny ? (pokemon.id - 1000) + '_S' : pokemon.id}.gif`" />
            </div>

            <div class="pokemon-types">
                <template v-for="type in pokemonTypes" :key="type.name">
                    <span class="type-chip bg-gradient-to-r" :class="getTypeColor(type.name)">
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

.pokemon-image {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;

    img {
        width: 30%;
        height: auto;
        object-fit: contain;
    }
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
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
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
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    border: 2px solid rgba(0, 0, 0, 0.2);
}

.pokemon-generation {
    font-size: 10px;
    color: #666;
    font-weight: bold;
    text-align: center;
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
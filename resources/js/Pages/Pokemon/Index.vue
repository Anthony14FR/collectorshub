<script setup lang="ts">
import CardPokemon from '@/Components/CardPokemon.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Pokemon {
    id: number;
    pokedex_id: number;
    name: string;
    types: Array<{ name: string; image: string }>;
    resistances: Array<{ name: string; damage_multiplier: number; damage_relation: string }>;
    evolution_id?: number;
    pre_evolution_id?: number;
    description: string;
    height: number;
    weight: number;
    rarity: string;
    is_shiny: boolean;
    hp: number;
    attack: number;
    defense: number;
    speed: number;
    special_attack: number;
    special_defense: number;
    generation: number;
}

interface Props {
    auth: {
        user: {
            id: number;
            username: string;
            level: number;
            cash: number;
        };
    };
    pokemons: Pokemon[];
}

const { auth, pokemons } = defineProps<Props>();

// États des filtres
const selectedType = ref('');
const selectedRarity = ref('');
const selectedGeneration = ref('');
const searchQuery = ref('');

// Types uniques
const uniqueTypes = computed(() => {
    const types = new Set<string>();
    pokemons.forEach(pokemon => {
        pokemon.types.forEach(type => types.add(type.name));
    });
    return Array.from(types).sort();
});

// Générations uniques
const uniqueGenerations = computed(() => {
    const generations = new Set<number>();
    pokemons.forEach(pokemon => generations.add(pokemon.generation));
    return Array.from(generations).sort((a, b) => a - b);
});

// Pokémons filtrés
const filteredPokemons = computed(() => {
    return pokemons.filter(pokemon => {
        const matchesSearch = searchQuery.value === '' ||
            pokemon.name.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesType = selectedType.value === '' ||
            pokemon.types.some(type => type.name === selectedType.value);

        const matchesRarity = selectedRarity.value === '' ||
            pokemon.rarity === selectedRarity.value;

        const matchesGeneration = selectedGeneration.value === '' ||
            pokemon.generation.toString() === selectedGeneration.value;

        return matchesSearch && matchesType && matchesRarity && matchesGeneration;
    });
});

const clearFilters = () => {
    selectedType.value = '';
    selectedRarity.value = '';
    selectedGeneration.value = '';
    searchQuery.value = '';
};
</script>

<template>

    <Head title="Pokédex - Tous les Pokémons" />

    <div class="pokemon-discovery-world">
        <!-- Arrière-plan immersif -->
        <div class="world-background">
            <div class="sky-gradient"></div>
            <div class="clouds cloud-1"></div>
            <div class="clouds cloud-2"></div>
            <div class="clouds cloud-3"></div>
            <div class="mountains"></div>
            <div class="grass-field"></div>
            <div class="floating-particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
        </div>

        <!-- Interface principale -->
        <div class="ui-overlay">
            <!-- Header -->
            <div class="header-zone">
                <div class="header-content">
                    <Link href="/me" class="back-button">
                    ← Retour
                    </Link>

                    <div class="title-section">
                        <h1 class="main-title">Pokédex National</h1>
                        <p class="subtitle">{{ filteredPokemons.length }} Pokémons disponibles</p>
                    </div>

                    <div class="user-info">
                        <span class="username">{{ auth.user.username }}</span>
                        <span class="level">Niv. {{ auth.user.level }}</span>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filters-zone">
                <div class="filters-panel">
                    <div class="search-section">
                        <input v-model="searchQuery" type="text" placeholder="Rechercher un Pokémon..."
                            class="search-input" />
                    </div>

                    <div class="filter-grid">
                        <select v-model="selectedType" class="filter-select">
                            <option value="">Tous les types</option>
                            <option v-for="type in uniqueTypes" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>

                        <select v-model="selectedRarity" class="filter-select">
                            <option value="">Toutes les raretés</option>
                            <option value="common">Commun</option>
                            <option value="uncommon">Peu commun</option>
                            <option value="rare">Rare</option>
                            <option value="epic">Épique</option>
                            <option value="legendary">Légendaire</option>
                        </select>

                        <select v-model="selectedGeneration" class="filter-select">
                            <option value="">Toutes les générations</option>
                            <option v-for="gen in uniqueGenerations" :key="gen" :value="gen.toString()">
                                Génération {{ gen }}
                            </option>
                        </select>

                        <button @click="clearFilters" class="clear-filters-btn">
                            🗑️ Effacer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grille des Pokémons -->
            <div class="pokemons-zone">
                <div class="pokemons-grid">
                    <CardPokemon v-for="pokemon in filteredPokemons" :key="pokemon.id" :pokemon="pokemon" />
                </div>

                <!-- Message si aucun résultat -->
                <div v-if="filteredPokemons.length === 0" class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>Aucun Pokémon trouvé</h3>
                    <p>Essayez de modifier vos filtres de recherche.</p>
                    <button @click="clearFilters" class="reset-search-btn">
                        Réinitialiser la recherche
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pokemon-discovery-world {
    width: 100vw;
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
}

.world-background {
    position: fixed;
    inset: 0;
    z-index: 1;
}

.sky-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom,
            #87CEEB 0%,
            #98D8E8 30%,
            #B8E6B8 60%,
            #90EE90 100%);
}

.clouds {
    position: absolute;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50px;
    opacity: 0.7;
}

.cloud-1 {
    width: 100px;
    height: 40px;
    top: 10%;
    left: 20%;
    animation: float 20s infinite linear;
}

.cloud-2 {
    width: 80px;
    height: 30px;
    top: 15%;
    right: 30%;
    animation: float 25s infinite linear reverse;
}

.cloud-3 {
    width: 120px;
    height: 50px;
    top: 8%;
    left: 60%;
    animation: float 30s infinite linear;
}

.mountains {
    position: absolute;
    bottom: 30%;
    left: 0;
    right: 0;
    height: 200px;
    background: linear-gradient(to top, #8B7355, #A0522D);
    clip-path: polygon(0 100%, 20% 60%, 40% 80%, 60% 40%, 80% 70%, 100% 50%, 100% 100%);
}

.grass-field {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 30%;
    background: linear-gradient(to top, #228B22, #32CD32, #90EE90);
}

.floating-particles {
    position: absolute;
    inset: 0;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: sparkle 3s infinite ease-in-out;
}

.particle:nth-child(1) {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.particle:nth-child(2) {
    top: 40%;
    right: 20%;
    animation-delay: 1s;
}

.particle:nth-child(3) {
    top: 60%;
    left: 70%;
    animation-delay: 2s;
}

.particle:nth-child(4) {
    top: 30%;
    left: 50%;
    animation-delay: 0.5s;
}

.particle:nth-child(5) {
    top: 70%;
    right: 40%;
    animation-delay: 1.5s;
}

.ui-overlay {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    padding: 20px;
}

.header-zone {
    margin-bottom: 30px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.back-button {
    background: linear-gradient(145deg, #4a90e2, #357abd);
    border: 4px solid #2c5aa0;
    border-radius: 8px;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    transition: all 0.2s ease;
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        4px 4px 0px rgba(0, 0, 0, 0.2);
}

.back-button:hover {
    transform: translate(2px, 2px);
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        2px 2px 0px rgba(0, 0, 0, 0.2);
}

.title-section {
    text-align: center;
}

.main-title {
    font-family: 'Courier New', monospace;
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin: 0;
    text-shadow: 2px 2px 0px rgba(0, 0, 0, 0.1);
}

.subtitle {
    color: #666;
    margin: 5px 0 0 0;
    font-size: 16px;
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    color: #333;
}

.username {
    font-weight: bold;
    font-size: 18px;
}

.level {
    font-size: 14px;
    color: #666;
}

.filters-zone {
    margin-bottom: 30px;
}

.filters-panel {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.search-section {
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 15px;
    border: 3px solid #333;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 16px;
    background: white;
    box-shadow:
        inset 2px 2px 0px #ddd,
        inset -2px -2px 0px #fff;
}

.search-input:focus {
    outline: none;
    border-color: #4a90e2;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: center;
}

.filter-select {
    padding: 12px;
    border: 3px solid #333;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    background: white;
    box-shadow:
        inset 2px 2px 0px #ddd,
        inset -2px -2px 0px #fff;
}

.clear-filters-btn {
    background: linear-gradient(145deg, #ff6b6b, #ee5a52);
    border: 3px solid #d63031;
    border-radius: 8px;
    padding: 12px 20px;
    color: white;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        4px 4px 0px rgba(0, 0, 0, 0.2);
}

.clear-filters-btn:hover {
    transform: translate(2px, 2px);
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        2px 2px 0px rgba(0, 0, 0, 0.2);
}

.pokemons-zone {
    flex: 1;
}

.pokemons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.no-results-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.no-results h3 {
    font-family: 'Courier New', monospace;
    color: #333;
    margin-bottom: 10px;
}

.no-results p {
    color: #666;
    margin-bottom: 30px;
}

.reset-search-btn {
    background: linear-gradient(145deg, #4a90e2, #357abd);
    border: 3px solid #2c5aa0;
    border-radius: 8px;
    padding: 15px 30px;
    color: white;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        4px 4px 0px rgba(0, 0, 0, 0.2);
}

.reset-search-btn:hover {
    transform: translate(2px, 2px);
    box-shadow:
        inset 2px 2px 0px rgba(255, 255, 255, 0.3),
        inset -2px -2px 0px rgba(0, 0, 0, 0.3),
        2px 2px 0px rgba(0, 0, 0, 0.2);
}

/* Animations */
@keyframes float {
    0% {
        transform: translateX(-100px);
    }

    100% {
        transform: translateX(calc(100vw + 100px));
    }
}

@keyframes sparkle {

    0%,
    100% {
        opacity: 0;
        transform: scale(0);
    }

    50% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 20px;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .pokemons-grid {
        grid-template-columns: 1fr;
    }
}
</style>
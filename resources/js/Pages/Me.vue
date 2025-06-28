<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import PixelModal from '@/Components/PixelModal.vue';
import CardPokemon from '@/Components/CardPokemon.vue';
import type { User } from '@/types/user';

interface Item {
    id: number;
    name: string;
    description: string;
    type: string;
    effect: any;
    price: number;
    rarity: string;
}

interface Inventory {
    id: number;
    user_id: number;
    item_id: number;
    quantity: number;
    item: Item;
}

interface Pokemon {
    id: number;
    name: string;
    types: string[];
    rarity: string;
}

interface PokedexEntry {
    id: number;
    user_id: number;
    pokemon_id: number;
    name: string;
    nickname?: string;
    level: number;
    star: number;
    is_shiny: boolean;
    is_in_team: boolean;
    is_favorite: boolean;
    pokemon: Pokemon;
}

interface MarketplaceEntry {
    id: number;
    seller_id: number;
    pokemon_id: number;
    price: number;
    status: string;
    pokemon: Pokemon;
}

interface Props {
    auth: {
        user: User;
    };
    inventory?: Inventory[];
    pokedex?: PokedexEntry[];
    marketplace?: MarketplaceEntry[];
}

const { auth, inventory = [], pokedex = [], marketplace = [] } = defineProps<Props>();

const inventoryOpen = ref(false);
const menuOpen = ref(false);
const pokedexModalOpen = ref(false);

const logoutForm = reactive({
    processing: false,
});

const logout = () => {
    logoutForm.processing = true;

    router.post('/logout', {}, {
        onFinish: () => {
            logoutForm.processing = false;
        },
    });
};

const experienceToNextLevel = computed(() => {
    const currentLevel = auth.user.level;
    const expForNextLevel = currentLevel * 1000;
    return expForNextLevel;
});

const experienceProgress = computed(() => {
    const currentExp = auth.user.experience;
    const expForCurrentLevel = (auth.user.level - 1) * 1000;
    const expForNextLevel = experienceToNextLevel.value;
    const progress = ((currentExp - expForCurrentLevel) / (expForNextLevel - expForCurrentLevel)) * 100;
    return Math.min(Math.max(progress, 0), 100);
});

const favoritePokemon = computed(() => pokedex.filter((p: PokedexEntry) => p.is_favorite));

const activeSales = computed(() => marketplace.filter((m: MarketplaceEntry) => m.status === 'active'));

const getRarityColor = (rarity: string) => {
    const colors = {
        common: 'from-slate-400 to-slate-600',
        uncommon: 'from-emerald-400 to-emerald-600',
        rare: 'from-sky-400 to-sky-600',
        epic: 'from-violet-400 to-violet-600',
        legendary: 'from-amber-400 to-amber-600'
    };
    return colors[rarity as keyof typeof colors] || colors.common;
};

const getTypeColor = (type: string) => {
    const colors = {
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
    return colors[type?.toLowerCase() as keyof typeof colors] || colors.normal;
};
</script>

<template>

    <Head title="Tableau de bord" />

    <div class="pokemon-world">
        <!-- Arrière-plan pixel art -->
        <div class="world-background">

            <!-- Nuages pixelisés -->
            <div class="pixel-clouds">
                <div class="pixel-cloud cloud-1"></div>
                <div class="pixel-cloud cloud-2"></div>
            </div>

            <!-- Sol/terre -->
            <div class="pixel-ground"></div>
        </div>

        <!-- Interface utilisateur intégrée -->
        <div class="ui-overlay">
            <!-- Zone supérieure -->
            <div class="top-zone">
                <!-- Inventaire -->
                <div class="left-ui">
                    <div @click="inventoryOpen = !inventoryOpen" class="inventory-button">
                        <img src="/images/inventory.png" alt="Inventaire" class="inventory-icon" />
                    </div>

                    <!-- Menu inventaire -->
                    <div v-if="inventoryOpen" class="inventory-panel">
                        <div class="panel-header">
                            <h3>🎒 Mon Sac</h3>
                            <button @click="inventoryOpen = false" class="close-btn">×</button>
                        </div>
                        <div class="panel-content">
                            <div class="cash-display">
                                <span class="cash-icon">💰</span>
                                <span class="cash-amount">{{ auth.user.cash.toLocaleString() }}</span>
                            </div>
                            <div class="items-grid">
                                <div v-for="item in inventory.slice(0, 8)" :key="item.id" class="item-slot">
                                    <div class="item-icon">📦</div>
                                    <div class="item-info">
                                        <div class="item-name">{{ item.item.name }}</div>
                                        <div class="item-quantity">×{{ item.quantity }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Niveau -->
                <div class="center-ui">
                    <div class="exp-info">
                        <div class="pixel-level-display">
                            <span class="pixel-level-text">LEVEL {{ String(auth.user.level).padStart(2, '0') }}</span>
                        </div>
                        <div class="pixel-progress-container">
                            <div class="pixel-progress-bar">
                                <div class="pixel-progress-fill" :style="{ width: experienceProgress + '%' }"></div>
                            </div>
                        </div>
                        <div class="exp-text">{{ auth.user.experience.toLocaleString() }} / {{
                            experienceToNextLevel.toLocaleString() }}</div>
                    </div>
                </div>

                <!-- Menu utilisateur -->
                <div class="right-ui">
                    <div @click="menuOpen = !menuOpen" class="pixel-menu-btn">
                        <div class="pixel-btn-content">
                            <span class="menu-text">MENU</span>
                        </div>
                    </div>

                    <!-- Menu déroulant simple -->
                    <div v-if="menuOpen" class="user-menu">
                        <Link href="/profile" class="menu-link">⚙️ Profil</Link>
                        <Link href="/pokemon" class="menu-link">📚 Pokédex</Link>
                        <Link v-if="auth.user.role === 'admin'" href="/admin/users" class="menu-link">👤 Utilisateurs
                        </Link>
                        <button @click="logout" :disabled="logoutForm.processing" class="menu-link logout-btn">
                            <span v-if="logoutForm.processing">🔄 Déconnexion...</span>
                            <span v-else">🚪 Déconnexion</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dresseur au centre -->
            <div class="trainer-center">
                <div class="trainer-platform">
                    <div class="platform-shadow"></div>
                    <img src="/images/dresseur.png" alt="Dresseur" class="trainer-sprite">
                    <div class="trainer-aura"></div>
                </div>
            </div>

            <!-- Zone inférieure -->
            <div class="bottom-zone">
                <!-- Marketplace -->
                <div class="bottom-left">
                    <div class="market-stand">
                        <div class="stand-header">
                            <h3>Marketplace</h3>
                            <Link href="/marketplace" class="view-all">Ouvrir
                            </Link>
                        </div>
                        <div class="market-items">
                            <div v-for="sale in activeSales.slice(0, 3)" :key="sale.id" class="market-item">
                                <div class="pokemon-info">
                                    <div class="pokemon-name">{{ sale.pokemon.name }}</div>
                                    <div class="pokemon-types">
                                        <span v-for="type in sale.pokemon.types" :key="type"
                                            class="type-chip bg-gradient-to-r" :class="getTypeColor(type)">
                                            {{ type }}
                                        </span>
                                    </div>
                                </div>
                                <div class="price-info">
                                    <div class="price">{{ sale.price.toLocaleString() }}💰</div>
                                    <div class="rarity bg-gradient-to-r" :class="getRarityColor(sale.pokemon.rarity)">
                                        {{ sale.pokemon.rarity }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="market-footer">{{ activeSales.length }} offres disponibles</div>
                    </div>
                </div>

                <!-- Pokédex -->
                <div class="bottom-right">
                    <div class="pokedex-book">
                        <div class="book-header">
                            <h3>Mon Pokédex</h3>
                            <button @click="pokedexModalOpen = true" class="view-all">Ouvrir</button>
                        </div>
                        <div class="pokemon-entries">
                            <div v-for="pokemon in pokedex.slice(0, 3)" :key="pokemon.id" class="pokemon-entry">
                                <div class="entry-info">
                                    <div class="pokemon-name-entry">
                                        {{ pokemon.nickname || pokemon.name }}
                                        <span v-if="pokemon.is_shiny" class="shiny-star">✨</span>
                                        <span v-if="pokemon.is_favorite" class="favorite-heart">❤️</span>
                                        <span v-if="pokemon.is_in_team" class="team-star">⭐</span>
                                    </div>
                                    <div class="pokemon-details">Niv. {{ pokemon.level }} • {{ pokemon.star }}⭐</div>
                                </div>
                                <div class="entry-rarity bg-gradient-to-r"
                                    :class="getRarityColor(pokemon.pokemon.rarity)">
                                    {{ pokemon.pokemon.rarity }}
                                </div>
                            </div>
                        </div>
                        <div class="book-stats">
                            <div class="book-stat">
                                <span class="stat-label">Favoris</span>
                                <span class="stat-number">{{ favoritePokemon.length }}</span>
                            </div>
                            <div class="book-stat">
                                <span class="stat-label">Total</span>
                                <span class="stat-number">{{ pokedex.length }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <PixelModal :show="pokedexModalOpen" @close="pokedexModalOpen = false">
        <template #header>
            <h3 class="modal-title">Mon Pokédex</h3>
        </template>
        <template #default>
            <div v-if="pokedex.length > 0" class="pokedex-grid">
                <CardPokemon v-for="entry in pokedex" :key="entry.id"
                    :pokemon="{ ...entry.pokemon, pokedex_id: entry.pokemon.id, level: entry.level, is_shiny: entry.is_shiny, nickname: entry.nickname }" />
            </div>
            <div v-else class="no-results-message">
                <p>Vous n'avez aucun Pokémon pour le moment.</p>
                <p>Partez à l'aventure pour en attraper !</p>
            </div>
        </template>
    </PixelModal>
</template>

<style scoped>
.pokemon-world {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    position: relative;
    /* dimensions originales du tile */
    --ground-w: 24px;
    --ground-h: 48px;

    /* multiplicateur (1 = taille originale, 2 = x2, 3 = x3…)  */
    --ground-scale: 6;
}

.world-background {
    position: absolute;
    inset: 0;
    z-index: 1;
    image-rendering: pixelated;
    image-rendering: -moz-crisp-edges;
    image-rendering: crisp-edges;
    background-color: #5F9BFF;
}

.pixel-clouds {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 40%;
}

.pixel-cloud {
    position: absolute;
    background: #D5D5F2;
    image-rendering: pixelated;
}

.pixel-cloud::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #F2F2F2;
    clip-path: polygon(8px 16px, 8px 8px, 16px 8px, 16px 0, 88px 0, 88px 8px, 96px 8px, 96px 16px);
}

.cloud-1 {
    top: 15%;
    left: 15%;
    width: 104px;
    height: 24px;
    clip-path: polygon(8px 24px, 8px 16px, 16px 16px, 16px 8px, 24px 8px, 24px 0, 80px 0, 80px 8px, 88px 8px, 88px 16px, 96px 16px, 96px 24px);
    animation: float-cloud 25s infinite linear;
}

.cloud-1::before {
    clip-path: polygon(16px 16px, 16px 8px, 24px 8px, 24px 0, 80px 0, 80px 8px, 88px 8px, 88px 16px);
}

.cloud-2 {
    top: 10%;
    right: 20%;
    width: 80px;
    height: 24px;
    clip-path: polygon(8px 24px, 8px 16px, 16px 16px, 16px 8px, 24px 8px, 24px 0, 56px 0, 56px 8px, 64px 8px, 64px 16px, 72px 16px, 72px 24px);
    animation: float-cloud 30s infinite linear reverse;
}

.cloud-2::before {
    clip-path: polygon(16px 16px, 16px 8px, 24px 8px, 24px 0, 56px 0, 56px 8px, 64px 8px, 64px 16px);
}

.cloud-3 {
    display: none;
}

.pixel-ground {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: calc(var(--ground-h) * var(--ground-scale));
    background-image: url('/images/tiles/test.png');
    background-repeat: repeat-x;
    background-position: bottom left;
    background-size:
        calc(var(--ground-w) * var(--ground-scale)) calc(var(--ground-h) * var(--ground-scale));
    image-rendering: pixelated;
}

.ui-overlay {
    position: relative;
    z-index: 10;
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.top-zone {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    height: 200px;
}

.left-ui,
.right-ui,
.center-ui,
.bottom-left,
.bottom-right {
    padding: 20px;
    position: relative;
}

.left-ui {
    width: 280px;
    display: flex;
}

.right-ui {
    width: 280px;
    display: flex;
    justify-content: flex-end;
}

.center-ui {
    width: 320px;
}

.inventory-button {
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    padding: 5px;
}

.inventory-button:hover {
    transform: translate(2px, 2px);
}

.inventory-button:active {
    transform: translate(3px, 3px);
}

.inventory-icon {
    width: 64px;
    height: auto;
    image-rendering: pixelated;
    transition: all 0.2s ease;
}

.inventory-button:hover .inventory-icon {
    filter: drop-shadow(-2px -2px 0px rgba(0, 0, 0, 0.2));
}

.inventory-button:active .inventory-icon {
    filter: none;
}

.inventory-panel {
    position: absolute;
    top: 100%;
    left: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    min-width: 150px;
}

.panel-header {
    background: transparent;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px solid #404040;
}

.panel-header h3 {
    margin: 0;
    color: #202020;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    font-size: 18px;
    letter-spacing: 1px;
    text-shadow: 1px 1px 0 #fff;
    text-transform: uppercase;
}

.close-btn {
    border: none;
    background: none;
    font-size: 18px;
    color: #333;
    cursor: pointer;
}

.panel-content {
    padding: 15px;
}

.cash-display {
    margin-bottom: 10px;
}

.cash-icon {
    margin-right: 5px;
}

.cash-amount {
    font-weight: bold;
    color: #333;
}

.items-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.item-slot {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.item-icon {
    width: 48px;
    height: auto;
    image-rendering: pixelated;
}

.item-info {
    text-align: center;
}

.item-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.item-quantity {
    font-size: 12px;
    color: #666;
}

.user-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    min-width: 150px;
    z-index: 1000;
}

.menu-link {
    display: block;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.3s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
}

.menu-link:hover {
    background: rgba(0, 0, 0, 0.1);
}

.logout-btn {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    color: #dc3545;
}

.trainer-center {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
}

.trainer-platform {
    position: relative;
    margin-bottom: 20px;
}

.platform-shadow {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 20px;
    background: radial-gradient(ellipse, rgba(0, 0, 0, 0.3) 0%, transparent 70%);
    border-radius: 50%;
}

.trainer-sprite {
    height: 150px;
    width: auto;
    image-rendering: pixelated;
    filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
    position: relative;
    z-index: 2;
}

.trainer-aura {
    position: absolute;
    inset: -20px;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 3s ease-in-out infinite;
}

.bottom-zone {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    height: 200px;
}

.bottom-left,
.bottom-right {
    width: 350px;
}

.market-stand,
.pokedex-book {
    background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
    border: 4px solid #404040;
    border-radius: 0;
    box-shadow:
        inset 2px 2px 0px white,
        inset -2px -2px 0px #b0b0b0,
        5px 5px 0px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    position: relative;
}

.stand-header,
.book-header {
    background: transparent;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px solid #404040;
}

.stand-header h3,
.book-header h3 {
    margin: 0;
    color: #202020;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    font-size: 18px;
    letter-spacing: 1px;
    text-shadow: 1px 1px 0 #fff;
    text-transform: uppercase;
}

.view-all {
    background: linear-gradient(145deg, #4a90e2, #357abd);
    border: 3px solid #2c5aa0;
    padding: 5px 12px;
    color: white;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    border-radius: 0;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3),
        2px 2px 0px rgba(0, 0, 0, 0.2);
    text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.view-all:hover {
    text-decoration: none;
    transform: translate(1px, 1px);
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3),
        1px 1px 0px rgba(0, 0, 0, 0.2);
}

.view-all:active {
    transform: translate(2px, 2px);
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3);
}

.market-items,
.pokemon-entries {
    padding: 15px;
    max-height: 200px;
    overflow-y: auto;
}

.market-item,
.pokemon-entry {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    margin-bottom: 10px;
    background: #ffffff;
    border-radius: 0;
    border: 2px solid #c0c0c0;
}

.pokemon-info,
.entry-info {
    flex: 1;
    margin-left: 5px;
}

.pokemon-name,
.pokemon-name-entry {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.pokemon-types {
    display: flex;
    gap: 5px;
}

.type-chip {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
}

.price-info,
.entry-rarity {
    text-align: right;
}

.price {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.rarity,
.entry-rarity {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
}

.pokemon-details {
    font-size: 12px;
    color: #666;
}

.shiny-star {
    color: #FFD700;
    margin-left: 5px;
}

.favorite-heart {
    color: #FF69B4;
    margin-left: 5px;
}

.team-star {
    color: #1E90FF;
    margin-left: 5px;
}

.market-footer,
.book-stats {
    padding: 10px 15px;
    background: transparent;
    border-top: 4px solid #404040;
    text-align: center;
    font-size: 12px;
    color: #202020;
}

.book-stats {
    display: flex;
    justify-content: space-around;
}

.book-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-label {
    font-size: 10px;
    color: #999;
}

.stat-number {
    font-weight: bold;
    color: #333;
}

/* Animations */
@keyframes float-cloud {
    0% {
        transform: translateX(-100px);
    }

    100% {
        transform: translateX(calc(100vw + 100px));
    }
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.5;
        transform: scale(1);
    }

    50% {
        opacity: 1;
        transform: scale(1.1);
    }
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 3px;
}

.pixel-menu-btn {
    background: linear-gradient(145deg, #ff1b1b, #d90000);
    border: 3px solid #a70000;
    border-radius: 0;
    padding: 10px 18px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3),
        3px 3px 0px rgba(0, 0, 0, 0.2);
    position: relative;
}

.pixel-menu-btn:hover {
    transform: translate(1px, 1px);
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3),
        2px 2px 0px rgba(0, 0, 0, 0.2);
}

.pixel-menu-btn:active {
    transform: translate(2px, 2px);
    box-shadow:
        inset 1px 1px 0px rgba(255, 255, 255, 0.3),
        inset -1px -1px 0px rgba(0, 0, 0, 0.3);
}

.pixel-btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-text {
    color: white;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 14px;
    text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
    letter-spacing: 1px;
}

.pixel-level-display {
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.exp-text {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: bold;
    color: white;
    text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
    text-align: center;
}

.pixel-level-text {
    font-family: 'Courier New', monospace;
    font-size: 16px;
    font-weight: bold;
    color: white;
    text-shadow:
        2px 0px 0px black,
        -2px 0px 0px black,
        0px 2px 0px black,
        0px -2px 0px black,
        2px 2px 0px black,
        -2px -2px 0px black,
        2px -2px 0px black,
        -2px 2px 0px black;
    letter-spacing: 1px;
}

.pixel-progress-container {
    margin-bottom: 10px;
}

.pixel-progress-bar {
    width: 200px;
    height: 20px;
    background: #404040;
    border: 3px solid black;
    position: relative;
    margin: 0 auto;
    box-shadow:
        inset 2px 2px 0px #606060,
        inset -2px -2px 0px #202020;
}

.pixel-progress-fill {
    height: 100%;
    background: #00ff00;
    transition: width 0.8s ease;
    position: relative;
    box-shadow:
        inset 1px 1px 0px #40ff40,
        inset -1px -1px 0px #00cc00;
}

.pixel-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 2px;
    height: 100%;
    background: #00cc00;
}

.modal-title {
    margin: 0;
    color: #202020;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    font-size: 18px;
    letter-spacing: 1px;
    text-shadow: 1px 1px 0 #fff;
    text-transform: uppercase;
}

.pokedex-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
}

.no-results-message {
    text-align: center;
    padding: 40px;
    color: #333;
    font-family: 'Courier New', monospace;
}

.no-results-message p {
    margin: 5px 0;
}
</style>

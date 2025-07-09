<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import GachaBall from '@/Components/Game/GachaBall.vue';
import PokemonCard from '@/Components/Cards/PokemonCard.vue';
import EvolutionAnimation from '@/Components/Game/EvolutionAnimation.vue';

import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';

interface Props extends PageProps {
    auth: {
        user: User;
    };
    inventory: Inventory[];
}

const { auth, inventory } = defineProps<Props>();
const page = usePage();

const isInvoking = ref(false);
const showEvolutionAnimation = ref(false);
const showGachaResults = ref(false);
const showFinalResults = ref(false);
const invocationResults = ref<any[]>([]);
const revealedBalls = ref<boolean[]>([]);
const currentBallType = ref('');

const getInventoryQuantity = (itemName: string) => {
    const item = inventory.find(inv => inv.item?.name === itemName);
    return item ? item.quantity : 0;
};

const pokeballQuantity = getInventoryQuantity('Pokeball');
const masterballQuantity = getInventoryQuantity('Masterball');

const transformedResults = computed(() => {
    return invocationResults.value.map(pokemon => ({
        id: pokemon.id,
        user_id: auth.user.id,
        pokemon_id: pokemon.pokemon_id,
        level: pokemon.level,
        star: pokemon.star,
        nickname: null,
        is_favorite: false,
        is_in_team: false,
        obtained_at: new Date().toISOString(),
        pokemon: {
            id: pokemon.pokemon_id,
            name: pokemon.name,
            is_shiny: pokemon.is_shiny,
            rarity: pokemon.rarity,
            types: pokemon.types,
            hp: pokemon.hp,
            attack: pokemon.attack,
            defense: pokemon.defense,
            speed: pokemon.speed,
            special_attack: pokemon.special_attack,
            special_defense: pokemon.special_defense
        }
    }));
});

// Nouvelles fonctions pour l'analyse des résultats
const getMaxRarity = () => {
    if (!invocationResults.value.length) return 'normal';
    
    const rarityOrder = { 'normal': 0, 'rare': 1, 'epic': 2, 'legendary': 3 };
    
    return invocationResults.value.reduce((maxRarity, pokemon) => {
        const currentRarityValue = rarityOrder[pokemon.rarity] || 0;
        const maxRarityValue = rarityOrder[maxRarity] || 0;
        
        return currentRarityValue > maxRarityValue ? pokemon.rarity : maxRarity;
    }, 'normal');
};

const hasShinyPokemon = () => {
    return invocationResults.value.some(pokemon => pokemon.is_shiny);
};

const canInvoke = (ballType: string, quantity: number) => {
    const available = ballType === 'Pokeball' ? pokeballQuantity : masterballQuantity;
    return available >= quantity && !isInvoking.value;
};

const startInvocation = async (ballType: string, quantity: number) => {
    if (!canInvoke(ballType, quantity)) return;

    isInvoking.value = true;
    currentBallType.value = ballType;

    try {
        const response = await fetch('/api/opening', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token as string || '',
            },
            body: JSON.stringify({
                ball_type: ballType,
                quantity: quantity
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            invocationResults.value = data.pokemons;
            revealedBalls.value = new Array(data.pokemons.length).fill(false);
            
            // Délai pour le loading initial
            setTimeout(() => {
                isInvoking.value = false;
                
                // Lancer l'animation d'évolution seulement si on a au moins 5 Pokémons (pour x10)
                if (quantity >= 5) {
                    showEvolutionAnimation.value = true;
                } else {
                    // Pour les invocations simples, aller directement aux résultats
                    showGachaResults.value = true;
                }
            }, 2000);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'invocation');
        }
    } catch (error) {
        isInvoking.value = false;
        alert('Erreur lors de l\'invocation. Veuillez réessayer.');
    }
};

const onEvolutionComplete = () => {
    showEvolutionAnimation.value = false;
    showGachaResults.value = true;
};

const revealBall = (index: number) => {
    revealedBalls.value[index] = true;
};

const revealAllBalls = () => {
    revealedBalls.value = revealedBalls.value.map(() => true);
};

const allBallsRevealed = computed(() => {
    return revealedBalls.value.every(revealed => revealed);
});

const showAllResults = () => {
    showGachaResults.value = false;
    showFinalResults.value = true;
};

const closeResults = () => {
    showGachaResults.value = false;
    showFinalResults.value = false;
    showEvolutionAnimation.value = false;
    invocationResults.value = [];
    revealedBalls.value = [];
    router.reload();
};

const goBack = () => {
    router.visit('/me');
};

const getBallEmoji = (ballType: string) => {
    return ballType === 'Masterball' ? '🏐' : '⚾';
};
</script>

<template>
    <Head title="Invocation" />

    <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
        <BackgroundEffects />

        <div class="relative z-10 h-screen w-screen overflow-hidden">
            <div class="flex justify-center pt-4 mb-4">
                <div class="text-center">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-1 tracking-wider">
                        ⚡ INVOCATION
                    </h1>
                    <p class="text-xs text-base-content/70 uppercase tracking-wider">
                        Choisissez votre type de ball et la quantité
                    </p>
                </div>
            </div>

            <div class="absolute left-8 top-20">
                <Button
                    @click="goBack"
                    variant="ghost"
                    size="sm"
                    class="!bg-base-100/60 !backdrop-blur-sm"
                >
                    ← Retour
                </Button>
            </div>

            <div class="flex flex-row justify-center items-start gap-8 mt-24">
                <!-- Pokeball -->
                <div class="w-64">
                    <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                        <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6" />
                                POKEBALL
                            </h3>
                        </div>
                        <div class="p-3 text-center">
                            <div class="text-2xl font-bold text-info mb-1">{{ pokeballQuantity }}</div>
                            <div class="text-xs text-base-content/70">disponible{{ pokeballQuantity > 1 ? 's' : '' }}</div>
                        </div>
                        <div class="p-3 bg-base-100/40">
                            <h4 class="text-xs font-bold tracking-wider mb-2 text-center">CHANCES</h4>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span>Normal</span>
                                    <span class="font-bold">70%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-info">Rare</span>
                                    <span class="font-bold">27%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-secondary">Epic</span>
                                    <span class="font-bold">2.7%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-warning">Légendaire</span>
                                    <span class="font-bold">0.3%</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 space-y-2">
                            <Button
                                @click="startInvocation('Pokeball', 1)"
                                :disabled="!canInvoke('Pokeball', 1)"
                                variant="secondary"
                                size="sm"
                                class="w-full flex items-center justify-center gap-2"
                            >
                                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6 inline-block" />
                                Invoquer x1
                            </Button>
                            <Button
                                @click="startInvocation('Pokeball', 10)"
                                :disabled="!canInvoke('Pokeball', 10)"
                                variant="primary"
                                size="sm"
                                class="w-full flex items-center justify-center gap-2"
                            >
                                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-6 h-6 inline-block" />
                                Invoquer x10
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Bloc central décoratif -->
                <div class="flex-1 max-w-md">
                    <div class="bg-base-100/70 backdrop-blur-md rounded-2xl border border-base-300/30 shadow-lg flex flex-col items-center justify-center py-12 px-6">
                        <div class="text-7xl mb-4 animate-pulse">🎰</div>
                        <h2 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-2">Machine à Invocation</h2>
                        <p class="text-base-content/70 text-center mb-2">
                            Tente ta chance et découvre quels Pokémon tu vas obtenir !<br>
                            Plus tu ouvres de balls, plus tu as de chances d'obtenir des Pokémon rares ou shiny.
                        </p>
                        <div class="mt-4 flex flex-col items-center gap-2">
                            <span class="text-xs text-base-content/50">Bonne chance, Dresseur !</span>
                        </div>
                    </div>
                </div>

                <!-- Masterball -->
                <div class="w-64">
                    <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                        <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6" />
                                MASTERBALL
                            </h3>
                        </div>
                        <div class="p-3 text-center">
                            <div class="text-2xl font-bold text-warning mb-1">{{ masterballQuantity }}</div>
                            <div class="text-xs text-base-content/70">disponible{{ masterballQuantity > 1 ? 's' : '' }}</div>
                        </div>
                        <div class="p-3 bg-base-100/40">
                            <h4 class="text-xs font-bold tracking-wider mb-2 text-center">CHANCES</h4>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span>Normal</span>
                                    <span class="font-bold">34%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-info">Rare</span>
                                    <span class="font-bold">60%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-secondary">Epic</span>
                                    <span class="font-bold">5%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-warning">Légendaire</span>
                                    <span class="font-bold">1%</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 space-y-2">
                            <Button
                                @click="startInvocation('Masterball', 1)"
                                :disabled="!canInvoke('Masterball', 1)"
                                variant="secondary"
                                size="sm"
                                class="w-full flex items-center justify-center gap-2"
                            >
                                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6 inline-block" />
                                Invoquer x1
                            </Button>
                            <Button
                                @click="startInvocation('Masterball', 10)"
                                :disabled="!canInvoke('Masterball', 10)"
                                variant="primary"
                                size="sm"
                                class="w-full flex items-center justify-center gap-2"
                            >
                                <img src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6 inline-block" />
                                Invoquer x10
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading, animation, résultats, etc. restent inchangés -->
        <div v-if="isInvoking" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm">
            <div class="text-center">
                <div class="text-8xl mb-6 animate-spin">
                    <img src="/images/items/pokeball.png" alt="Chargement" class="w-24 h-24 object-contain">
                </div>
                <div class="text-2xl font-bold text-white">
                    Chargement...
                </div>
            </div>
        </div>
        <EvolutionAnimation
            v-if="showEvolutionAnimation"
            :max-rarity="getMaxRarity()"
            :has-shiny="hasShinyPokemon()"
            :on-complete="onEvolutionComplete"
        />
        <div v-if="showGachaResults" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm flex items-center justify-center">
            <div class="text-center max-w-6xl mx-auto px-6">
                <h2 class="text-3xl font-bold mb-2 bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent">
                    🎁 Vos récompenses vous attendent !
                </h2>
                <p class="text-base-content/50 mb-20">
                    Survolez les balls pour apercevoir leur rareté, puis cliquez pour révéler !
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-20 mb-8">
                    <div
                        v-for="(pokemon, index) in invocationResults"
                        :key="index"
                        class="animate-slideInUp"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <GachaBall
                            :ball-type="currentBallType"
                            :pokemon="pokemon"
                            :index="index"
                            :revealed="revealedBalls[index]"
                            @reveal="revealBall"
                        />
                    </div>
                </div>
                <div class="flex gap-4 justify-center mt-20">
                    <Button
                        @click="revealAllBalls"
                        variant="outline"
                        size="lg"
                        :disabled="allBallsRevealed"
                    >
                        🎊 Tout révéler
                    </Button>
                    <Button
                        v-if="allBallsRevealed"
                        @click="closeResults"
                        variant="outline"
                        size="lg"
                    >
                        ✖️ Fermer
                    </Button>
                </div>
            </div>
        </div>
        <Modal :show="showFinalResults" @close="closeResults" max-width="7xl">
            <template #header>
                <div class="text-center">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent">
                        🎉 Résumé de l'invocation
                    </h2>
                    <p class="text-base-content/70 mt-2">
                        {{ transformedResults.length }} nouveau{{ transformedResults.length > 1 ? 'x' : '' }} Pokémon ajouté{{ transformedResults.length > 1 ? 's' : '' }} à votre collection !
                    </p>
                </div>
            </template>
            <template #default>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="(pokemon, index) in transformedResults"
                        :key="index"
                        class="transform transition-all duration-300 animate-slide-in-up"
                        :style="{ animationDelay: `${index * 50}ms` }"
                    >
                        <PokemonCard
                            :entry="pokemon"
                            size="large"
                            variant="modal"
                            :show-details="true"
                        />
                    </div>
                </div>
                <div class="text-center mt-8">
                    <Button
                        @click="closeResults"
                        variant="primary"
                        size="lg"
                        class="min-w-48"
                    >
                        🎉 Parfait !
                    </Button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideInUp {
    animation: slideInUp 0.6s ease-out forwards;
    opacity: 0;
}

.animate-slide-in-up {
    animation: slideUp 0.5s ease-out forwards;
}
</style>
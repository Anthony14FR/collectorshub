<script setup lang="ts">
import { ref, computed, reactive } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import GachaBall from '@/Components/Game/GachaBall.vue';
import PokemonCard from '@/Components/Cards/PokemonCard.vue';

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
const showGachaResults = ref(false);
const showFinalResults = ref(false);
const invocationResults = ref<any[]>([]);
const revealedBalls = ref<boolean[]>([]);
const currentBallType = ref('');

// Récupération des quantités
const getInventoryQuantity = (itemName: string) => {
    const item = inventory.find(inv => inv.item?.name === itemName);
    return item ? item.quantity : 0;
};

const pokeballQuantity = getInventoryQuantity('Pokeball');
const masterballQuantity = getInventoryQuantity('Masterball');

// Transformation des données pour le composant PokemonCard
const transformedResults = computed(() => {
    return invocationResults.value.map(pokemon => ({
        id: pokemon.id,
        user_id: auth.user.id,
        pokemon_id: pokemon.pokemon_id, // ✅ Utilise pokemon_id pour les images
        level: pokemon.level,
        star: pokemon.star,
        nickname: null,
        is_favorite: false,
        is_in_team: false,
        obtained_at: new Date().toISOString(),
        pokemon: {
            id: pokemon.pokemon_id, // ✅ Utilise pokemon_id
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
            console.log('🎉 Invocation réussie!', data.pokemons);
            console.log('🔮 Pokémons obtenus:', data.pokemons.map(p => `${p.name} (${p.rarity}${p.is_shiny ? ' ✨' : ''})`));
            console.log('🖼️ Vérification des IDs:', data.pokemons.map(p => ({ name: p.name, pokemon_id: p.pokemon_id, pokedex_id: p.pokedex_id, is_shiny: p.is_shiny })));

            invocationResults.value = data.pokemons;
            revealedBalls.value = new Array(data.pokemons.length).fill(false);

            setTimeout(() => {
                isInvoking.value = false;
                showGachaResults.value = true;
            }, 2000);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'invocation');
        }
    } catch (error) {
        console.error('❌ Erreur invocation:', error);
        isInvoking.value = false;
        alert('Erreur lors de l\'invocation. Veuillez réessayer.');
    }
};

const revealBall = (index: number) => {
    revealedBalls.value[index] = true;
    console.log(`🎁 Révélation de la ball ${index + 1}:`, invocationResults.value[index]);
};

const revealAllBalls = () => {
    revealedBalls.value = revealedBalls.value.map(() => true);
    console.log('🎊 Toutes les balls révélées!');
};

const allBallsRevealed = computed(() => {
    return revealedBalls.value.every(revealed => revealed);
});

const showAllResults = () => {
    showGachaResults.value = false;
    showFinalResults.value = true;
    console.log('📋 Affichage des résultats finaux');
};

const closeResults = () => {
    showGachaResults.value = false;
    showFinalResults.value = false;
    invocationResults.value = [];
    revealedBalls.value = [];
    console.log('🔄 Rechargement de la page...');
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

    <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
        <BackgroundEffects />

        <!-- Header -->
        <div class="relative z-10 p-6">
            <div class="flex items-center justify-between mb-8">
                <Button
                    @click="goBack"
                    variant="ghost"
                    size="sm"
                    class="!bg-base-100/60 !backdrop-blur-sm"
                >
                    ← Retour
                </Button>

                <div class="text-center">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent">
                        ⚡ Invocation
                    </h1>
                    <p class="text-base-content/70 mt-2">
                        Choisissez votre type de ball et la quantité
                    </p>
                </div>

                <div class="w-20"></div>
            </div>
        </div>

        <!-- Animation d'invocation -->
        <div v-if="isInvoking" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="text-center">
                <div class="text-8xl mb-4 animate-bounce">
                    {{ getBallEmoji(currentBallType) }}
                </div>
                <div
                    :class="[
                        'text-2xl font-bold mb-4 bg-gradient-to-r bg-clip-text text-transparent',
                        currentBallType === 'Masterball' ? 'from-warning to-error' : 'from-primary to-secondary'
                    ]"
                >
                    ⚡ Invocation en cours...
                </div>
                <div class="text-base-content/70">
                    Préparation de {{ invocationResults.length }} pokéball{{ invocationResults.length > 1 ? 's' : '' }}...
                </div>
            </div>
        </div>

        <!-- Portails d'invocation -->
        <div v-if="!showGachaResults && !showFinalResults" class="relative z-10 max-w-4xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Pokeball Portal -->
                <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-6">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">⚾</div>
                        <h2 class="text-2xl font-bold text-primary mb-2">Pokeball</h2>
                        <p class="text-base-content/70">{{ pokeballQuantity }} disponible{{ pokeballQuantity > 1 ? 's' : '' }}</p>
                    </div>

                    <div class="bg-base-100/40 rounded-xl p-4 mb-6">
                        <h3 class="font-semibold mb-3 text-center">Chances</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Normal</span>
                                <span>70%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-info">Rare</span>
                                <span>27%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary">Epic</span>
                                <span>2.7%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-warning">Légendaire</span>
                                <span>0.3%</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Button
                            @click="startInvocation('Pokeball', 1)"
                            :disabled="!canInvoke('Pokeball', 1)"
                            variant="secondary"
                            size="lg"
                            class="w-full"
                        >
                            🎯 Invoquer x1
                        </Button>

                        <Button
                            @click="startInvocation('Pokeball', 10)"
                            :disabled="!canInvoke('Pokeball', 10)"
                            variant="primary"
                            size="lg"
                            class="w-full"
                        >
                            🌟 Invoquer x10
                        </Button>
                    </div>
                </div>

                <!-- Masterball Portal -->
                <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-6">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">🏐</div>
                        <h2 class="text-2xl font-bold text-warning mb-2">Masterball</h2>
                        <p class="text-base-content/70">{{ masterballQuantity }} disponible{{ masterballQuantity > 1 ? 's' : '' }}</p>
                    </div>

                    <div class="bg-base-100/40 rounded-xl p-4 mb-6">
                        <h3 class="font-semibold mb-3 text-center">Chances</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Normal</span>
                                <span>34%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-info">Rare</span>
                                <span>60%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary">Epic</span>
                                <span>5%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-warning">Légendaire</span>
                                <span>1%</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Button
                            @click="startInvocation('Masterball', 1)"
                            :disabled="!canInvoke('Masterball', 1)"
                            variant="secondary"
                            size="lg"
                            class="w-full"
                        >
                            🎯 Invoquer x1
                        </Button>

                        <Button
                            @click="startInvocation('Masterball', 10)"
                            :disabled="!canInvoke('Masterball', 10)"
                            variant="primary"
                            size="lg"
                            class="w-full"
                        >
                            🌟 Invoquer x10
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résultats Gacha (Balls à révéler) -->
        <div v-if="showGachaResults" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-sm flex items-center justify-center">
            <div class="text-center max-w-6xl mx-auto px-6">
                <h2 class="text-3xl font-bold mb-2 bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent">
                    🎁 Vos récompenses vous attendent !
                </h2>
                <p class="text-base-content/70 mb-8">
                    Survolez les balls pour apercevoir leur rareté, puis cliquez pour révéler !
                </p>

                <!-- Grille des balls -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-8">
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

                <!-- Boutons d'action -->
                <div class="flex gap-4 justify-center">
                    <Button
                        @click="revealAllBalls"
                        variant="secondary"
                        size="lg"
                        :disabled="allBallsRevealed"
                    >
                        🎊 Tout révéler
                    </Button>

                    <Button
                        v-if="allBallsRevealed"
                        @click="showAllResults"
                        variant="primary"
                        size="lg"
                    >
                        📋 Voir le résumé
                    </Button>
                </div>
            </div>
        </div>

        <!-- Modal des résultats finaux -->
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

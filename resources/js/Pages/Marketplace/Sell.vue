<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Pokedex } from '@/types/pokedex';
import type { Marketplace } from '@/types/marketplace';

import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Badge from '@/Components/UI/Badge.vue';
import Alert from '@/Components/UI/Alert.vue';
import SaleRow from '@/Components/Marketplace/SaleRow.vue';
import PokemonCard from '@/Components/Cards/PokemonCard.vue';

interface Props extends PageProps {
    auth: {
        user: User;
    };
    userPokemons: Pokedex[];
    myListings: Marketplace[];
}

const props = defineProps<Props>();

// État local
const selectedPokemon = ref<Pokedex | null>(null);
const pokemonSelectorOpen = ref(false);
const processing = ref(false);
const userPokemons = ref(props.userPokemons || []);
const myListings = ref(props.myListings || []);
const showSuccessAlert = ref(false);
const showErrorAlert = ref(false);

// Form
const form = ref({
    pokemon_id: '',
    price: ''
});

// Computed
const priceRange = computed(() => {
    if (!selectedPokemon.value) {
        return { min: 10, max: 1000000 };
    }

    const ranges: Record<string, { min: number; max: number }> = {
        normal: { min: 10, max: 1000000 },
        rare: { min: 100, max: 1000000 },
        epic: { min: 1000, max: 1000000 },
        legendary: { min: 10000, max: 1000000 }
    };

    return ranges[selectedPokemon.value.pokemon?.rarity || 'normal'] || ranges.normal;
});

const canSubmit = computed(() => {
    return form.value.pokemon_id &&
           form.value.price &&
           parseInt(form.value.price) >= priceRange.value.min &&
           parseInt(form.value.price) <= priceRange.value.max;
});

const availablePokemons = computed(() => {
    const listedPokemonIds = myListings.value.map(listing => listing.pokemon_id);
    return userPokemons.value.filter(pokemon =>
        !listedPokemonIds.includes(pokemon.id) && !pokemon.is_in_team
    );
});

const activeSellListings = computed(() => {
    return myListings.value.filter(listing => listing.status === 'active');
});

// Méthodes
const formatPrice = (price: any) => {
    return new Intl.NumberFormat('fr-FR').format(price);
};

const selectPokemon = (pokemon: any) => {
    selectedPokemon.value = pokemon;
    form.value.pokemon_id = pokemon.id.toString();
    form.value.price = priceRange.value.min.toString();
    pokemonSelectorOpen.value = false;
};

const clearSelection = () => {
    selectedPokemon.value = null;
    form.value.pokemon_id = '';
    form.value.price = '';
};

const listPokemon = () => {
    if (!canSubmit.value) return;

    processing.value = true;

    router.post('/marketplace/sell', {
        pokemon_id: form.value.pokemon_id,
        price: form.value.price
    }, {
        preserveScroll: true,
        onSuccess: () => {
            form.value.pokemon_id = '';
            form.value.price = '';
            selectedPokemon.value = null;
            processing.value = false;
            showSuccessAlert.value = true;
            refreshMyListings();
        },
        onError: () => {
            processing.value = false;
            showErrorAlert.value = true;
        }
    });
};

const cancelListing = (listingId: number) => {
    processing.value = true;

    router.post(`/marketplace/cancel/${listingId}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            myListings.value = myListings.value.filter(listing => listing.id !== listingId);
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};

const refreshMyListings = async () => {
    try {
        const response = await fetch('/marketplace/listings?myListings=true');
        const data = await response.json();
        myListings.value = data;
    } catch (error) {
        console.error('Erreur lors de l\'actualisation:', error);
    }
};

// Watchers
watch(() => selectedPokemon.value, (newPokemon) => {
    if (newPokemon && (!form.value.price || parseInt(form.value.price) < priceRange.value.min)) {
        form.value.price = priceRange.value.min.toString();
    }
});
</script>

<template>
    <Head title="Vendre un Pokémon" />

    <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative" data-theme="pokemon">
        <BackgroundEffects />

        <!-- Bouton Retour toujours visible -->
        <div class="fixed top-6 left-6 z-50">
            <Button
                @click="router.visit('/marketplace')"
                variant="ghost"
                size="lg"
                class="shadow-xl"
            >
                <span class="text-lg mr-2">←</span>
                Retour Marketplace
            </Button>
        </div>

        <!-- Header moderne -->
        <div class="relative z-10 pt-12 pb-8">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <Badge
                        variant="warning"
                        size="lg"
                        pill
                        class="mb-6 shadow-xl shadow-warning/20"
                    >
                        <div class="w-2 h-2 bg-warning rounded-full animate-pulse"></div>
                        <span class="font-semibold">Vendre</span>
                        <div class="w-2 h-2 bg-warning rounded-full animate-pulse"></div>
                    </Badge>

                    <h1 class="text-5xl md:text-7xl font-black mb-6">
                        <span class="bg-gradient-to-r from-warning via-secondary to-accent bg-clip-text text-transparent drop-shadow-2xl">
                            VENDRE POKÉMON
                        </span>
                    </h1>

                    <p class="text-xl text-base-content/80 max-w-2xl mx-auto leading-relaxed mb-8">
                        Mettez vos Pokémon en vente et gagnez des pièces d'or
                    </p>

                    <!-- Cash Display -->
                    <div class="bg-base-100/60 backdrop-blur-sm border-2 border-warning/30 rounded-2xl px-8 py-4 inline-block">
                        <div class="flex items-center gap-3">
                            <span class="text-warning text-2xl">💰</span>
                            <div>
                                <div class="text-sm text-base-content/70">Votre solde</div>
                                <div class="text-2xl font-black bg-gradient-to-r from-warning to-secondary bg-clip-text text-transparent">
                                    {{ formatPrice(props.auth.user.cash) }} Pièces
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu Principal -->
        <div class="relative z-10 pb-12">
            <div class="container mx-auto px-6">
                <!-- Messages Flash avec Alert component -->
                <div v-if="showSuccessAlert" class="mb-8">
                    <Alert
                        type="success"
                        variant="outlined"
                        size="lg"
                        :dismissible="true"
                        @dismiss="showSuccessAlert = false"
                    >
                        {{ $page.props.flash?.success || 'Pokémon mis en vente avec succès !' }}
                    </Alert>
                </div>
                <div v-if="showErrorAlert || $page.props.errors?.message" class="mb-8">
                    <Alert
                        type="error"
                        variant="outlined"
                        size="lg"
                        :dismissible="true"
                        @dismiss="showErrorAlert = false"
                    >
                        {{ $page.props.errors?.message || 'Une erreur est survenue' }}
                    </Alert>
                </div>

                <!-- Grille principale -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-12">
                    <!-- Section Formulaire de Vente -->
                    <div class="space-y-8">
                        <div class="bg-base-100/60 backdrop-blur-sm border-2 border-warning/30 rounded-3xl p-8 shadow-2xl shadow-warning/10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-gradient-to-br from-warning/20 to-secondary/20 rounded-xl flex items-center justify-center">
                                    <span class="text-2xl">📝</span>
                                </div>
                                <h2 class="text-3xl font-bold bg-gradient-to-r from-warning to-secondary bg-clip-text text-transparent">
                                    Nouvelle Vente
                                </h2>
                            </div>

                            <!-- Sélection Pokémon -->
                            <div class="mb-8">
                                <label class="label mb-4">
                                    <span class="label-text text-lg font-semibold">Choisir un Pokémon</span>
                                    <span class="label-text-alt">{{ availablePokemons.length }} disponibles</span>
                                </label>

                                <div v-if="!selectedPokemon" class="text-center">
                                    <div
                                        @click="pokemonSelectorOpen = true"
                                        class="cursor-pointer border-2 border-dashed border-warning/40 rounded-2xl p-12 hover:border-warning/70 hover:bg-warning/10 transition-all duration-500 group"
                                    >
                                        <div class="w-20 h-20 bg-warning/20 rounded-full mx-auto mb-6 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                            <span class="text-3xl">🎯</span>
                                        </div>
                                        <h3 class="text-xl font-bold mb-2">Sélectionner un Pokémon</h3>
                                        <p class="text-base-content/70">Cliquez pour ouvrir votre collection</p>
                                    </div>
                                </div>

                                <div v-else class="bg-gradient-to-br from-warning/10 to-secondary/10 rounded-2xl p-6 border-2 border-warning/30">
                                    <div class="flex items-center gap-6">
                                        <div class="w-20 h-20 bg-gradient-to-br from-base-300/50 to-base-300/30 rounded-xl flex items-center justify-center overflow-hidden border-2 border-warning/30">
                                            <img
                                                :src="`/images/pokemon-gifs/${selectedPokemon.pokemon?.is_shiny ? (selectedPokemon.pokemon.id - 1000) + '_S' : selectedPokemon.pokemon?.id || 1}.gif`"
                                                :alt="selectedPokemon.pokemon?.name"
                                                class="w-16 h-16"
                                                style="image-rendering: pixelated;"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-2xl font-bold mb-2">{{ selectedPokemon.pokemon?.name }}</h3>
                                            <div class="flex gap-3 mb-2">
                                                <Badge variant="primary" size="md">{{ selectedPokemon.pokemon?.rarity }}</Badge>
                                                <Badge v-if="selectedPokemon.pokemon?.is_shiny" variant="warning" size="md">✨ Shiny</Badge>
                                            </div>
                                            <div class="flex gap-4 text-sm text-base-content/70">
                                                <span>❤️ {{ selectedPokemon.pokemon?.hp }}</span>
                                                <span>⚔️ {{ selectedPokemon.pokemon?.attack }}</span>
                                                <span>🛡️ {{ selectedPokemon.pokemon?.defense }}</span>
                                            </div>
                                        </div>
                                        <Button @click="clearSelection" variant="ghost" size="sm">
                                            <span class="text-lg">✕</span>
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div v-if="selectedPokemon" class="mb-8">
                                <Input
                                    v-model="form.price"
                                    type="number"
                                    :min="priceRange.min"
                                    :max="priceRange.max"
                                    label="Prix de vente"
                                    :helper-text="`Fourchette : ${formatPrice(priceRange.min)} - ${formatPrice(priceRange.max)} 💰`"
                                    placeholder="Entrez le prix de vente"
                                    size="lg"
                                    variant="bordered"
                                />

                                <!-- Range slider -->
                                <div class="mt-4">
                                    <input
                                        v-model="form.price"
                                        type="range"
                                        :min="priceRange.min"
                                        :max="priceRange.max"
                                        :step="priceRange.min < 100 ? 10 : 100"
                                        class="range range-warning w-full"
                                    />

                                    <!-- Prix suggérés -->
                                    <div class="flex justify-between text-xs text-base-content/60 mt-2">
                                        <span>{{ formatPrice(priceRange.min) }}</span>
                                        <span>{{ formatPrice(priceRange.min + (priceRange.max - priceRange.min) * 0.33) }}</span>
                                        <span>{{ formatPrice(priceRange.min + (priceRange.max - priceRange.min) * 0.66) }}</span>
                                        <span>{{ formatPrice(priceRange.max) }}</span>
                                    </div>
                                </div>

                                <!-- Aperçu du gain -->
                                <div v-if="form.price" class="mt-4 bg-success/10 border border-success/30 rounded-xl p-4">
                                    <div class="text-center">
                                        <div class="text-sm text-base-content/70 mb-1">Vous recevrez</div>
                                        <div class="text-2xl font-bold text-success">+ {{ formatPrice(form.price) }} 💰</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton Action -->
                            <Button
                                @click="listPokemon"
                                :disabled="!canSubmit || processing"
                                variant="primary"
                                size="lg"
                                class="w-full shadow-2xl shadow-warning/30"
                            >
                                <span class="text-xl mr-3">💼</span>
                                <span class="text-xl">{{ processing ? 'Mise en vente...' : 'Mettre en Vente' }}</span>
                            </Button>
                        </div>
                    </div>

                    <!-- Section Mes Ventes -->
                    <div class="space-y-8">
                        <div class="bg-base-100/60 backdrop-blur-sm border-2 border-secondary/30 rounded-3xl p-8 shadow-2xl shadow-secondary/10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-secondary/20 to-accent/20 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">📊</span>
                                    </div>
                                    <h2 class="text-3xl font-bold bg-gradient-to-r from-secondary to-accent bg-clip-text text-transparent">
                                        Mes Ventes Actives
                                    </h2>
                                </div>
                                <Badge variant="secondary" size="lg" class="text-lg px-4">
                                    {{ activeSellListings.length }}
                                </Badge>
                            </div>

                            <div class="max-h-[600px] overflow-y-auto space-y-6">
                                <div v-if="activeSellListings.length === 0" class="text-center py-16">
                                    <div class="w-24 h-24 bg-base-300/50 rounded-full mx-auto mb-6 flex items-center justify-center">
                                        <span class="text-4xl">📦</span>
                                    </div>
                                    <h3 class="text-2xl font-bold mb-4">Aucune vente en cours</h3>
                                    <p class="text-base-content/70 text-lg">Sélectionnez un Pokémon pour commencer à vendre !</p>
                                </div>

                                <SaleRow
                                    v-for="item in activeSellListings"
                                    :key="item.id"
                                    :item="item"
                                    @cancel="cancelListing"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sélection Pokémon Amélioré -->
        <Modal :show="pokemonSelectorOpen" @close="pokemonSelectorOpen = false" size="4xl">
            <template #header>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center">
                        <span class="text-xl">🎒</span>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Collection Pokémon
                        </h3>
                        <!-- Nombre de Pokémon disponible à gauche sous le titre -->
                        <p class="text-sm text-base-content/60 mt-1">
                            {{ availablePokemons.length }} Pokémon disponibles pour la vente
                        </p>
                    </div>
                </div>
            </template>
            <template #default>
                <div v-if="availablePokemons.length === 0" class="text-center py-20">
                    <div class="w-32 h-32 bg-base-300/50 rounded-full mx-auto mb-8 flex items-center justify-center">
                        <span class="text-6xl">😔</span>
                    </div>
                    <h3 class="text-3xl font-bold mb-4">Aucun Pokémon disponible</h3>
                    <p class="text-base-content/70 text-lg max-w-md mx-auto">
                        Les Pokémon en équipe ou déjà en vente ne peuvent pas être vendus
                    </p>
                </div>

                <!-- Grille de sélection avec PokemonCard -->
                <div v-else class="bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-2xl p-6 border-2 border-primary/20">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 max-h-[500px] overflow-y-auto">
                        <div
                            v-for="pokemon in availablePokemons"
                            :key="pokemon.id"
                            @click="selectPokemon(pokemon)"
                            class="cursor-pointer"
                        >
                            <PokemonCard
                                :entry="pokemon"
                                size="large"
                                variant="modal"
                                :show-details="true"
                                class="hover:border-primary/70 transition-all duration-300"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
/* Masquer les scrollbars sur desktop uniquement si nécessaire */
@media (min-width: 1024px) {
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
}

/* Éviter le débordement des cards dans le modal */
.grid > div {
    overflow: hidden;
}
</style>


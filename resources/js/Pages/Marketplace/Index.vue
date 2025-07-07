<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Marketplace } from '@/types/marketplace';

import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Badge from '@/Components/UI/Badge.vue';
import Alert from '@/Components/UI/Alert.vue';
import MarketplaceCard from '@/Components/Cards/MarketplaceCard.vue';

interface Props extends PageProps {
    auth: {
        user: User;
    };
    myListings: Marketplace[];
    otherListings: Marketplace[];
}

const props = defineProps<Props>();

// État local
const activeSection = ref<'buy' | 'sell'>('buy');
const filtersOpen = ref(false);
const loading = ref(false);
const myListings = ref(props.myListings || []);
const otherListings = ref(props.otherListings || []);
const showSuccessAlert = ref(false);
const showErrorAlert = ref(false);

// Filtres
const filters = ref({
    rarity: '',
    type: '',
    isShiny: '',
    minPrice: '',
    maxPrice: ''
});

// Computed
const displayedListings = computed(() => {
    return activeSection.value === 'buy' ? otherListings.value : myListings.value;
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR').format(price);
};

// Méthodes
const buyPokemon = (listingId: number) => {
    loading.value = true;
    router.post(`/marketplace/buy/${listingId}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
            showSuccessAlert.value = true;
            // Actualiser les données sans reload complet
            refreshListings();
        },
        onError: () => {
            loading.value = false;
            showErrorAlert.value = true;
        }
    });
};

const cancelListing = (listingId: number) => {
    loading.value = true;
    router.post(`/marketplace/cancel/${listingId}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            myListings.value = myListings.value.filter(listing => listing.id !== listingId);
            loading.value = false;
            showSuccessAlert.value = true;
        },
        onError: () => {
            loading.value = false;
            showErrorAlert.value = true;
        }
    });
};

const applyFilters = async () => {
    loading.value = true;

    try {
        const params: any = {};
        if (filters.value.rarity) params.rarity = filters.value.rarity;
        if (filters.value.type) params.type = filters.value.type;
        if (filters.value.isShiny) params.isShiny = filters.value.isShiny;
        if (filters.value.minPrice) params.minPrice = filters.value.minPrice;
        if (filters.value.maxPrice) params.maxPrice = filters.value.maxPrice;

        const response = await fetch(`/marketplace/listings?${new URLSearchParams(params)}`);
        const data = await response.json();
        otherListings.value = data;
    } catch (error) {
        console.error('Erreur:', error);
    } finally {
        loading.value = false;
    }
};

// Nouvelle méthode pour confirmer les filtres
const confirmFilters = () => {
    applyFilters();
    filtersOpen.value = false;
};

// Méthode pour actualiser les données après achat
const refreshListings = async () => {
    try {
        const response = await fetch('/marketplace/listings');
        const data = await response.json();
        otherListings.value = data.filter((listing: any) => listing.seller_id !== props.auth.user.id);

        // Actualiser aussi mes ventes
        const myResponse = await fetch('/marketplace/listings?myListings=true');
        const myData = await myResponse.json();
        myListings.value = myData;
    } catch (error) {
        console.error('Erreur lors de l\'actualisation:', error);
    }
};

const resetFilters = () => {
    filters.value = {
        rarity: '',
        type: '',
        isShiny: '',
        minPrice: '',
        maxPrice: ''
    };
    // Ne pas appliquer les filtres si on est dans la section "sell"
    if (activeSection.value === 'buy') {
        applyFilters();
    }
};

const switchSection = (section: 'buy' | 'sell') => {
    activeSection.value = section;
    // Pas de router.reload() ou autre action qui pourrait causer le zoom
};
</script>

<template>
    <Head title="Marketplace" />

    <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative" data-theme="pokemon">
        <BackgroundEffects />

        <!-- Bouton Vendre toujours visible -->
        <div class="fixed top-6 right-6 z-50">
            <Button
                @click="router.visit('/marketplace/sell')"
                variant="primary"
                size="lg"
                class="shadow-2xl shadow-primary/30"
            >
                <span class="text-lg mr-2">💼</span>
                Vendre un Pokémon
            </Button>
        </div>

        <!-- Header moderne -->
        <div class="relative z-10 pt-12 pb-8">
            <div class="container mx-auto px-6">
                <div class="text-center mb-8">
                    <Badge
                        variant="primary"
                        size="lg"
                        pill
                        class="mb-6 shadow-xl shadow-primary/20"
                    >
                        <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                        <span class="font-semibold">Marketplace</span>
                        <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                    </Badge>

                    <h1 class="text-5xl md:text-7xl font-black mb-6">
                        <span class="bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-transparent drop-shadow-2xl">
                            POKÉMON MARKET
                        </span>
                    </h1>

                    <p class="text-xl text-base-content/80 max-w-2xl mx-auto leading-relaxed">
                        Achetez et vendez vos Pokémon dans le plus grand marché du monde
                    </p>

                    <!-- Cash Display -->
                    <div class="mt-8 inline-block bg-base-100/60 backdrop-blur-sm border-2 border-warning/30 rounded-2xl px-8 py-4">
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

                <!-- Navigation Sections avec transition fluide -->
                <div class="flex justify-center mb-12">
                    <div class="bg-base-100/60 backdrop-blur-sm border-2 border-base-300/30 rounded-3xl p-3 shadow-2xl shadow-primary/10">
                        <div class="flex gap-3">
                            <Button
                                @click="switchSection('buy')"
                                :variant="activeSection === 'buy' ? 'primary' : 'ghost'"
                                size="lg"
                                class="min-w-[200px] transition-all duration-300"
                            >
                                <span class="text-2xl mr-3">🛒</span>
                                <div class="flex flex-col items-start">
                                    <span>Acheter</span>
                                    <span class="text-xs opacity-70">{{ otherListings.length }} offres</span>
                                </div>
                            </Button>
                            <Button
                                @click="switchSection('sell')"
                                :variant="activeSection === 'sell' ? 'secondary' : 'ghost'"
                                size="lg"
                                class="min-w-[200px] transition-all duration-300"
                            >
                                <span class="text-2xl mr-3">💼</span>
                                <div class="flex flex-col items-start">
                                    <span>Mes Ventes</span>
                                    <span class="text-xs opacity-70">{{ myListings.length }} actives</span>
                                </div>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Actions Bar -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
                    <div class="flex flex-wrap gap-4">
                        <Button
                            v-if="activeSection === 'buy'"
                            @click="filtersOpen = true"
                            variant="outline"
                            size="lg"
                            class="shadow-lg"
                        >
                            <span class="text-lg mr-2">🔍</span>
                            Filtres Avancés
                        </Button>
                        <Button
                            @click="resetFilters"
                            variant="ghost"
                            size="lg"
                        >
                            Réinitialiser
                        </Button>
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
                        {{ $page.props.flash?.success }}
                    </Alert>
                </div>
                <div v-if="showErrorAlert" class="mb-8">
                    <Alert
                        type="error"
                        variant="outlined"
                        size="lg"
                        :dismissible="true"
                        @dismiss="showErrorAlert = false"
                    >
                        {{ $page.props.errors?.message }}
                    </Alert>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-20">
                    <div class="loading loading-spinner loading-lg text-primary"></div>
                    <p class="mt-4 text-base-content/70">Chargement...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="displayedListings.length === 0" class="text-center py-20">
                    <div class="bg-base-100/60 backdrop-blur-sm border-2 border-base-300/30 rounded-3xl p-12 max-w-md mx-auto shadow-xl">
                        <div class="w-24 h-24 bg-base-300/50 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <span class="text-5xl">{{ activeSection === 'buy' ? '🛒' : '💼' }}</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4">
                            {{ activeSection === 'buy' ? 'Aucune offre disponible' : 'Aucune vente en cours' }}
                        </h3>
                        <p class="text-base-content/70 mb-6">
                            {{ activeSection === 'buy'
                                ? 'Revenez plus tard pour découvrir de nouvelles offres !'
                                : 'Commencez par mettre votre premier Pokémon en vente !'
                            }}
                        </p>
                        <Button
                            v-if="activeSection === 'sell'"
                            @click="router.visit('/marketplace/sell')"
                            variant="primary"
                            size="lg"
                        >
                            Vendre maintenant
                        </Button>
                    </div>
                </div>

                <!-- Grille des Pokémon -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
                    <MarketplaceCard
                        v-for="listing in displayedListings"
                        :key="listing.id"
                        :item="listing"
                        :variant="activeSection"
                        @buy="buyPokemon"
                        @cancel="cancelListing"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Filtres avec case Shiny -->
        <Modal :show="filtersOpen" @close="filtersOpen = false" size="lg">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center">
                        <span class="text-lg">🔍</span>
                    </div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        Filtres Avancés
                    </h3>
                </div>
            </template>
            <template #default>
                <div class="space-y-8">
                    <div>
                        <label class="label">
                            <span class="label-text text-lg font-semibold">Rareté</span>
                        </label>
                        <select v-model="filters.rarity" class="select select-bordered select-lg w-full bg-base-100/60 backdrop-blur-sm">
                            <option value="">Toutes les raretés</option>
                            <option value="normal">Normal</option>
                            <option value="rare">Rare</option>
                            <option value="epic">Épique</option>
                            <option value="legendary">Légendaire</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text text-lg font-semibold">Type</span>
                        </label>
                        <select v-model="filters.type" class="select select-bordered select-lg w-full bg-base-100/60 backdrop-blur-sm">
                            <option value="">Tous les types</option>
                            <option value="fire">Feu</option>
                            <option value="water">Eau</option>
                            <option value="grass">Plante</option>
                            <option value="electric">Électrique</option>
                            <option value="psychic">Psy</option>
                            <option value="ice">Glace</option>
                            <option value="dragon">Dragon</option>
                            <option value="dark">Ténèbres</option>
                            <option value="fighting">Combat</option>
                            <option value="poison">Poison</option>
                            <option value="ground">Sol</option>
                            <option value="flying">Vol</option>
                            <option value="bug">Insecte</option>
                            <option value="rock">Roche</option>
                            <option value="ghost">Spectre</option>
                            <option value="steel">Acier</option>
                            <option value="fairy">Fée</option>
                        </select>
                    </div>

                    <!-- Case Shiny ajoutée -->
                    <div>
                        <label class="label">
                            <span class="label-text text-lg font-semibold">Pokémon Shiny</span>
                        </label>
                        <select v-model="filters.isShiny" class="select select-bordered select-lg w-full bg-base-100/60 backdrop-blur-sm">
                            <option value="">Peu importe</option>
                            <option value="true">Shiny uniquement ✨</option>
                            <option value="false">Non-shiny uniquement</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <Input
                            v-model="filters.minPrice"
                            type="number"
                            label="Prix minimum"
                            placeholder="0"
                            size="lg"
                            variant="bordered"
                        />
                        <Input
                            v-model="filters.maxPrice"
                            type="number"
                            label="Prix maximum"
                            placeholder="1000000"
                            size="lg"
                            variant="bordered"
                        />
                    </div>
                </div>
            </template>
            <template #actions>
                <div class="flex gap-4">
                    <Button @click="resetFilters" variant="ghost" size="lg">
                        Réinitialiser
                    </Button>
                    <Button @click="confirmFilters" variant="primary" size="lg" class="shadow-xl shadow-primary/30">
                        Confirmer
                    </Button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
/* Transitions fluides pour les sections */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

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
</style>


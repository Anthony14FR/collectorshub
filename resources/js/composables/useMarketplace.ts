import type {
  MarketplaceFilters,
  MarketplaceListing,
  NormalizedPokemon
} from '@/types/marketplace';
import {
  calculateMarketplaceStats,
  formatPrice,
  normalizePokemonData
} from '@/utils/marketplace';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

export function useMarketplace() {
  const loading = ref(false);
  const currentUserId = ref<number | null>(null);
  const myListings = ref<MarketplaceListing[]>([]);
  const otherListings = ref<MarketplaceListing[]>([]);
  const selectedListing = ref<MarketplaceListing | null>(null);
  const showPurchaseModal = ref(false);
  const showCancelModal = ref(false);

  const filters = ref<MarketplaceFilters>({
    rarity: '',
    type: '',
    isShiny: '',
    minPrice: '',
    maxPrice: ''
  });

  const rarityOptions = [
    { value: '', label: 'Toutes' },
    { value: 'normal', label: 'Normal' },
    { value: 'rare', label: 'Rare' },
    { value: 'epic', label: 'Épique' },
    { value: 'legendary', label: 'Légendaire' }
  ];

  const typeOptions = [
    { value: '', label: 'Tous' },
    { value: 'Normal', label: 'Normal' },
    { value: 'Feu', label: 'Feu' },
    { value: 'Eau', label: 'Eau' },
    { value: 'Electrik', label: 'Électrique' },
    { value: 'Plante', label: 'Plante' },
    { value: 'Glace', label: 'Glace' },
    { value: 'Combat', label: 'Combat' },
    { value: 'Poison', label: 'Poison' },
    { value: 'Sol', label: 'Sol' },
    { value: 'Vol', label: 'Vol' },
    { value: 'Psy', label: 'Psy' },
    { value: 'Insecte', label: 'Insecte' },
    { value: 'Roche', label: 'Roche' },
    { value: 'Spectre', label: 'Spectre' },
    { value: 'Dragon', label: 'Dragon' },
    { value: 'Tenebres', label: 'Ténèbres' },
    { value: 'Acier', label: 'Acier' },
    { value: 'Fee', label: 'Fée' }
  ];

  const shinyOptions = [
    { value: '', label: 'Tous' },
    { value: 'true', label: 'Shiny uniquement' },
    { value: 'false', label: 'Normal uniquement' }
  ];

  const stats = computed(() => calculateMarketplaceStats(otherListings.value));

  const displayedListings = computed(() => otherListings.value);

  const getPokemonData = (listing: MarketplaceListing): NormalizedPokemon => {
    return normalizePokemonData(listing);
  };

  const initializeData = (data: {
    myListings: MarketplaceListing[],
    otherListings: MarketplaceListing[],
    userId?: number
  }) => {
    myListings.value = data.myListings || [];
    otherListings.value = data.otherListings || [];
    if (data.userId) {
      currentUserId.value = data.userId;
    }
  };

  const applyFilters = async () => {
    loading.value = true;

    try {
      const params: any = {};

      if (filters.value.rarity) params.rarity = filters.value.rarity;
      if (filters.value.type) params.type = filters.value.type;
      if (filters.value.isShiny) params.isShiny = filters.value.isShiny;
      if (filters.value.minPrice) params.minPrice = Number(filters.value.minPrice);
      if (filters.value.maxPrice) params.maxPrice = Number(filters.value.maxPrice);

      const response = await fetch(`/marketplace/listings?${new URLSearchParams(params)}`);
      const data = await response.json();

      const filteredData = currentUserId.value
        ? data.filter((listing: MarketplaceListing) => listing.seller_id !== currentUserId.value)
        : data;

      otherListings.value = filteredData;
    } catch (error) {
      console.error('Erreur lors de la récupération des annonces:', error);
    } finally {
      loading.value = false;
    }
  };

  const showPurchaseConfirm = (listing: MarketplaceListing) => {
    selectedListing.value = listing;
    showPurchaseModal.value = true;
  };

  const showCancelConfirm = (listing: MarketplaceListing) => {
    selectedListing.value = listing;
    showCancelModal.value = true;
  };

  const closeModals = () => {
    showPurchaseModal.value = false;
    showCancelModal.value = false;
    selectedListing.value = null;
  };

  const confirmPurchase = () => {
    if (!selectedListing.value) return;

    loading.value = true;

    router.post(`/marketplace/buy/${selectedListing.value.id}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        otherListings.value = otherListings.value.filter(
          listing => listing.id !== selectedListing.value!.id
        );
        closeModals();
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    });
  };

  const confirmCancel = () => {
    if (!selectedListing.value) return;

    loading.value = true;

    router.post(`/marketplace/cancel/${selectedListing.value.id}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(
          listing => listing.id !== selectedListing.value!.id
        );
        closeModals();
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    });
  };

  return {
    loading,
    myListings,
    otherListings,
    selectedListing,
    showPurchaseModal,
    showCancelModal,
    filters,
    rarityOptions,
    typeOptions,
    shinyOptions,
    stats,
    displayedListings,
    getPokemonData,
    formatPrice,
    initializeData,
    applyFilters,
    showPurchaseConfirm,
    showCancelConfirm,
    closeModals,
    confirmPurchase,
    confirmCancel,
  };
} 
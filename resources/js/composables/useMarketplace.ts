import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import type { 
  MarketplaceListing, 
  MarketplaceFilters,
  NormalizedPokemon 
} from '@/types/marketplace';
import { 
  normalizePokemonData,
  formatPrice,
  calculateMarketplaceStats,
  filterListings
} from '@/utils/marketplace';

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
    { value: 'normal', label: 'Normal' },
    { value: 'fire', label: 'Feu' },
    { value: 'water', label: 'Eau' },
    { value: 'electric', label: 'Électrique' },
    { value: 'grass', label: 'Plante' },
    { value: 'ice', label: 'Glace' },
    { value: 'fighting', label: 'Combat' },
    { value: 'poison', label: 'Poison' },
    { value: 'ground', label: 'Sol' },
    { value: 'flying', label: 'Vol' },
    { value: 'psychic', label: 'Psy' },
    { value: 'bug', label: 'Insecte' },
    { value: 'rock', label: 'Roche' },
    { value: 'ghost', label: 'Spectre' },
    { value: 'dragon', label: 'Dragon' },
    { value: 'dark', label: 'Ténèbres' },
    { value: 'steel', label: 'Acier' },
    { value: 'fairy', label: 'Fée' }
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
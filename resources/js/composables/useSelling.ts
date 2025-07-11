import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import type { PokedexEntry, MarketplaceListing } from '@/types/marketplace';
import { 
  normalizePokedexEntry, 
  formatPrice, 
  getPriceRangeForRarity,
  canSellPokemon,
  isValidPrice 
} from '@/utils/marketplace';

export function useSelling() {
  const userPokemons = ref<PokedexEntry[]>([]);
  const myListings = ref<MarketplaceListing[]>([]);
  const selectedPokemon = ref<PokedexEntry | null>(null);
  const showCreateModal = ref(false);
  const showPokemonModal = ref(false);
  const showCancelModal = ref(false);
  const listingToCancel = ref<MarketplaceListing | null>(null);
  const processing = ref(false);
  const priceRange = ref({ min: 10, max: 1000000 });

  const form = ref({
    pokemon_id: '',
    price: ''
  });

  const availablePokemons = computed(() => {
    const alreadyListedIds = myListings.value.map(listing => listing.pokemon.id);
    return userPokemons.value.filter(pokemon =>
      canSellPokemon(pokemon) && 
      !alreadyListedIds.includes(pokemon.id)
    );
  });

  const canSubmit = computed(() => {
    if (!selectedPokemon.value) return false;
    
    const rarity = selectedPokemon.value.pokemon.rarity;
    const price = Number(form.value.price);
    
    return form.value.pokemon_id &&
           form.value.price &&
           isValidPrice(price, rarity);
  });

  const initializeData = (data: { userPokemons: PokedexEntry[], myListings: MarketplaceListing[] }) => {
    userPokemons.value = data.userPokemons || [];
    myListings.value = data.myListings || [];
  };

  const selectPokemon = (pokemon: PokedexEntry) => {
    
    form.value.pokemon_id = pokemon.id.toString();
    selectedPokemon.value = pokemon;
    
    updatePriceRange();
    showCreateModal.value = false;
  };

  const updatePriceRange = () => {
    if (!selectedPokemon.value) return;

    const rarity = selectedPokemon.value.pokemon.rarity;
    priceRange.value = getPriceRangeForRarity(rarity);

    const currentPrice = Number(form.value.price);
    if (!currentPrice || currentPrice < priceRange.value.min) {
      form.value.price = priceRange.value.min.toString();
    } else if (currentPrice > priceRange.value.max) {
      form.value.price = priceRange.value.max.toString();
    }
  };

  const resetForm = () => {
    form.value = { pokemon_id: '', price: '' };
    selectedPokemon.value = null;
  };

  const openCreateModal = () => {
    showCreateModal.value = true;
  };

  const showPokemonDetails = (pokemon: PokedexEntry) => {
    selectedPokemon.value = pokemon;
    showPokemonModal.value = true;
  };

  const showCancelConfirm = (listing: MarketplaceListing) => {
    listingToCancel.value = listing;
    showCancelModal.value = true;
  };

  const closeModals = () => {
    showCreateModal.value = false;
    showPokemonModal.value = false;
    showCancelModal.value = false;
    listingToCancel.value = null;
  };

  const listPokemon = () => {
    if (!canSubmit.value) return;

    processing.value = true;

    router.post('/marketplace/sell', {
      pokemon_id: form.value.pokemon_id,
      price: form.value.price
    }, {
      preserveScroll: true,
      onSuccess: (page: any) => {
        const responseData = page.props as any;
        if (responseData.myListings) {
          myListings.value = responseData.myListings;
        }
        resetForm();
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      }
    });
  };

  const confirmCancel = () => {
    if (!listingToCancel.value) return;

    processing.value = true;

    router.post(`/marketplace/cancel/${listingToCancel.value.id}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        myListings.value = myListings.value.filter(
          listing => listing.id !== listingToCancel.value!.id
        );
        closeModals();
        processing.value = false;
      },
      onError: () => {
        processing.value = false;
      }
    });
  };

  return {
    userPokemons,
    myListings,
    selectedPokemon,
    showCreateModal,
    showPokemonModal,
    showCancelModal,
    listingToCancel,
    processing,
    priceRange,
    form,
    
    availablePokemons,
    canSubmit,
    
    normalizePokedexEntry,
    formatPrice,
    getPriceRangeForRarity,
    
    initializeData,
    selectPokemon,
    updatePriceRange,
    resetForm,
    openCreateModal,
    showPokemonDetails,
    showCancelConfirm,
    closeModals,
    listPokemon,
    confirmCancel,
  };
} 
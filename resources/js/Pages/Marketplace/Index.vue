<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import type { MarketplaceListing } from '@/types/marketplace';
import { useMarketplace } from '@/composables/useMarketplace';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Select from '@/Components/UI/Select.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Modal from '@/Components/UI/Modal.vue';
import ConfirmationModal from '@/Components/UI/ConfirmationModal.vue';
import Spinner from '@/Components/UI/Spinner.vue';
import MyListingsSection from '@/Components/Game/MyListingsSection.vue';
import MarketplaceListingCard from '@/Components/Cards/MarketplaceListingCard.vue';

interface Props {
  myListings: MarketplaceListing[];
  otherListings: MarketplaceListing[];
  auth?: {
    user: {
      id: number;
      username: string;
      cash: number;
    };
  };
  flash?: {
    success?: string;
  };
  errors?: {
    message?: string;
  };
}

const props = defineProps<Props>();
const {
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
} = useMarketplace();

const currentPage = ref(1);
const itemsPerPage = 9;

const userCash = computed(() => props.auth?.user?.cash || 0);

const paginatedListings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return displayedListings.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(displayedListings.value.length / itemsPerPage);
});

const changePage = (page: number) => {
  currentPage.value = page;
};

onMounted(() => {
  initializeData({
    myListings: props.myListings,
    otherListings: props.otherListings,
    userId: props.auth?.user?.id
  });

  if (myListings.value.length === 0 && otherListings.value.length === 0) {
    applyFilters();
  }
});
</script>


<template>
  <Head title="Marketplace" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-8 px-4">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏪 MARKETPLACE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            POKÉMONS À VENDRE
          </p>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-4 p-4 lg:p-8">
        <div class="w-full lg:w-64 order-1 lg:order-1">
          <Button
            @click="router.visit('/me')"
            variant="secondary"
            size="sm"
            class="w-full mb-4"
          >
            ← Retour vers le menu
          </Button>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">🔍</span>
                FILTRES
              </h3>
            </div>

            <div class="p-3 space-y-3">
              <Select
                v-model="filters.rarity"
                :options="rarityOptions"
                label="Rareté"
                variant="default"
                size="xs"
              />
              <Select
                v-model="filters.type"
                :options="typeOptions"
                label="Type"
                variant="default"
                size="xs"
              />
              <Select
                v-model="filters.isShiny"
                :options="shinyOptions"
                label="Shiny"
                variant="default"
                size="xs"
              />
              <Input
                v-model="filters.minPrice"
                type="number"
                label="Prix min"
                placeholder="0"
                variant="default"
                size="sm"
              />
              <Input
                v-model="filters.maxPrice"
                type="number"
                label="Prix max"
                placeholder="999999"
                variant="default"
                size="sm"
              />

              <Button
                @click="applyFilters"
                variant="secondary"
                size="sm"
                :disabled="loading"
                class="w-full"
              >
                {{ loading ? '🔄' : '🔍' }} Filtrer
              </Button>
            </div>
          </div>
        </div>

        <div class="flex-1 order-2 lg:order-2">
          <div v-if="props.flash?.success" class="mb-4">
            <Alert type="success" :message="props.flash.success" />
          </div>
          <div v-if="props.errors?.message" class="mb-4">
            <Alert type="error" :message="props.errors.message" />
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[600px] lg:h-[800px] flex flex-col sm:max-w-[1000px] mx-auto">
            <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📦</span>
                MARKETPLACE
              </h3>
            </div>

            <div class="flex-1 overflow-y-auto p-4" >
              <div v-if="loading" class="flex justify-center items-center py-12">
                <Spinner size="md" />
              </div>

              <div v-else-if="paginatedListings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <div
                  v-for="listing in paginatedListings"
                  :key="listing.id"
                  class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 group"
                >
                  <MarketplaceListingCard 
                    :listing="listing"
                    :user-cash="userCash"
                    :loading="loading"
                    @purchase="showPurchaseConfirm"
                  />
                </div>
              </div>

              <div v-else class="text-center py-8">
                <p class="text-2xl mb-2">🛒</p>
                <p class="text-sm mb-1">Aucun Pokémon en vente</p>
                <p class="opacity-70 text-xs">Revenez plus tard !</p>
              </div>
            </div>

            <div v-if="totalPages > 1" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-4 py-3 border-t border-warning/20">
              <div class="flex justify-center items-center gap-2">
                <Button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  variant="ghost"
                  size="sm"
                >
                  ←
                </Button>

                <span v-for="page in totalPages" :key="page">
                  <Button
                    @click="changePage(page)"
                    :variant="currentPage === page ? 'primary' : 'ghost'"
                    size="sm"
                    class="min-w-[2rem]"
                  >
                    {{ page }}
                  </Button>
                </span>

                <Button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  variant="ghost"
                  size="sm"
                >
                  →
                </Button>
              </div>
            </div>

            <div v-if="displayedListings.length > 0" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
              <div class="text-xs text-center text-base-content/70">
                {{ displayedListings.length }} annonce{{ displayedListings.length > 1 ? 's' : '' }} trouvée{{ displayedListings.length > 1 ? 's' : '' }}
                {{ totalPages > 1 ? ` - Page ${currentPage}/${totalPages}` : '' }}
              </div>
            </div>
          </div>
        </div>

        <div class="w-full lg:w-64 order-3 lg:order-3 space-y-4">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-secondary/10 to-accent/5 border-b border-secondary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">⚙️</span>
                ACTIONS
              </h3>
            </div>

            <div class="p-3">
              <Button
                @click="router.visit('/marketplace/sell')"
                variant="secondary"
                size="sm"
                class="w-full"
              >
                🏷️ Vendre
              </Button>
            </div>
          </div>

          <MyListingsSection
            :listings="myListings"
            :show-cancel-button="true"
            @cancel-listing="showCancelConfirm"
          />

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="bg-gradient-to-r from-success/10 to-success/5 px-3 py-2 border-b border-success/20">
              <h4 class="text-xs font-bold tracking-wider">PORTE-MONNAIE</h4>
            </div>
            <div class="p-3 text-center">
              <div class="text-2xl font-bold text-success">{{ formatPrice(userCash) }}</div>
              <div class="text-xs text-base-content/70">Votre solde</div>
            </div>
          </div>
        </div>
      </div>

      <ConfirmationModal
        :show="showPurchaseModal"
        :listing="selectedListing"
        :user-cash="userCash"
        :loading="loading"
        @close="closeModals"
        @confirm="confirmPurchase"
      />

      <Modal :show="showCancelModal" @close="closeModals">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-error/20 to-error/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">🗑️</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent">
                Retirer de la vente
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-error">Confirmation requise</span>
              </div>
            </div>
          </div>
        </template>

        <template #default>
          <div v-if="selectedListing" class="space-y-4">
            <p class="text-base-content">
              Êtes-vous sûr de vouloir retirer
              <span class="font-bold">{{ getPokemonData(selectedListing).name }}</span>
              de la vente ?
            </p>

            <div class="flex gap-3">
              <Button
                @click="closeModals"
                variant="outline"
                size="lg"
                class="flex-1"
                :disabled="loading"
              >
                Annuler
              </Button>

              <Button
                @click="confirmCancel"
                variant="outline"
                size="lg"
                class="flex-1 !border-error !text-error hover:!bg-error hover:!text-white"
                :disabled="loading"
              >
                {{ loading ? '🔄 En cours...' : '🗑️ Confirmer' }}
              </Button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>
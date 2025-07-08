<template>
  <Head title="Vendre un Pokémon" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏷️ VENDRE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            METTRE EN VENTE
          </p>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[400px] z-30">
        <div v-if="($page.props as any).flash?.success" class="mb-2">
          <Alert type="success" :message="($page.props as any).flash.success" />
        </div>
        <div v-if="($page.props as any).errors?.message" class="mb-2">
          <Alert type="error" :message="($page.props as any).errors.message" />
        </div>
      </div>

      <div class="absolute left-4 top-20 w-72">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">➕</span>
              NOUVELLE ANNONCE
            </h3>
          </div>

          <div class="p-3">
            <div v-if="!selectedPokemon"
                 @click="showCreateModal = true"
                 class="bg-base-200/30 backdrop-blur-sm rounded-lg p-8 border-2 border-dashed border-base-300/50 hover:border-primary/50 transition-all duration-200 cursor-pointer group text-center">
              <div class="text-6xl text-base-content/30 group-hover:text-primary/50 transition-colors duration-200 mb-2">+</div>
              <p class="text-sm text-base-content/70 group-hover:text-base-content transition-colors duration-200">
                Cliquer pour ajouter
              </p>
            </div>

            <div v-else class="space-y-4">
              <SelectedPokemonCard
                :pokemon="selectedPokemon"
                @close="resetForm"
              />

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-warning/20 p-4">
                <Input
                  v-model="form.price"
                  type="number"
                  label="Prix de vente"
                  :placeholder="`${formatPrice(priceRange.min)} - ${formatPrice(priceRange.max)}`"
                  variant="bordered"
                  size="sm"
                  :helper-text="`Recommandé : ${formatPrice(priceRange.min)} - ${formatPrice(priceRange.max)}`"
                />
              </div>

              <Button
                @click="listPokemon"
                :disabled="!canSubmit || processing"
                variant="primary"
                size="md"
                class="w-full bg-gradient-to-r from-success to-success/80 hover:from-success/90 hover:to-success/70 border-success/30 shadow-lg shadow-success/20"
              >
                {{ processing ? '🔄' : '💰' }} Mettre en vente
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute right-4 top-20 w-72">
        <MyListingsSection
          :listings="myListings"
          :show-cancel-button="true"
          @cancel-listing="showCancelConfirm"
        />

        <div class="mt-4">
          <Button
            @click="router.visit('/marketplace')"
            variant="secondary"
            size="sm"
            class="w-full"
          >
            ← Retour marketplace
          </Button>
        </div>
      </div>

      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80">
        <div class="bg-base-100/60 backdrop-blur-lg rounded-2xl border border-warning/30 p-6 shadow-xl">
          <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-warning/30 to-warning/10 rounded-full flex items-center justify-center mb-3 mx-auto border-2 border-warning/40">
              <span class="text-4xl">💰</span>
            </div>
            <h2 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
              Centre de Vente
            </h2>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-success/10 rounded-xl p-3 text-center border border-success/20">
              <div class="text-xs text-success/70 uppercase tracking-wide mb-1">Disponibles</div>
              <div class="text-2xl font-bold text-success">{{ availablePokemons.length }}</div>
            </div>
            <div class="bg-warning/10 rounded-xl p-3 text-center border border-warning/20">
              <div class="text-xs text-warning/70 uppercase tracking-wide mb-1">En vente</div>
              <div class="text-2xl font-bold text-warning">{{ myListings.length }}</div>
            </div>
          </div>

          <div class="bg-base-200/30 rounded-xl p-4 border border-base-300/30">
            <h3 class="text-sm font-bold text-center mb-3 text-base-content/80">📊 Guide des Prix</h3>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between items-center">
                <span class="text-base-content/70">Normal</span>
                <span class="text-success font-semibold">{{ formatPrice(getPriceRangeForRarity('normal').min) }} - {{ formatPrice(getPriceRangeForRarity('normal').max) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-blue-400">Rare</span>
                <span class="text-info font-semibold">{{ formatPrice(getPriceRangeForRarity('rare').min) }} - {{ formatPrice(getPriceRangeForRarity('rare').max) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-purple-400">Epic</span>
                <span class="text-secondary font-semibold">{{ formatPrice(getPriceRangeForRarity('epic').min) }} - {{ formatPrice(getPriceRangeForRarity('epic').max) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-orange-400">Legendary</span>
                <span class="text-warning font-semibold">{{ formatPrice(getPriceRangeForRarity('legendary').min) }} - {{ formatPrice(getPriceRangeForRarity('legendary').max) }}</span>
              </div>
            </div>
          </div>

          <div class="mt-4 text-center">
            <p class="text-xs text-base-content/60 leading-relaxed">
              💡 Les prix sont automatiquement validés selon la rareté
            </p>
          </div>
        </div>
      </div>

      <Modal :show="showCreateModal" @close="showCreateModal = false" max-width="6xl">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-success/20 to-success/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">📦</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent">
                Choisir un Pokémon
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-success">{{ availablePokemons.length }} disponibles</span>
              </div>
            </div>
          </div>
        </template>

        <template #default>
          <div v-if="availablePokemons.length === 0" class="text-center py-8">
            <p class="text-2xl mb-2">😔</p>
            <p class="text-lg font-bold mb-2">Aucun Pokémon disponible</p>
            <p class="text-base-content/70 mb-4">Tous vos Pokémon sont dans votre équipe ou déjà en vente.</p>
            <Button @click="showCreateModal = false" variant="secondary" size="md">
              Fermer
            </Button>
          </div>

          <div v-else>
            <div class="mb-6">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                🎯 Choisir un Pokémon
              </h3>
              <p class="text-base-content/70 text-sm mt-1">Sélectionnez un Pokémon de votre inventaire à mettre en vente</p>
            </div>

            <div class="max-h-[500px] overflow-y-auto">
              <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                  <div
                    v-for="pokemon in availablePokemons"
                    :key="pokemon.id"
                    @click="selectPokemon(pokemon)"
                    class="cursor-pointer group"
                  >
                    <PokemonCard
                      :entry="pokemon"
                      size="large"
                      variant="modal"
                      :show-details="true"
                    />
                    <div class="mt-2 text-xs text-center text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                      Cliquer pour sélectionner
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Modal>

      <Modal :show="showPokemonModal" @close="showPokemonModal = false">
        <template #header>
          <div v-if="selectedPokemon" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
              <span class="text-lg">📦</span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                {{ selectedPokemon.pokemon?.name }}
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-warning">Niveau {{ selectedPokemon.level }}</span>
              </div>
            </div>
          </div>
        </template>

        <template #default>
          <div v-if="selectedPokemon" class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="flex justify-center">
              <div class="relative">
                <img
                  :src="`/images/pokemon-gifs/${selectedPokemon.pokemon?.is_shiny ? (selectedPokemon.pokemon.id - 1000) + '_S' : selectedPokemon.pokemon.id}.gif`"
                  :alt="selectedPokemon.pokemon?.name"
                  class="w-48 h-48 object-contain"
                  style="image-rendering: pixelated;"
                />
              </div>
            </div>

            <div class="space-y-6">
              <div class="text-center">
                <RarityBadge v-if="selectedPokemon.pokemon?.rarity" :rarity="selectedPokemon.pokemon.rarity" size="md" />
              </div>

              <div class="flex justify-center gap-2">
                <PokemonTypeBadge
                  v-for="type in parseTypes(selectedPokemon.pokemon?.types)"
                  :key="type"
                  :type="type"
                  size="sm"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">HP</span>
                  <p class="text-2xl font-bold text-error">{{ selectedPokemon.pokemon?.hp }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Attaque</span>
                  <p class="text-2xl font-bold text-warning">{{ selectedPokemon.pokemon?.attack }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Défense</span>
                  <p class="text-2xl font-bold text-info">{{ selectedPokemon.pokemon?.defense }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Vitesse</span>
                  <p class="text-2xl font-bold text-success">{{ selectedPokemon.pokemon?.speed }}</p>
                </div>
              </div>

              <div class="bg-base-200/50 rounded-xl p-4 text-center">
                <p class="text-base-content/70 text-sm mb-2">Statut</p>
                <div class="flex justify-center gap-2">
                  <span v-if="selectedPokemon.is_in_team" class="px-3 py-1 bg-primary/20 text-primary rounded-full text-sm">Dans l'équipe</span>
                  <span v-else class="px-3 py-1 bg-success/20 text-success rounded-full text-sm">Disponible</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Modal>

      <Modal :show="showCancelModal" @close="showCancelModal = false">
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
          <div v-if="listingToCancel" class="space-y-4">
            <div class="flex items-center gap-4 p-4 bg-base-200/30 rounded-xl">
              <img
                :src="`/images/pokemon-gifs/${listingToCancel.pokemon.pokemon?.is_shiny ? (listingToCancel.pokemon.pokemon.id - 1000) + '_S' : listingToCancel.pokemon.pokemon.id}.gif`"
                :alt="listingToCancel.pokemon.pokemon?.name"
                class="w-16 h-16 object-contain"
                style="image-rendering: pixelated;"
              />
              <div>
                <h4 class="font-bold text-lg">{{ listingToCancel.pokemon.pokemon?.name }}</h4>
                <p class="text-sm text-base-content/70">Niveau {{ listingToCancel.pokemon.level }}</p>
                <p class="text-sm font-bold text-warning">{{ formatPrice(listingToCancel.price) }}</p>
              </div>
            </div>

            <p class="text-base-content">
              Êtes-vous sûr de vouloir retirer ce Pokémon de la vente ?
            </p>

            <div class="flex gap-3">
              <Button
                @click="showCancelModal = false"
                variant="outline"
                size="lg"
                class="flex-1"
                :disabled="processing"
              >
                Annuler
              </Button>

              <Button
                @click="confirmCancel"
                variant="outline"
                size="lg"
                class="flex-1 !border-error !text-error hover:!bg-error hover:!text-white"
                :disabled="processing"
              >
                {{ processing ? '🔄 En cours...' : '🗑️ Confirmer' }}
              </Button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import type { PokedexEntry, MarketplaceListing } from '@/types/marketplace';
import { useSelling } from '@/composables/useSelling';
import { formatPrice, parseTypes, getPriceRangeForRarity } from '@/utils/marketplace';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Select from '@/Components/UI/Select.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Modal from '@/Components/UI/Modal.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import Spinner from '@/Components/UI/Spinner.vue';
import MyListingsSection from '@/Components/Game/MyListingsSection.vue';
import SelectedPokemonCard from '@/Components/Cards/SelectedPokemonCard.vue';
import PokemonCard from '@/Components/Cards/PokemonCard.vue';

interface Props {
  userPokemons: PokedexEntry[];
  myListings: MarketplaceListing[];
}

const props = defineProps<Props>();

const {
  form,
  selectedPokemon,
  showCreateModal,
  showCancelModal,
  listingToCancel,
  priceRange,
  processing,
  availablePokemons,
  canSubmit,
  selectPokemon,
  resetForm,
  listPokemon,
  initializeData,
  myListings,
  showCancelConfirm,
  confirmCancel
} = useSelling();

onMounted(() => {
  initializeData({
    userPokemons: props.userPokemons,
    myListings: props.myListings
  });
});

const showPokemonModal = ref(false);
</script>

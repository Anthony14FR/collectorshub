<template>

  <Head title="Vendre un Pokémon" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-4 mb-4 px-4">
        <div class="text-center">
          <h1
            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            VENDRE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            METTRE EN VENTE
          </p>
        </div>
      </div>

      <div v-if="($page.props as any).flash?.success" class="mb-2 px-4">
        <Alert type="success" :message="($page.props as any).flash.success" />
      </div>
      <div v-if="($page.props as any).errors?.message" class="mb-2 px-4">
        <Alert type="error" :message="($page.props as any).errors.message" />
      </div>

      <div class="flex flex-col lg:flex-row gap-4 p-4 lg:p-8">
        <div class="w-full lg:w-72 order-1 lg:order-1">
          <Button @click="router.visit('/marketplace')" variant="secondary" size="sm" class="w-full mb-4">
            ← Retour marketplace
          </Button>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg"><Plus :size="20" /></span>
                NOUVELLE ANNONCE
              </h3>
            </div>

            <div class="p-3">
              <div v-if="!selectedPokemon" @click="showCreateModal = true"
                   class="bg-base-200/30 backdrop-blur-sm rounded-lg p-8 border-2 border-dashed border-base-300/50 hover:border-primary/50 transition-all duration-200 cursor-pointer group text-center">
                <div
                  class="text-6xl text-base-content/30 group-hover:text-primary/50 transition-colors duration-200 mb-2">
                  +</div>
                <p class="text-sm text-base-content/70 group-hover:text-base-content transition-colors duration-200">
                  Cliquer pour ajouter
                </p>
              </div>

              <div v-else class="space-y-4">
                <SelectedPokemonCard :pokemon="selectedPokemon" @close="resetForm" />

                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-warning/20 p-4">
                  <Input v-model="form.price" type="number" label="Prix de vente" placeholder="Minimum 1 ₽"
                         variant="bordered" size="sm"
                         :helper-text="($page.props as any).errors.price || 'Le prix minimum est de 1 ₽'"
                         :state="($page.props as any).errors.price ? 'error' : 'default'" />
                </div>

                <Button @click="listPokemon" :disabled="!canSubmit || processing" variant="primary" size="md"
                        class="w-full bg-gradient-to-r from-success to-success/80 hover:from-success/90 hover:to-success/70 border-success/30 shadow-lg shadow-success/20">
                  <RotateCcw v-if="processing" :size="16" class="inline" />
                  <Coins v-else :size="16" class="inline" /> Mettre en vente
                </Button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex-1 order-2 lg:order-2 flex justify-center">
          <div class="w-full max-w-80">
            <div class="bg-base-100/60 backdrop-blur-lg rounded-2xl border border-warning/30 p-6 shadow-xl">
              <div class="text-center mb-6">
                <div
                  class="w-20 h-20 bg-gradient-to-br from-warning/30 to-warning/10 rounded-full flex items-center justify-center mb-3 mx-auto border-2 border-warning/40">
                  <span class="text-4xl"><Coins :size="20" /></span>
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
            </div>
          </div>
        </div>

        <div class="w-full lg:w-72 order-3 lg:order-3 space-y-4">
          <MyListingsSection :listings="myListings" :show-cancel-button="true" @cancel-listing="showCancelConfirm" />
        </div>
      </div>

      <Modal :show="showCreateModal" @close="showCreateModal = false" max-width="6xl">
        <template #header>
          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 bg-gradient-to-br from-success/20 to-success/40 rounded-lg flex items-center justify-center">
              <span class="text-lg"><Package :size="20" /></span>
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
            <p class="text-2xl mb-2"><Frown :size="20" /></p>
            <p class="text-lg font-bold mb-2">Aucun Pokémon disponible</p>
            <p class="text-base-content/70 mb-4">Tous vos Pokémon sont dans votre équipe ou déjà en vente.</p>
            <Button @click="showCreateModal = false" variant="secondary" size="md">
              Fermer
            </Button>
          </div>

          <div v-else>
            <div class="mb-6">
              <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                Choisir un Pokémon
              </h3>
              <p class="text-base-content/70 text-sm mt-1">Sélectionnez un Pokémon de votre inventaire à mettre en vente
              </p>
            </div>

            <div class="max-h-[500px] overflow-y-auto">
              <div class="p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  <div v-for="pokemon in availablePokemons" :key="pokemon.id" @click="selectPokemon(pokemon)"
                       class="cursor-pointer group">
                    <PokedexModalCard :displayPokemon="{
                      pokedexInfo: pokemon,
                      pokemon: pokemon.pokemon,
                      owned: true,
                      count: 1
                    }" />
                    <div
                      class="mt-2 text-xs text-center text-primary opacity-0 group-hover:opacity-100 transition-opacity">
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
            <div
              class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
              <span class="text-lg"><Package :size="20" /></span>
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
                  :alt="selectedPokemon.pokemon?.name" class="w-48 h-48 object-contain"
                  style="image-rendering: pixelated;" />
              </div>
            </div>

            <div class="space-y-6">
              <div class="text-center">
                <RarityBadge v-if="selectedPokemon.pokemon?.rarity" :rarity="selectedPokemon.pokemon.rarity"
                             size="md" />
              </div>

              <div class="flex justify-center gap-2">
                <PokemonTypeBadge v-for="type in parseTypes(selectedPokemon.pokemon?.types)" :key="type" :type="type"
                                  size="sm" />
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
                  <p class="text-2xl font-bold text-primary">{{ selectedPokemon.pokemon?.speed }}</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">PC</span>
                  <p class="text-2xl font-bold text-success">{{ selectedPokemon.cp }}</p>
                </div>
                <div class="bg-base-200/50 rounded-xl p-4 text-center">
                  <span class="text-base-content/70 text-sm">Étoiles</span>
                  <p class="text-2xl font-bold text-neutral">{{ selectedPokemon.star || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 text-center">
            <Button @click="showPokemonModal = false" variant="primary" size="md">
              Fermer
            </Button>
          </div>
        </template>
      </Modal>

      <Modal :show="showCancelModal" @close="closeModals" max-width="lg">
        <template #header>
          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 bg-gradient-to-br from-error/20 to-error/40 rounded-lg flex items-center justify-center">
              <span class="text-lg"><Trash2 :size="20" /></span>
            </div>
            <div class="flex flex-col">
              <h3 class="text-xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent">
                Annuler l'annonce
              </h3>
              <div class="mt-1">
                <span class="text-sm font-semibold text-error">Confirmer l'annulation</span>
              </div>
            </div>
          </div>
        </template>

        <template #default>
          <div v-if="listingToCancel" class="text-center">
            <p class="text-lg mb-4">
              Êtes-vous sûr de vouloir annuler l'annonce pour
              <span class="font-bold text-warning">{{ listingToCancel.pokemon.pokemon?.name }}</span>
              (prix : <span class="font-bold text-warning">{{ formatPrice(listingToCancel.price) }}</span>) ?
            </p>
            <div class="flex justify-center gap-4">
              <Button @click="closeModals" variant="secondary" size="md">
                Non, garder
              </Button>
              <Button @click="confirmCancel" :disabled="processing" variant="secondary" size="md">
                <RotateCcw v-if="processing" :size="16" class="inline" />
                <Check v-else :size="16" class="inline" /> Oui, annuler
              </Button>
            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import SelectedPokemonCard from '@/Components/Cards/SelectedPokemonCard.vue';
import MyListingsSection from '@/Components/Game/MyListingsSection.vue';
import PokedexModalCard from '@/Components/Pokedex/PokedexModalCard.vue';
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Modal from '@/Components/UI/Modal.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import {
  formatPrice,
  parseTypes
} from '@/utils/marketplace';
import { Head, router } from '@inertiajs/vue3';
import { Check, Coins, Package, Plus, RotateCcw, Trash2 } from 'lucide-vue-next';

import { useSelling } from '@/composables/useSelling';

const props = defineProps<{
  userPokemons: any[],
  myListings: any[]
}>();

const {
  myListings,
  selectedPokemon,
  showCreateModal,
  showPokemonModal,
  showCancelModal,
  listingToCancel,
  processing,
  form,
  availablePokemons,
  canSubmit,
  initializeData,
  selectPokemon,
  resetForm,
  showCancelConfirm,
  closeModals,
  listPokemon,
  confirmCancel
} = useSelling();

initializeData(props);

</script>
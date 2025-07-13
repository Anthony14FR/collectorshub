<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import PokemonSelectionModal from '@/Components/PokemonUpgrade/PokemonSelectionModal.vue';
import UpgradeSlot from '@/Components/PokemonUpgrade/UpgradeSlot.vue';
import type { PokedexEntry } from '@/types/user';
import type { UpgradeData, UpgradeRequirement } from '@/types/upgrade';

declare function route(name: string, params?: Record<string, any>): string;

interface Props {
  pokemon: PokedexEntry;
  show: boolean;
}

const { pokemon, show } = defineProps<Props>();
const emit = defineEmits<{
  close: [];
  success: [pokemon: PokedexEntry];
  error: [message: string];
}>();

const upgradeData = ref<UpgradeData | null>(null);
const loading = ref(false);
const selectedMaterials = ref<PokedexEntry[]>([]);
const showSelectionModal = ref(false);
const currentSlotType = ref<'main' | 'secondary'>('main');
const currentSlotIndex = ref(0);
const showSuccessModal = ref(false);
const selectedPokemon = ref<PokedexEntry | null>(null);

const materialSlots = computed(() => {
  if (!upgradeData.value) return [];
  
  const slots = [];
  const requirements = upgradeData.value.requirements;
  
  for (let i = 0; i < requirements.main_requirement.quantity; i++) {
    const slotIndex = i;
    const selectedPokemon = selectedMaterials.value[slotIndex];
    
    slots.push({
      type: 'main' as const,
      index: i,
      requirement: requirements.main_requirement,
      selected: selectedPokemon
    });
  }
  
  if (requirements.secondary_requirement) {
    const mainSlotsCount = requirements.main_requirement.quantity;
    for (let i = 0; i < requirements.secondary_requirement.quantity; i++) {
      const slotIndex = mainSlotsCount + i;
      const selectedPokemon = selectedMaterials.value[slotIndex];
      
      slots.push({
        type: 'secondary' as const,
        index: i,
        requirement: requirements.secondary_requirement,
        selected: selectedPokemon
      });
    }
  }
  
  return slots;
});

const canUpgrade = computed(() => {
  const validMaterials = selectedMaterials.value.filter(m => m !== null && m !== undefined);
  return upgradeData.value?.canUpgrade && 
         validMaterials.length === getTotalRequiredMaterials();
});

const getTotalRequiredMaterials = (): number => {
  if (!upgradeData.value) return 0;
  
  let total = upgradeData.value.requirements.main_requirement.quantity;
  if (upgradeData.value.requirements.secondary_requirement) {
    total += upgradeData.value.requirements.secondary_requirement.quantity;
  }
  return total;
};

const loadUpgradeData = async () => {
  loading.value = true;
  try {
    const response = await fetch(route('pokemon-upgrade.requirements', { pokedexId: pokemon.id }));
    upgradeData.value = await response.json();
  } catch (error) {
    console.error('Erreur lors du chargement des données d\'upgrade:', error);
  } finally {
    loading.value = false;
  }
};

const openSlotSelection = (slotType: 'main' | 'secondary', slotIndex: number) => {
  currentSlotType.value = slotType;
  currentSlotIndex.value = slotIndex;
  showSelectionModal.value = true;
};

const onPokemonSelected = (selectedPokemon: PokedexEntry) => {
  const slotIndex = currentSlotType.value === 'main' 
    ? currentSlotIndex.value 
    : currentSlotIndex.value + (upgradeData.value?.requirements.main_requirement.quantity || 0);
  
  const totalSlots = getTotalRequiredMaterials();
  while (selectedMaterials.value.length < totalSlots) {
    selectedMaterials.value.push(null as any);
  }
  
  selectedMaterials.value[slotIndex] = selectedPokemon;
  
  showSelectionModal.value = false;
};

const removeMaterial = (material: PokedexEntry) => {
  const index = selectedMaterials.value.findIndex(m => m.id === material.id);
  if (index > -1) {
    selectedMaterials.value.splice(index, 1);
  }
};

const getAvailableForSlot = (slotType: 'main' | 'secondary'): PokedexEntry[] => {
  if (!upgradeData.value) return [];
  
  const availableKey = slotType === 'main' ? 'main_requirement' : 'secondary_requirement';
  const available = upgradeData.value.availablePokemons[availableKey] || [];
  
  const alreadySelectedIds = selectedMaterials.value
    .filter(m => m !== null && m !== undefined)
    .map(m => m.id);
  return available.filter(p => !alreadySelectedIds.includes(p.id));
};

const getCurrentRequirement = () => {
  if (!upgradeData.value) return null;
  
  return currentSlotType.value === 'main' 
    ? upgradeData.value.requirements.main_requirement
    : upgradeData.value.requirements.secondary_requirement;
};

watch(() => pokemon.id, () => {
  selectedMaterials.value = [];
  upgradeData.value = null;
  if (show) {
    loadUpgradeData();
  }
});

watch(() => show, (newShow) => {
  if (newShow) {
    selectedMaterials.value = [];
    upgradeData.value = null;
    loadUpgradeData();
  }
});

watch(() => pokemon.star, () => {
  selectedMaterials.value = [];
  upgradeData.value = null;
  if (show) {
    loadUpgradeData();
  }
});

const submitUpgrade = () => {
  loading.value = true;
  
  router.post(route('pokemon-upgrade.upgrade'), {
    pokedex_id: pokemon.id,
    materials: selectedMaterials.value.filter(m => m !== null && m !== undefined).map(m => m.id)
  }, {
    preserveScroll: true,
    onSuccess: () => {
      loading.value = false;
      
      const upgradedPokemon = {
        ...pokemon,
        star: pokemon.star + 1
      };
      
      selectedMaterials.value = [];
      emit('close');
      
      emit('success', upgradedPokemon);
    },
    onError: (errors: Record<string, string>) => {
      loading.value = false;
      console.error('Erreur lors de l\'amélioration:', errors);
      
      const errorMessage = Object.values(errors).join(', ') || 'Une erreur est survenue lors de l\'amélioration';
      emit('error', errorMessage);
    },
    onFinish: () => {
      loading.value = false;
    }
  });
};

onMounted(() => {
  if (show) {
    loadUpgradeData();
  }
});
</script>

<template>
  <Modal :show="show" @close="emit('close')" max-width="6xl">
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
        <span class="text-xl sm:text-2xl">⭐</span>
        <div class="flex-1">
          <h3 class="text-base sm:text-lg font-bold">Améliorer {{ pokemon.pokemon?.name }}</h3>
          <div class="flex items-center gap-2 mt-1">
            <StarsBadge :stars="pokemon.star" />
            <span class="text-lg sm:text-xl">→</span>
            <StarsBadge :stars="pokemon.star + 1" />
          </div>
          <p v-if="pokemon.pokemon?.is_shiny" class="text-xs sm:text-sm text-yellow-500 flex items-center gap-1 mt-1">
            ✨ Pokémon Shiny - Seuls les Shiny peuvent être utilisés
          </p>
        </div>
      </div>
    </template>

    <div v-if="loading" class="flex justify-center py-6 sm:py-8">
      <div class="loading loading-spinner loading-lg"></div>
    </div>

    <div v-else-if="upgradeData" class="space-y-4 sm:space-y-6">
      <div class="text-center">
        <h4 class="font-bold mb-2 sm:mb-3 text-sm sm:text-base">Pokémon à améliorer :</h4>
        <div class="flex justify-center">
          <div class="relative">
            <img
              :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon?.id}.gif`"
              :alt="pokemon.pokemon?.name"
              class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 object-contain"
              style="image-rendering: pixelated;"
            />
            <div class="absolute -top-1 sm:-top-2 -right-1 sm:-right-2">
              <StarsBadge :stars="pokemon.star" />
            </div>
            <div v-if="pokemon.pokemon?.is_shiny" class="absolute -top-1 sm:-top-2 -left-1 sm:-left-2">
              <span class="text-yellow-400 text-lg sm:text-xl">✨</span>
            </div>
          </div>
        </div>
        <p class="text-xs sm:text-sm font-medium mt-2">{{ pokemon.nickname || pokemon.pokemon?.name }} (Niv. {{ pokemon.level }})</p>
      </div>

      <div>
        <h4 class="font-bold mb-2 sm:mb-3 text-center text-sm sm:text-base">Matériaux requis :</h4>
        <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-2 sm:gap-3 md:gap-4 justify-items-center">
          <UpgradeSlot
            v-for="slot in materialSlots"
            :key="`${slot.type}-${slot.index}`"
            :requirement="slot.requirement"
            :selected-pokemon="slot.selected"
            :pokemon-to-upgrade="pokemon"
            @click="openSlotSelection(slot.type, slot.index)"
            @remove="removeMaterial"
          />
        </div>
      </div>

      <div class="bg-base-200/50 rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
          <span class="font-medium text-sm sm:text-base">Matériaux sélectionnés :</span>
          <span class="text-base sm:text-lg font-bold">{{ selectedMaterials.filter(m => m !== null && m !== undefined).length }} / {{ getTotalRequiredMaterials() }}</span>
        </div>
        <div v-if="selectedMaterials.filter(m => m !== null && m !== undefined).length > 0" class="mt-2 text-xs sm:text-sm text-base-content/70">
          <span v-for="(material, index) in selectedMaterials.filter(m => m !== null && m !== undefined)" :key="material.id">
            {{ material.pokemon?.name }} {{ material.star }}★<span v-if="index < selectedMaterials.filter(m => m !== null && m !== undefined).length - 1">, </span>
          </span>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
        <Button @click="emit('close')" variant="outline" :disabled="loading" class="w-full sm:w-auto order-2 sm:order-1">
          Annuler
        </Button>
        <Button 
          @click="submitUpgrade" 
          variant="primary"
          :disabled="!canUpgrade || loading"
          :loading="loading"
          class="w-full sm:w-auto order-1 sm:order-2"
        >
          <span v-if="!loading" class="flex items-center justify-center gap-1">
            <span>⭐ Améliorer</span>
            <span class="hidden sm:inline">({{ selectedMaterials.filter(m => m !== null && m !== undefined).length }}/{{ getTotalRequiredMaterials() }})</span>
          </span>
          <span v-else>Amélioration en cours...</span>
        </Button>
      </div>
    </div>

    <PokemonSelectionModal
      :show="showSelectionModal"
      :available-pokemons="getAvailableForSlot(currentSlotType)"
      :requirement="getCurrentRequirement()"
      :pokemon-to-upgrade="pokemon"
      @close="showSelectionModal = false"
      @select="onPokemonSelected"
    />
  </Modal>
</template>

<style scoped>
@media (max-width: 475px) {
  .xs\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>
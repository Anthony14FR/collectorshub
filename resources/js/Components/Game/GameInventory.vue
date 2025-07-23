<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { Inventory } from '@/types/inventory';
import { Coins, Package } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
  inventory?: Inventory[];
  cash?: number;
}

const { inventory = [], cash = 0 } = defineProps<Props>();
const isModalOpen = ref(false);

const filteredInventory = computed(() => {
  return inventory.filter(item => item.item.type !== 'avatar');
});

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};
</script>

<template>
  <div class="relative">
    <Button
      variant="secondary"
      size="md"
      @click="isModalOpen = true"
      class="w-full"
    >
      <Package :size="24" class="mr-2" /> SAC
    </Button>

    <Modal :show="isModalOpen" @close="isModalOpen = false" max-width="4xl" fixed-height>
      <template #header>
        <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
            <Package :size="24" class="text-primary" />
          </div>
          <div class="flex-1 text-center sm:text-left">
            <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              Mon Inventaire
            </h3>
            <p class="text-sm text-base-content/70">{{ filteredInventory.length }} objets</p>
          </div>
        </div>
      </template>
      <template #default>
        <div class="bg-gradient-to-r from-warning/10 to-warning/5 rounded-xl p-4 mb-4 border border-warning/20">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
                <Coins :size="24" class="text-warning" />
              </div>
              <div>
                <h4 class="font-bold text-base-content">Porte-monnaie</h4>
                <p class="text-xs text-base-content/70">Votre solde disponible</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-warning">{{ formatPrice(cash) }}</div>
            </div>
          </div>
        </div>

        <div v-if="filteredInventory.length === 0" class="text-center py-8">
          <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-base-200/50 to-base-300/50 rounded-full flex items-center justify-center">
            <Package :size="40" class="opacity-50" />
          </div>
          <p class="text-base-content/70 mb-2">Votre sac est vide</p>
          <p class="text-sm text-base-content/50">Explorez pour trouver des objets !</p>
        </div>

        <div v-else>
          <h5 class="font-bold text-base-content mb-3 flex items-center gap-2">
            <Package :size="24" />
            Objets
          </h5>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 max-h-96 overflow-y-auto p-2">
            <div
              v-for="item in filteredInventory"
              :key="item.id"
              class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 border border-success/30 hover:border-success/50 shadow-lg shadow-success/10 hover:shadow-xl hover:shadow-success/20 transition-all duration-300 hover:scale-105 cursor-pointer"
            >
              <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-success/20 to-primary/20 rounded-lg flex items-center justify-center">
                  <img :src="item.item.image" :alt="item.item.name" class="w-8 h-8" />
                </div>
                <h4 class="font-bold text-sm mb-1">{{ item.item.name }}</h4>
                <p class="text-xs opacity-70 mb-2">{{ item.item.description }}</p>
                <div class="flex justify-between items-center text-xs">
                  <span class="bg-success/20 px-2 py-1 rounded-full">
                    x{{ item.quantity }}
                  </span>
                  <span class="text-success font-semibold flex items-center gap-1">
                    {{ item.item.price }}
                    <Coins :size="12" class="text-success" />
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

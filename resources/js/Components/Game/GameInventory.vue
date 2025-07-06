<script setup lang="ts">
import { ref } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import type { Inventory } from '@/types/inventory';

interface Props {
    inventory?: Inventory[];
}

const { inventory = [] } = defineProps<Props>();
const isModalOpen = ref(false);
</script>

<template>
    <div class="relative">
        <!-- Bouton Sac -->
        <Button 
            variant="primary" 
            icon="collection" 
            size="md"
            @click="isModalOpen = true"
            class="w-full"
        >
            SAC
        </Button>

        <!-- Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-success/20 to-primary/20 rounded-lg flex items-center justify-center">
                        <span class="text-lg">🎒</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-xl font-bold bg-gradient-to-r from-success to-primary bg-clip-text text-transparent">
                            Mon Inventaire
                        </h3>
                        <div class="mt-1">
                            <span class="text-sm font-semibold text-success">{{ inventory.length }} objets</span>
                        </div>
                    </div>
                </div>
            </template>
            <template #default>
                <div v-if="inventory.length === 0" class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-base-200/50 to-base-300/50 rounded-full flex items-center justify-center">
                        <span class="text-2xl opacity-50">🎒</span>
                    </div>
                    <p class="text-base-content/70 mb-2">Votre sac est vide</p>
                    <p class="text-sm text-base-content/50">Explorez pour trouver des objets !</p>
                </div>
                
                <div v-else class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-96 overflow-y-auto p-2">
                    <div 
                        v-for="item in inventory" 
                        :key="item.id" 
                        class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 border border-success/30 hover:border-success/50 shadow-lg shadow-success/10 hover:shadow-xl hover:shadow-success/20 transition-all duration-300 hover:scale-105 cursor-pointer"
                    >
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-success/20 to-primary/20 rounded-lg flex items-center justify-center">
                                <span class="text-lg">📦</span>
                            </div>
                            <h4 class="font-bold text-sm mb-1">{{ item.item.name }}</h4>
                            <p class="text-xs opacity-70 mb-2">{{ item.item.description }}</p>
                            <div class="flex justify-between items-center text-xs">
                                <span class="bg-success/20 px-2 py-1 rounded-full">
                                    x{{ item.quantity }}
                                </span>
                                <span class="text-success font-semibold">
                                    {{ item.item.price }}💰
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Modal>
    </div>
</template> 
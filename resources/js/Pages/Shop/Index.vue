<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import Select from '@/Components/UI/Select.vue';

const props = defineProps({
    user: Object,
    items: Array,
    inventory: Array,
    errors: Object,
});

const selectedItem = ref(null);
const quantity = ref(1);
const showModal = ref(false);
const showInventoryModal = ref(false);
const selectedTypeFilter = ref('');

const form = useForm({
    item_id: null,
    quantity: 1,
});

const typeOptions = computed(() => {
    const types = [...new Set(props.items.map(item => item.type))];
    return [
        { value: '', label: 'Tous les types' },
        ...types.map(type => ({ value: type, label: type.charAt(0).toUpperCase() + type.slice(1) }))
    ];
});

const filteredItems = computed(() => {
    if (!selectedTypeFilter.value) {
        return props.items;
    }
    return props.items.filter(item => item.type === selectedTypeFilter.value);
});

const getInventoryQuantity = (itemId) => {
    const inventoryItem = props.inventory.find(inv => inv.item_id === itemId);
    return inventoryItem ? inventoryItem.quantity : 0;
};

const totalCost = computed(() => {
    if (!selectedItem.value) return 0;
    return selectedItem.value.price * quantity.value;
});

const canAfford = computed(() => {
    return props.user.cash >= totalCost.value;
});

const openBuyModal = (item) => {
    selectedItem.value = item;
    quantity.value = 1;
    showModal.value = true;
};

const buyItem = () => {
    form.item_id = selectedItem.value.id;
    form.quantity = quantity.value;
    
    form.post(route('shop.buy'), {
        onSuccess: () => {
            showModal.value = false;
            selectedItem.value = null;
            quantity.value = 1;
        },
    });
};

const formatPrice = (price) => {
    return price.toLocaleString();
};

const getItemImage = (item) => {
    if (item.image) {
        return item.image;
    }
    return '/images/items/default-item.png';
};

const getRarityColor = (rarity) => {
    switch (rarity) {
        case 'normal': return 'text-gray-600';
        case 'rare': return 'text-blue-400';
        case 'epic': return 'text-purple-400';
        case 'legendary': return 'text-yellow-400';
        default: return 'text-gray-600';
    }
};
</script>

<template>
    <Head title="Boutique" />

    <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
        <BackgroundEffects />

        <div class="relative z-10 h-screen w-screen overflow-hidden">
            <div class="flex justify-center pt-4 mb-4">
                <div class="text-center">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
                        BOUTIQUE
                    </h1>
                    <p class="text-xs text-base-content/70 uppercase tracking-wider">
                        ITEMS & OBJETS SPÉCIAUX
                    </p>
                </div>
            </div>

            <div class="absolute left-8 top-20 w-64">
                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
                    <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                        <h3 class="text-sm font-bold tracking-wider">NAVIGATION</h3>
                    </div>
                    <div class="p-3">
                        <Link href="/me">
                            <Button variant="secondary" size="sm" class="w-full mb-2">
                                Retour au profil
                            </Button>
                        </Link>
                        <Button
                            @click="showInventoryModal = true"
                            variant="outline"
                            size="sm"
                            class="w-full"
                        >
                            Mon inventaire
                        </Button>
                    </div>
                </div>

                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                    <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                        <h3 class="text-sm font-bold tracking-wider">FILTRES</h3>
                    </div>
                    <div class="p-3">
                        <Select
                            v-model="selectedTypeFilter"
                            :options="typeOptions"
                            label="Type d'item"
                            variant="default"
                            size="sm"
                        />
                    </div>
                </div>
            </div>

            <div class="absolute right-8 top-20 w-64">
                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                    <div class="bg-gradient-to-r from-success/10 to-success/5 px-3 py-2 border-b border-success/20">
                        <h4 class="text-xs font-bold tracking-wider">PORTE-MONNAIE</h4>
                    </div>
                    <div class="p-3 text-center">
                        <div class="text-2xl font-bold text-success">{{ formatPrice(user.cash) }}</div>
                        <div class="text-xs text-base-content/70">Votre solde</div>
                    </div>
                </div>
            </div>

            <div class="absolute top-32 left-1/2 -translate-x-1/2 w-[800px] max-h-[600px]">
                <div v-if="$page.props.flash?.success" class="mb-4">
                    <Alert type="success" :message="$page.props.flash.success" />
                </div>
                <div v-if="$page.props.errors?.message" class="mb-4">
                    <Alert type="error" :message="$page.props.errors.message" />
                </div>

                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
                    <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                        <h3 class="text-sm font-bold tracking-wider">
                            CATALOGUE D'ITEMS
                        </h3>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="item in filteredItems" :key="item.id" 
                                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 group">
                                
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center p-1">
                                        <img :src="getItemImage(item)" :alt="item.name" class="w-full h-full object-contain" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-sm mb-1">{{ item.name }}</h4>
                                        <div class="flex items-center gap-2">
                                            <span :class="getRarityColor(item.rarity)" class="text-xs font-semibold capitalize">
                                                {{ item.rarity }}
                                            </span>
                                            <span class="text-xs text-base-content/50">{{ item.type }}</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-base-content/70 mb-3 line-clamp-2">
                                    {{ item.description }}
                                </p>

                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-xs text-base-content/60">
                                        Dans mon inventaire: <span class="font-semibold">{{ getInventoryQuantity(item.id) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-warning text-sm">₽</span>
                                        <span class="font-bold text-warning">{{ formatPrice(item.price) }}</span>
                                    </div>
                                </div>

                                <Button
                                    @click="openBuyModal(item)"
                                    variant="primary"
                                    size="sm"
                                    class="w-full"
                                >
                                    Acheter
                                </Button>
                            </div>
                        </div>

                        <div v-if="filteredItems.length === 0" class="text-center py-8">
                            <p class="text-2xl mb-2">📦</p>
                            <p class="text-sm mb-1">Aucun item trouvé</p>
                            <p class="opacity-70 text-xs">Essayez un autre filtre</p>
                        </div>
                    </div>

                    <div class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
                        <div class="text-xs text-center text-base-content/70">
                            {{ filteredItems.length }} item{{ filteredItems.length > 1 ? 's' : '' }} affiché{{ filteredItems.length > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
            </div>

            <Modal :show="showModal" @close="showModal = false">
                <template #header>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center p-1">
                            <img :src="getItemImage(selectedItem)" :alt="selectedItem?.name" class="w-full h-full object-contain" />
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                                Acheter {{ selectedItem?.name }}
                            </h3>
                            <div class="mt-1">
                                <span class="text-sm font-semibold text-primary">{{ formatPrice(selectedItem?.price) }} ₽</span>
                            </div>
                        </div>
                    </div>
                </template>

                <template #default>
                    <div v-if="selectedItem" class="space-y-4">
                        <div class="bg-base-200/30 rounded-xl p-4">
                            <p class="text-sm text-base-content/80 mb-3">{{ selectedItem.description }}</p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-base-content/70">Quantité:</span>
                                <div class="flex items-center gap-2">
                                    <Button
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        variant="ghost"
                                        size="xs"
                                        :disabled="quantity <= 1"
                                    >
                                        -
                                    </Button>
                                    <span class="px-3 py-1 bg-base-100 rounded text-sm font-bold">{{ quantity }}</span>
                                    <Button
                                        @click="quantity = Math.min(99, quantity + 1)"
                                        variant="ghost"
                                        size="xs"
                                        :disabled="quantity >= 99"
                                    >
                                        +
                                    </Button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-warning">{{ formatPrice(totalCost) }} ₽</span>
                            </div>
                        </div>

                        <div v-if="!canAfford" class="bg-error/10 text-error p-3 rounded-xl border border-error/20">
                            <div class="flex items-center gap-2">
                                <span>Solde insuffisant</span>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <Button
                                @click="showModal = false"
                                variant="outline"
                                size="lg"
                                class="flex-1"
                            >
                                Annuler
                            </Button>

                            <Button
                                @click="buyItem"
                                variant="primary"
                                size="lg"
                                class="flex-1"
                                :disabled="!canAfford"
                            >
                                {{ canAfford ? 'Confirmer' : 'Impossible' }}
                            </Button>
                        </div>
                    </div>
                </template>
            </Modal>

            <Modal :show="showInventoryModal" @close="showInventoryModal = false" max-width="4xl">
                <template #header>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-success/20 to-success/40 rounded-lg flex items-center justify-center">
                            <span class="text-lg">📦</span>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent">
                                Mon Inventaire
                            </h3>
                            <div class="mt-1">
                                <span class="text-sm font-semibold text-success">{{ inventory.length }} types d'items</span>
                            </div>
                        </div>
                    </div>
                </template>

                <template #default>
                    <div v-if="inventory.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto p-2">
                        <div v-for="inventoryItem in inventory" :key="inventoryItem.id" 
                            class="bg-base-200/30 backdrop-blur-sm rounded-xl p-3 border border-base-300/20">
                            
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center p-1">
                                    <img :src="getItemImage(inventoryItem.item)" :alt="inventoryItem.item.name" class="w-full h-full object-contain" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-xs">{{ inventoryItem.item.name }}</h4>
                                    <span :class="getRarityColor(inventoryItem.item.rarity)" class="text-xs font-semibold capitalize">
                                        {{ inventoryItem.item.rarity }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-center">
                                <div class="text-lg font-bold text-success">{{ inventoryItem.quantity }}</div>
                                <div class="text-xs text-base-content/70">exemplaires</div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-2xl mb-2">📦</p>
                        <p class="text-sm mb-1">Inventaire vide</p>
                        <p class="opacity-70 text-xs">Achetez des items pour les voir ici</p>
                    </div>
                </template>
            </Modal>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style> 
<script setup>
import { ref, computed, watch } from 'vue';
import ShopItemCard from '@/Components/Cards/ShopItemCard.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    inventory: {
        type: Array,
        default: () => []
    },
    userAvatars: {
        type: Array,
        default: () => []
    },
    activeTab: {
        type: String,
        default: 'items'
    }
});

const emit = defineEmits(['buy', 'changeTab']);

const currentPage = ref(1);
const itemsPerPage = 9;

watch(() => props.activeTab, () => {
    currentPage.value = 1;
});

const filteredItems = computed(() => {
    let result = props.items;
    
    if (props.activeTab === 'items') {
        result = result.filter(item => item.type !== 'avatar' && item.type !== 'ball');
    } else if (props.activeTab === 'balls') {
        result = result.filter(item => item.type === 'ball');
    } else if (props.activeTab === 'avatars') {
        result = result.filter(item => item.type === 'avatar');
    }
    
    return result;
});

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const items = filteredItems.value.slice(start, end);
    
    const paddedItems = [...items];
    while (paddedItems.length < 9) {
        paddedItems.push({ id: `placeholder-${paddedItems.length}`, isPlaceholder: true });
    }
    
    return paddedItems;
});

const totalPages = computed(() => {
    return Math.ceil(filteredItems.value.length / itemsPerPage);
});

const changePage = (page) => {
    currentPage.value = page;
};

const paginationItems = computed(() => {
    const maxVisiblePages = 7;
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    
    if (total <= maxVisiblePages) {
        for (let i = 1; i <= total; i++) {
            pages.push({ type: 'page', value: i });
        }
    } else {
        let startPage = Math.max(1, current - 3);
        let endPage = Math.min(total, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        if (startPage > 1) {
            pages.push({ type: 'page', value: 1 });
            if (startPage > 2) {
                pages.push({ type: 'ellipsis' });
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i !== 1 && i !== total) {
                pages.push({ type: 'page', value: i });
            }
        }
        
        if (endPage < total) {
            if (endPage < total - 1) {
                pages.push({ type: 'ellipsis' });
            }
            pages.push({ type: 'page', value: total });
        }
        
        if (!pages.some(p => p.value === 1) && startPage === 1) {
            pages.unshift({ type: 'page', value: 1 });
        }
        if (!pages.some(p => p.value === total) && endPage === total) {
            pages.push({ type: 'page', value: total });
        }
    }
    
    return pages;
});
</script>

<template>
    <div class="flex flex-col h-[700px]">
        <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20 flex gap-4">
            <button
                @click="emit('changeTab', 'items')"
                :class="activeTab === 'items' ? 'font-bold text-warning underline' : 'text-base-content/70'"
            >
                Objets
            </button>
            <button
                @click="emit('changeTab', 'balls')"
                :class="activeTab === 'balls' ? 'font-bold text-warning underline' : 'text-base-content/70'"
            >
                Balls
            </button>
            <button
                @click="emit('changeTab', 'avatars')"
                :class="activeTab === 'avatars' ? 'font-bold text-warning underline' : 'text-base-content/70'"
            >
                Avatars
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template v-for="item in paginatedItems" :key="item.id">
                    <ShopItemCard
                        v-if="!item.isPlaceholder"
                        :item="item"
                        :userInventory="inventory"
                        :userAvatars="userAvatars"
                        @buy="emit('buy', $event)"
                    />
                    <div v-else class="bg-base-200/30 rounded-xl border border-base-300/20 h-full min-h-[200px]"></div>
                </template>
            </div>
        </div>

        <div v-if="totalPages > 1" class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-4 py-3 border-t border-warning/20">
            <div class="flex justify-center items-center gap-1">
                <Button
                    @click="changePage(1)"
                    :disabled="currentPage === 1"
                    variant="ghost"
                    size="sm"
                    title="Première page"
                >
                    ««
                </Button>
                <Button
                    @click="changePage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    variant="ghost"
                    size="sm"
                    title="Page précédente"
                >
                    ‹
                </Button>

                <template v-for="(item, index) in paginationItems" :key="index">
                    <Button
                        v-if="item.type === 'page'"
                        @click="changePage(item.value)"
                        :variant="currentPage === item.value ? 'primary' : 'ghost'"
                        size="sm"
                        class="min-w-[2.5rem]"
                    >
                        {{ item.value }}
                    </Button>
                    <span v-else class="px-2 text-base-content/50">…</span>
                </template>

                <Button
                    @click="changePage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    variant="ghost"
                    size="sm"
                    title="Page suivante"
                >
                    ›
                </Button>
                <Button
                    @click="changePage(totalPages)"
                    :disabled="currentPage === totalPages"
                    variant="ghost"
                    size="sm"
                    title="Dernière page"
                >
                    »»
                </Button>
            </div>
        </div>

        <div class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
            <div class="text-xs text-center text-base-content/70">
                {{ filteredItems.length }} item{{ filteredItems.length > 1 ? 's' : '' }} affiché{{ filteredItems.length > 1 ? 's' : '' }}
                {{ totalPages > 1 ? ` - Page ${currentPage}/${totalPages}` : '' }}
            </div>
        </div>
    </div>
</template> 
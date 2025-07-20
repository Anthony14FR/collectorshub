<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';

interface Item {
  id: number;
  name: string;
  description: string | null;
  type: string;
  price: number;
  rarity: string;
  image: string | null;
  effect: object;
  created_at: string;
}

interface PaginatedItems {
  data: Item[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

interface Props {
  items: PaginatedItems;
  stats: {
    total: number;
    ball: number;
    avatar: number;
    background: number;
  };
  filters?: {
    type?: string;
  };
}

const props = defineProps<Props>();

const typeFilter = ref(props.filters?.type || '');
const searchTerm = ref('');
const showDeleteModal = ref(false);
const itemToDelete = ref<Item | null>(null);

const typeOptions = [
  { value: '', label: 'Tous types' },
  { value: 'ball', label: 'Balls' },
  { value: 'avatar', label: 'Avatars' },
  { value: 'background', label: 'Backgrounds' }
];

const filteredItems = computed(() => {
  return props.items.data.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      (item.description && item.description.toLowerCase().includes(searchTerm.value.toLowerCase()));
    return matchesSearch;
  });
});

const deleteItem = (item: Item) => {
  itemToDelete.value = item;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (itemToDelete.value) {
    router.delete(`/admin/items/${itemToDelete.value.id}`, {
      preserveScroll: true,
      onFinish: () => {
        showDeleteModal.value = false;
        itemToDelete.value = null;
      }
    });
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  itemToDelete.value = null;
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (typeFilter.value) {
    params.append('type', typeFilter.value);
  }

  router.get(`/admin/items?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  typeFilter.value = '';
  searchTerm.value = '';
  router.get('/admin/items', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getTypeLabel = (type: string) => {
  switch (type) {
  case 'ball': return 'Ball';
  case 'avatar': return 'Avatar';
  case 'background': return 'Background';
  default: return type;
  }
};

const getTypeColor = (type: string) => {
  switch (type) {
  case 'ball': return 'text-info bg-info/10 border-info/30';
  case 'avatar': return 'text-warning bg-warning/10 border-warning/30';
  case 'background': return 'text-secondary bg-secondary/10 border-secondary/30';
  default: return 'text-base-content bg-base-200/50 border-base-300/30';
  }
};

const getRarityLabel = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
};

const getRarityColor = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'text-base-content bg-base-200/50 border-base-300/30';
  case 'rare': return 'text-info bg-info/10 border-info/30';
  case 'epic': return 'text-warning bg-warning/10 border-warning/30';
  case 'legendary': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content bg-base-200/50 border-base-300/30';
  }
};

const formatPrice = (price: number) => {
  return price.toLocaleString();
};
</script>

<template>
  <Head title="Gestion des Items" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            🛍️ GESTION ITEMS
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ props.items.total }} item{{ props.items.total > 1 ? 's' : '' }} en boutique
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    LISTE DES ITEMS
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Select
                        v-model="typeFilter"
                        @change="applyFilters"
                        :options="typeOptions"
                        class="w-32"
                      />

                      <Button @click="clearFilters" variant="outline" size="sm">
                        ✨ Reset
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gradient-to-r from-base-200/50 to-base-300/30 border-b border-base-300/30">
                    <tr>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Item
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Type
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Rareté
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Prix
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Description
                      </th>
                      <th class="px-6 py-4 text-center text-sm font-bold text-base-content uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-base-300/30">
                    <tr
                      v-for="item in filteredItems"
                      :key="item.id"
                      class="hover:bg-base-200/30 transition-colors duration-200"
                    >
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="w-12 h-12 rounded-full bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center overflow-hidden">
                            <img
                              v-if="item.image"
                              :src="item.image"
                              :alt="item.name"
                              class="w-full h-full object-contain"
                              @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                            />
                            <span v-else class="text-lg">🛍️</span>
                          </div>
                          <div>
                            <div class="font-semibold text-base-content">{{ item.name }}</div>
                            <div class="text-sm text-base-content/70">ID: #{{ item.id }}</div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getTypeColor(item.type)
                        ]">
                          {{ getTypeLabel(item.type) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getRarityColor(item.rarity)
                        ]">
                          {{ getRarityLabel(item.rarity) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="font-semibold text-warning">
                          {{ formatPrice(item.price) }} 💰
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-base-content/70 max-w-xs truncate">
                          {{ item.description || 'Aucune description' }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                          <Button
                            @click="router.visit(`/admin/items/${item.id}`)"
                            variant="ghost"
                            size="sm"
                            class="text-info hover:text-info hover:bg-info/20 transition-colors"
                            title="Voir"
                          >
                            👁️
                          </Button>
                          <Button
                            @click="router.visit(`/admin/items/${item.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/20 transition-colors"
                            title="Modifier"
                          >
                            ✏️
                          </Button>
                          <Button
                            @click="deleteItem(item)"
                            variant="ghost"
                            size="sm"
                            class="text-error hover:text-error hover:bg-error/20 transition-colors"
                            title="Supprimer"
                          >
                            🗑️
                          </Button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="props.items.last_page > 1" class="p-4 bg-gradient-to-r from-success/5 to-success/10 border-t border-success/20">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                  <template v-for="link in props.items.links" :key="link.label">
                    <Button
                      v-if="link.url"
                      @click="router.visit(link.url)"
                      :variant="link.active ? 'primary' : 'ghost'"
                      size="sm"
                      class="min-w-[2.5rem]"
                      v-html="link.label"
                    />
                    <span v-else class="px-3 py-2 text-base-content/50 text-sm" v-html="link.label" />
                  </template>
                </div>
                <div class="text-xs text-center text-base-content/70 mt-2">
                  Affichage de {{ (props.items.current_page - 1) * props.items.per_page + 1 }}
                  à {{ Math.min(props.items.current_page * props.items.per_page, props.items.total) }}
                  sur {{ props.items.total }} résultats
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/items/create')"
                  variant="secondary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ➕ Nouvel item
                </Button>
                <Button
                  @click="router.visit('/admin/')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  ← Dashboard
                </Button>
                <Button
                  @click="router.visit('/me')"
                  variant="ghost"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-success">{{ props.stats.total }}</div>
                  <div class="text-sm text-base-content/70">Total items</div>
                </div>
                <div class="grid grid-cols-1 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-info">{{ props.stats.ball }}</div>
                    <div class="text-xs text-base-content/70">Balls</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-warning">{{ props.stats.avatar }}</div>
                    <div class="text-xs text-base-content/70">Avatars</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-secondary">{{ props.stats.background }}</div>
                    <div class="text-xs text-base-content/70">Backgrounds</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="cancelDelete" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-error/20 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚠️</span>
          </div>
          <h3 class="text-xl font-bold text-base-content">Supprimer l'item</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer l'item
          <span class="font-bold text-error">{{ itemToDelete?.name }}</span> ?
        </p>
        <p class="text-sm text-base-content/60">
          Cette action est irréversible et supprimera toutes les données associées.
        </p>

        <div class="flex gap-3 pt-4">
          <Button @click="confirmDelete" variant="outline" class="flex-1 border-error text-error hover:bg-error hover:text-error-content">
            🗑️ Supprimer
          </Button>
          <Button @click="cancelDelete" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>

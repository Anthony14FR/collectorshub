<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
  updated_at: string;
}

interface Props {
  item: Item;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteItem = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/items/${props.item.id}`, {
    onSuccess: () => router.visit('/admin/items'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatPrice = (price: number) => {
  return price.toLocaleString();
};

const getItemIcon = (type: string) => {
  switch (type) {
  case 'ball': return '⚾';
  case 'avatar': return '👤';
  case 'background': return '🖼️';
  default: return '🛍️';
  }
};
</script>

<template>
  <Head :title="`Item: ${item.name}`" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            👁️ DÉTAILS ITEM
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Informations complètes de {{ props.item.name }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                <div class="flex items-center gap-4">
                  <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center overflow-hidden">
                    <img
                      v-if="item.image"
                      :src="item.image"
                      :alt="item.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                    <span v-else class="text-3xl">{{ getItemIcon(item.type) }}</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-base-content mb-1">{{ props.item.name }}</h3>
                    <p class="text-base-content/70 mb-2">ID: #{{ props.item.id }}</p>
                    <div class="flex gap-3 flex-wrap">
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getTypeColor(props.item.type)
                      ]">
                        {{ getTypeLabel(props.item.type) }}
                      </span>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getRarityColor(props.item.rarity)
                      ]">
                        {{ getRarityLabel(props.item.rarity) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-6 border border-warning/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-warning mb-2">{{ formatPrice(props.item.price) }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Prix 💰</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-6 border border-info/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-info mb-2">{{ getItemIcon(props.item.type) }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">{{ getTypeLabel(props.item.type) }}</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl p-6 border border-primary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-primary mb-2">✨</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">{{ getRarityLabel(props.item.rarity) }}</div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📋 Informations générales
                    </h4>

                    <div class="space-y-4">
                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Nom</span>
                        <span class="font-medium">{{ props.item.name }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Type</span>
                        <span class="font-medium">{{ getTypeLabel(props.item.type) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Rareté</span>
                        <span class="font-medium">{{ getRarityLabel(props.item.rarity) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Prix</span>
                        <span class="font-medium text-warning">{{ formatPrice(props.item.price) }} 💰</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📝 Description & Effets
                    </h4>

                    <div class="space-y-4">
                      <div class="p-4 bg-base-200/30 rounded-lg">
                        <div class="text-sm text-base-content/70 mb-2">Description</div>
                        <div class="text-base-content">
                          {{ props.item.description || 'Aucune description disponible' }}
                        </div>
                      </div>

                      <div class="p-4 bg-base-200/30 rounded-lg">
                        <div class="text-sm text-base-content/70 mb-2">Effets</div>
                        <div class="text-base-content">
                          <pre v-if="Object.keys(props.item.effect || {}).length > 0" class="text-sm whitespace-pre-wrap">{{ JSON.stringify(props.item.effect, null, 2) }}</pre>
                          <span v-else class="text-base-content/50">Aucun effet spécial</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    🖼️ Aperçu visuel
                  </h4>

                  <div class="text-center">
                    <div class="inline-block p-8 bg-base-200/30 rounded-xl border border-base-300/20">
                      <div class="w-32 h-32 mx-auto rounded-lg bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center overflow-hidden">
                        <img
                          v-if="props.item.image"
                          :src="props.item.image"
                          :alt="props.item.name"
                          class="w-full h-full object-contain"
                          @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                        />
                        <span v-else class="text-6xl">{{ getItemIcon(props.item.type) }}</span>
                      </div>
                      <div class="mt-4 text-sm text-base-content/70">
                        {{ props.item.image ? 'Image personnalisée' : 'Image par défaut' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/items/${props.item.id}/edit`)"
                  variant="primary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ✏️ Modifier l'item
                </Button>
                <Button
                  @click="router.visit('/admin/items')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste items
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  RÉSUMÉ
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div>
                    <div class="text-xl font-bold text-warning">{{ formatPrice(props.item.price) }}</div>
                    <div class="text-xs text-base-content/70">Prix boutique</div>
                  </div>
                  <div>
                    <div class="text-xl font-bold text-success">{{ getItemIcon(props.item.type) }}</div>
                    <div class="text-xs text-base-content/70">Type d'item</div>
                  </div>
                </div>

                <div class="text-center pt-4 border-t border-base-300/30">
                  <div class="text-sm font-medium text-base-content mb-1">Niveau de rareté</div>
                  <div class="text-2xl font-bold text-primary">
                    ✨
                  </div>
                  <div class="text-xs text-base-content/60">
                    {{ getRarityLabel(props.item.rarity) }}
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📅</span>
                  HISTORIQUE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-sm text-base-content/70 mb-1">Créé le</div>
                  <div class="text-sm font-medium">{{ formatDate(props.item.created_at) }}</div>
                </div>
                <div class="text-center">
                  <div class="text-sm text-base-content/70 mb-1">Modifié le</div>
                  <div class="text-sm font-medium">{{ formatDate(props.item.updated_at) }}</div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚠️</span>
                  ZONE DANGER
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <p class="text-sm text-base-content/70">
                  Actions irréversibles sur cet item
                </p>
                <Button
                  @click="deleteItem"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer l'item
                </Button>
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
          <span class="font-bold text-error">{{ props.item.name }}</span> ?
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

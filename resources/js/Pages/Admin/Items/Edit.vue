<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

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

interface Props {
  item: Item;
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = ref({
  name: '',
  description: '',
  type: 'ball',
  price: 0,
  rarity: 'normal',
  image: null as File | null,
  effect: '{}'
});

const isSubmitting = ref(false);
const showDeleteModal = ref(false);

const typeOptions = [
  { value: 'ball', label: 'Ball' },
  { value: 'avatar', label: 'Avatar' },
  { value: 'background', label: 'Background' }
];

const rarityOptions = [
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' }
];

const initializeForm = () => {
  form.value = {
    name: props.item.name,
    description: props.item.description || '',
    type: props.item.type,
    price: props.item.price,
    rarity: props.item.rarity,
    image: null,
    effect: JSON.stringify(props.item.effect || {}, null, 2)
  };
};

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.value.image = target.files[0];
  }
};

const submit = () => {
  isSubmitting.value = true;

  const formData = new FormData();
  formData.append('_method', 'PUT');
  formData.append('name', form.value.name);
  formData.append('description', form.value.description);
  formData.append('type', form.value.type);
  formData.append('price', form.value.price.toString());
  formData.append('rarity', form.value.rarity);
  formData.append('effect', form.value.effect);

  if (form.value.image) {
    formData.append('image', form.value.image);
  }

  router.post(`/admin/items/${props.item.id}`, formData, {
    onSuccess: () => {
      router.visit('/admin/items');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

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

const goBack = () => {
  router.visit('/admin/items');
};

const getTypeIcon = (type: string) => {
  switch (type) {
  case 'ball': return '⚾';
  case 'avatar': return '👤';
  case 'background': return '🖼️';
  default: return '🛍️';
  }
};

const getRarityIcon = (rarity: string) => {
  switch (rarity) {
  case 'normal': return '⚪';
  case 'rare': return '🔵';
  case 'epic': return '🟣';
  case 'legendary': return '🟡';
  default: return '⚪';
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

onMounted(() => {
  initializeForm();
});
</script>

<template>
  <Head title="Modifier l'item" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ✏️ MODIFIER ITEM
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Édition de {{ props.item.name }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">🛍️</span>
                  INFORMATIONS ITEM
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Nom de l'item *
                    </label>
                    <Input
                      v-model="form.name"
                      placeholder="Master Ball"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.name" class="text-xs text-error mt-1">
                      {{ props.errors.name }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Prix *
                    </label>
                    <Input
                      v-model="form.price"
                      type="number"
                      min="0"
                      placeholder="1000"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.price" class="text-xs text-error mt-1">
                      {{ props.errors.price }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Description
                  </label>
                  <textarea
                    v-model="form.description"
                    placeholder="Description de l'item..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50"
                    rows="3"
                  ></textarea>
                  <p v-if="props.errors?.description" class="text-xs text-error mt-1">
                    {{ props.errors.description }}
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Type d'item *
                    </label>
                    <Select
                      v-model="form.type"
                      :options="typeOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.type" class="text-xs text-error mt-1">
                      {{ props.errors.type }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Rareté *
                    </label>
                    <Select
                      v-model="form.rarity"
                      :options="rarityOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.rarity" class="text-xs text-error mt-1">
                      {{ props.errors.rarity }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Nouvelle image de l'item
                  </label>
                  <Input
                    @change="handleImageChange"
                    type="file"
                    accept="image/*"
                    class="w-full"
                  />
                  <p v-if="props.errors?.image" class="text-xs text-error mt-1">
                    {{ props.errors.image }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Laisser vide pour conserver l'image actuelle
                  </p>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    ⚙️ Effets spéciaux
                  </h4>

                  <div class="p-4 bg-base-200/30 rounded-lg">
                    <div class="text-sm text-base-content/70 mb-2">Configuration JSON des effets</div>
                    <textarea
                      v-model="form.effect"
                      placeholder='{"catch_rate": 1.5, "special_effect": "masterball"}'
                      class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50 font-mono text-sm"
                      rows="4"
                    ></textarea>
                    <p class="text-xs text-base-content/60 mt-2">
                      Format JSON pour définir les effets spéciaux de l'item
                    </p>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    <span v-if="isSubmitting">⏳</span>
                    <span v-else>💾</span>
                    {{ isSubmitting ? 'Mise à jour...' : 'Mettre à jour' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    ← Retour à la liste
                  </Button>
                </div>
              </form>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🛍️</span>
                  ITEM ACTUEL
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center mb-4">
                  <div class="w-16 h-16 mx-auto rounded-lg bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center overflow-hidden mb-3">
                    <img
                      v-if="props.item.image"
                      :src="props.item.image"
                      :alt="props.item.name"
                      class="w-full h-full object-contain"
                      @error="(event) => { const target = event.target as HTMLImageElement; if (target) target.style.display = 'none'; }"
                    />
                    <span v-else class="text-2xl">{{ getTypeIcon(props.item.type) }}</span>
                  </div>
                  <div class="font-bold text-lg">{{ props.item.name }}</div>
                  <div class="text-sm text-base-content/70">ID: #{{ props.item.id }}</div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Type:</span>
                    <span class="font-medium">{{ typeOptions.find(t => t.value === props.item.type)?.label }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Rareté:</span>
                    <span class="font-medium">{{ rarityOptions.find(r => r.value === props.item.rarity)?.label }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Prix:</span>
                    <span class="font-medium text-warning">{{ props.item.price.toLocaleString() }} 💰</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Créé le:</span>
                    <span class="font-medium">{{ formatDate(props.item.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">👁️</span>
                  APERÇU
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center">
                  <div class="w-16 h-16 mx-auto rounded-lg bg-gradient-to-br from-success/20 to-success/10 flex items-center justify-center text-2xl mb-3">
                    {{ getTypeIcon(form.type) }}
                  </div>
                  <div class="font-bold text-lg">{{ form.name || 'Nom de l\'item' }}</div>
                  <div class="text-sm text-base-content/70 mb-2">{{ form.description || 'Description...' }}</div>
                  <div class="flex justify-center gap-2 mb-3">
                    <span class="text-xs px-2 py-1 bg-info/20 text-info rounded-full">
                      {{ typeOptions.find(t => t.value === form.type)?.label }}
                    </span>
                    <span class="text-xs px-2 py-1 bg-warning/20 text-warning rounded-full">
                      {{ getRarityIcon(form.rarity) }} {{ rarityOptions.find(r => r.value === form.rarity)?.label }}
                    </span>
                  </div>
                  <div class="text-lg font-bold text-success">
                    {{ form.price?.toLocaleString() || '0' }} 💰
                  </div>
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
                  La suppression d'un item est définitive et supprimera toutes ses données.
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

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/items/${props.item.id}`)"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  👁️ Voir l'item
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

<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';

interface User {
  id: number;
  username: string;
  email: string;
}

interface Item {
  id: number;
  name: string;
  type: string;
  rarity: string;
}

interface PromoCode {
  id: number;
  code: string;
  is_global: boolean;
  is_active: boolean;
  cash: number;
  expires_at: string | null;
  created_at: string;
  items: Array<{
    id: number;
    name: string;
    type: string;
    pivot: { quantity: number };
  }>;
  users: Array<{
    id: number;
    username: string;
    email: string;
  }>;
}

interface Props {
  promoCode: PromoCode;
  users: User[];
  items: Item[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  code: props.promoCode.code,
  is_global: props.promoCode.is_global,
  is_active: props.promoCode.is_active,
  cash: props.promoCode.cash,
  expires_at: props.promoCode.expires_at ? props.promoCode.expires_at.slice(0, 16) : '',
  target_user_ids: props.promoCode.users?.map(u => u.id) || [],
  items: props.promoCode.items?.map(i => ({ id: i.id, quantity: i.pivot.quantity })) || []
});

const isSubmitting = ref(false);
const showDeleteModal = ref(false);
const selectedUsers = ref<number[]>(props.promoCode.users?.map(u => u.id) || []);
const selectedItems = ref<Array<{ id: number; quantity: number }>>(
  props.promoCode.items?.map(i => ({ id: i.id, quantity: i.pivot.quantity })) || []
);

const userOptions = computed(() => {
  return props.users.map(user => ({
    value: user.id,
    label: `${user.username} (${user.email})`
  }));
});

const itemOptions = computed(() => {
  return props.items.map(item => ({
    value: item.id,
    label: `${item.name} (${item.type})`
  }));
});

const globalOptions = [
  { value: true, label: 'Global (tous les utilisateurs)' },
  { value: false, label: 'Ciblé (utilisateurs spécifiques)' }
];

const activeOptions = [
  { value: true, label: 'Actif' },
  { value: false, label: 'Inactif' }
];

watch(() => form.is_global, (isGlobal) => {
  if (isGlobal) {
    form.target_user_ids = [];
    selectedUsers.value = [];
  }
});

const addUser = () => {
  const userId = parseInt((document.getElementById('user-select') as HTMLSelectElement)?.value);
  if (userId && !selectedUsers.value.includes(userId)) {
    selectedUsers.value.push(userId);
    form.target_user_ids = selectedUsers.value;
  }
};

const removeUser = (userId: number) => {
  selectedUsers.value = selectedUsers.value.filter(id => id !== userId);
  form.target_user_ids = selectedUsers.value;
};

const addItem = () => {
  const itemId = parseInt((document.getElementById('item-select') as HTMLSelectElement)?.value);
  const quantity = parseInt((document.getElementById('item-quantity') as HTMLInputElement)?.value) || 1;

  if (itemId && !selectedItems.value.find(item => item.id === itemId)) {
    selectedItems.value.push({ id: itemId, quantity });
    form.items = selectedItems.value;

    (document.getElementById('item-select') as HTMLSelectElement).value = '';
    (document.getElementById('item-quantity') as HTMLInputElement).value = '1';
  }
};

const removeItem = (itemId: number) => {
  selectedItems.value = selectedItems.value.filter(item => item.id !== itemId);
  form.items = selectedItems.value;
};

const updateItemQuantity = (itemId: number, quantity: number) => {
  const item = selectedItems.value.find(item => item.id === itemId);
  if (item) {
    item.quantity = quantity;
    form.items = selectedItems.value;
  }
};

const getUserById = (id: number) => {
  return props.users.find(user => user.id === id);
};

const getItemById = (id: number) => {
  return props.items.find(item => item.id === id);
};

const submit = () => {
  isSubmitting.value = true;
  form.put(`/admin/promocodes/${props.promoCode.id}`, {
    onSuccess: () => {
      router.visit('/admin/promocodes');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const deletePromoCode = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/promocodes/${props.promoCode.id}`, {
    onSuccess: () => router.visit('/admin/promocodes'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
};

const goBack = () => {
  router.visit('/admin/promocodes');
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>

<template>
  <Head title="Modifier le code promo" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ✏️ MODIFIER CODE PROMO
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Édition du code {{ props.promoCode.code }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">🎟️</span>
                  INFORMATIONS CODE PROMO
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Code promotionnel *
                    </label>
                    <Input
                      v-model="form.code"
                      placeholder="PROMO2024"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.code" class="text-xs text-error mt-1">
                      {{ props.errors.code }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Cash à attribuer 💰
                    </label>
                    <Input
                      v-model="form.cash"
                      type="number"
                      min="0"
                      class="w-full"
                    />
                    <p v-if="props.errors?.cash" class="text-xs text-error mt-1">
                      {{ props.errors.cash }}
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Type de code *
                    </label>
                    <Select
                      v-model="form.is_global"
                      :options="globalOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.is_global" class="text-xs text-error mt-1">
                      {{ props.errors.is_global }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Statut *
                    </label>
                    <Select
                      v-model="form.is_active"
                      :options="activeOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.is_active" class="text-xs text-error mt-1">
                      {{ props.errors.is_active }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Date d'expiration
                  </label>
                  <Input
                    v-model="form.expires_at"
                    type="datetime-local"
                    class="w-full"
                  />
                  <p v-if="props.errors?.expires_at" class="text-xs text-error mt-1">
                    {{ props.errors.expires_at }}
                  </p>
                </div>

                <div v-if="!form.is_global" class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    👥 Utilisateurs ciblés
                  </h4>

                  <div class="flex gap-3">
                    <select id="user-select" class="flex-1 bg-base-200/50 border border-base-300 rounded-lg px-3 py-2 text-sm">
                      <option value="">Sélectionner un utilisateur...</option>
                      <option v-for="user in props.users" :key="user.id" :value="user.id">
                        {{ user.username }} ({{ user.email }})
                      </option>
                    </select>
                    <Button @click="addUser" type="button" variant="outline" size="sm">
                      Ajouter
                    </Button>
                  </div>

                  <div v-if="selectedUsers.length > 0" class="space-y-2">
                    <div v-for="userId in selectedUsers" :key="userId" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
                      <div>
                        <span class="font-medium">{{ getUserById(userId)?.username }}</span>
                        <span class="text-sm text-base-content/70 ml-2">({{ getUserById(userId)?.email }})</span>
                      </div>
                      <Button @click="removeUser(userId)" variant="ghost" size="sm" class="text-error">
                        ✕
                      </Button>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    🎁 Items à attribuer
                  </h4>

                  <div class="flex gap-3">
                    <select id="item-select" class="flex-1 bg-base-200/50 border border-base-300 rounded-lg px-3 py-2 text-sm">
                      <option value="">Sélectionner un item...</option>
                      <option v-for="item in props.items" :key="item.id" :value="item.id">
                        {{ item.name }} ({{ item.type }})
                      </option>
                    </select>
                    <Input
                      id="item-quantity"
                      type="number"
                      min="1"
                      value="1"
                      placeholder="Qté"
                      class="w-20"
                    />
                    <Button @click="addItem" type="button" variant="outline" size="sm">
                      Ajouter
                    </Button>
                  </div>

                  <div v-if="selectedItems.length > 0" class="space-y-2">
                    <div v-for="item in selectedItems" :key="item.id" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
                      <div class="flex items-center gap-3">
                        <span class="font-medium">{{ getItemById(item.id)?.name }}</span>
                        <span class="text-sm text-base-content/70">({{ getItemById(item.id)?.type }})</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <Input
                          :model-value="item.quantity"
                          @update:model-value="(value) => updateItemQuantity(item.id, parseInt(value as string))"
                          type="number"
                          min="1"
                          class="w-16"
                          size="sm"
                        />
                        <Button @click="removeItem(item.id)" variant="ghost" size="sm" class="text-error">
                          ✕
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    <span v-if="isSubmitting || form.processing">⏳</span>
                    <span v-else">💾</span>
                    {{ isSubmitting || form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
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
                  <span class="text-xl">🎟️</span>
                  CODE ACTUEL
                </h3>
              </div>
              <div class="p-6">
                <div class="text-center mb-4">
                  <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-warning/20 to-warning/10 flex items-center justify-center text-2xl font-bold mb-3">
                    🎟️
                  </div>
                  <div class="font-bold text-xl text-warning">{{ props.promoCode.code }}</div>
                  <div class="text-sm text-base-content/70">ID: #{{ props.promoCode.id }}</div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Créé le:</span>
                    <span class="font-medium">{{ formatDate(props.promoCode.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Type:</span>
                    <span class="font-medium">{{ props.promoCode.is_global ? 'Global' : 'Ciblé' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Statut:</span>
                    <span :class="props.promoCode.is_active ? 'text-success' : 'text-error'">
                      {{ props.promoCode.is_active ? 'Actif' : 'Inactif' }}
                    </span>
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
                  La suppression d'un code promo est définitive et supprimera toutes les données associées.
                </p>
                <Button
                  @click="deletePromoCode"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer le code promo
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
                  @click="router.visit(`/admin/promocodes/${props.promoCode.id}`)"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  👁️ Voir le code promo
                </Button>
                <Button
                  @click="router.visit('/admin/promocodes')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste codes promo
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
          <h3 class="text-xl font-bold text-base-content">Supprimer le code promo</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer le code promo
          <span class="font-bold text-error">{{ props.promoCode.code }}</span> ?
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

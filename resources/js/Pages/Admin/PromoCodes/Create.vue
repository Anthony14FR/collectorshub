<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

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

interface Props {
  users: User[];
  items: Item[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  code: '',
  is_global: true,
  is_active: true,
  cash: 0,
  expires_at: '',
  target_user_ids: [] as number[],
  items: [] as Array<{ id: number; quantity: number }>
});

const isSubmitting = ref(false);
const selectedUsers = ref<number[]>([]);
const selectedItems = ref<Array<{ id: number; quantity: number }>>([]);

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
  form.post('/admin/promocodes', {
    onSuccess: () => {
      router.visit('/admin/promocodes');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin/promocodes');
};
</script>

<template>
  <Head title="Créer un code promo" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ➕ CRÉER CODE PROMO
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Ajouter un nouveau code promotionnel
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
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
                    <p class="text-xs text-base-content/60">
                      Le code sera automatiquement converti en majuscules
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
                      placeholder="1000"
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
                  <p class="text-xs text-base-content/60">
                    Laisser vide pour un code sans expiration
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
                    <span v-else>💾</span>
                    {{ isSubmitting || form.processing ? 'Création...' : 'Créer le code promo' }}
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
                  <span class="text-xl">💡</span>
                  AIDE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Types de codes :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-info font-medium">Global</span> : Utilisable par tous</li>
                      <li>• <span class="text-secondary font-medium">Ciblé</span> : Utilisateurs spécifiques</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Récompenses :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• Cash : Monnaie virtuelle</li>
                      <li>• Items : Objets du jeu</li>
                      <li>• Combinaison possible</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      ⚠️ Un code sans expiration reste actif indéfiniment
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
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
  </div>
</template>

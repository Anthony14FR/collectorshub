<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Toast from '@/Components/UI/Toast.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Badge from '@/Components/UI/Badge.vue';

const props = defineProps({
  promoCode: Object,
  users: Array,
  items: Array,
  errors: Object,
  flash: Object
});

const form = ref({
  code: props.promoCode?.code || '',
  is_global: props.promoCode?.is_global || true,
  is_active: props.promoCode?.is_active || true,
  cash: props.promoCode?.cash || 0,
  expires_at: props.promoCode?.expires_at ? props.promoCode.expires_at.substring(0, 10) : '',
  target_user_ids: props.promoCode?.users ? props.promoCode.users.map(u => u.id) : [],
  items: props.promoCode?.items ? props.promoCode.items.map(i => ({ 
    id: i.id, 
    quantity: i.pivot?.quantity || 1 
  })) : []
});

const notification = ref(null);
const isLoading = ref(false);
const showToast = ref(false);
const toastMessage = ref('');

if (props.flash?.success) {
  toastMessage.value = props.flash.success;
  showToast.value = true;
}

if (props.flash?.error) {
  notification.value = {
    type: 'error',
    message: props.flash.error
  };
  setTimeout(() => {
    notification.value = null;
  }, 5000);
}
const selectedItem = ref(null);
const itemQuantity = ref(1);
const showItemSelector = ref(false);

const addItem = () => {
  if (!selectedItem.value) return;
  
  const itemExists = form.value.items.find(i => i.id === selectedItem.value);
  if (itemExists) {
    itemExists.quantity += parseInt(itemQuantity.value);
  } else {
    form.value.items.push({
      id: parseInt(selectedItem.value),
      quantity: parseInt(itemQuantity.value)
    });
  }
  
  selectedItem.value = null;
  itemQuantity.value = 1;
  showItemSelector.value = false;
};

const removeItem = (itemId) => {
  form.value.items = form.value.items.filter(i => i.id !== itemId);
};

const getItemById = (itemId) => {
  return props.items.find(i => i.id === itemId);
};

const toggleUserSelection = (userId) => {
  const index = form.value.target_user_ids.indexOf(userId);
  if (index === -1) {
    form.value.target_user_ids.push(userId);
  } else {
    form.value.target_user_ids.splice(index, 1);
  }
};

const handleSubmit = () => {
  isLoading.value = true;
  router.put(`/admin/promocodes/${props.promoCode.id}`, form.value, {
    onSuccess: () => {
      toastMessage.value = 'Code promotionnel mis à jour avec succès';
      showToast.value = true;
    },
    onError: (errors) => {
      notification.value = { type: 'error', message: 'Erreur lors de la mise à jour' };
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};
</script>

<template>
  <Head title="Éditer le Code Promo" />
  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-3xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider">
              🎟️ Modifier le Code Promo
            </h1>
            <p class="text-xs text-base-content/70 uppercase tracking-wider">
              Modifier un code promotionnel existant
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin/promocodes">
              <Button variant="ghost" size="sm">Retour à la liste</Button>
            </Link>
          </div>
        </div>
      </div>
      <div class="flex-1 overflow-auto p-4">
        <div class="max-w-3xl mx-auto bg-base-200/50 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8">
          <Toast 
            v-if="showToast"
            :show="showToast"
            type="success"
            title="Succès"
            :message="toastMessage"
            @close="showToast = false"
          />
        
          <Alert v-if="notification" :variant="notification.type" class="mb-4">{{ notification.message }}</Alert>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">Code</label>
                <input 
                  v-model="form.code"
                  type="text"
                  class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                  required
                />
                <p v-if="props.errors?.code" class="mt-1 text-xs text-error">{{ props.errors.code }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">Montant (cash)</label>
                <input 
                  v-model="form.cash"
                  type="number"
                  min="0"
                  class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                  required
                />
                <p v-if="props.errors?.cash" class="mt-1 text-xs text-error">{{ props.errors.cash }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">Expiration</label>
                <input 
                  v-model="form.expires_at"
                  type="date"
                  class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                />
                <p v-if="props.errors?.expires_at" class="mt-1 text-xs text-error">{{ props.errors.expires_at }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">Type</label>
                <select 
                  v-model="form.is_global"
                  class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                >
                  <option :value="true">Global</option>
                  <option :value="false">Ciblé</option>
                </select>
                <p v-if="props.errors?.is_global" class="mt-1 text-xs text-error">{{ props.errors.is_global }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-base-content/80 mb-2">Statut</label>
                <select 
                  v-model="form.is_active"
                  class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                >
                  <option :value="true">Actif</option>
                  <option :value="false">Inactif</option>
                </select>
                <p v-if="props.errors?.is_active" class="mt-1 text-xs text-error">{{ props.errors.is_active }}</p>
              </div>
            </div>
            
            <!-- Items Section -->
            <div class="mt-6">
              <h3 class="text-base font-semibold mb-2">Items à attribuer</h3>
              <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
                <div v-if="form.items.length === 0" class="text-sm text-base-content/50 text-center py-2">
                  Aucun item ajouté
                </div>
                <ul v-else class="space-y-2">
                  <li v-for="item in form.items" :key="item.id" class="flex items-center justify-between bg-base-200/40 p-2 rounded-lg">
                    <div class="flex items-center gap-2">
                      <Badge variant="primary" size="sm">{{ getItemById(item.id)?.name || 'Item' }}</Badge>
                      <span class="text-sm">{{ getItemById(item.id)?.type || 'Item' }} - {{ getItemById(item.id)?.rarity || '' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-medium">Quantité: {{ item.quantity }}</span>
                      <Button @click="removeItem(item.id)" variant="ghost" size="xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </Button>
                    </div>
                  </li>
                </ul>
                
                <div v-if="showItemSelector" class="mt-4 space-y-3 p-3 bg-base-200/50 rounded-lg">
                  <div>
                    <label class="block text-sm font-medium text-base-content/80 mb-2">Sélectionner un item</label>
                    <select 
                      v-model="selectedItem"
                      class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                    >
                      <option value="">Choisir un item...</option>
                      <option v-for="item in props.items" :key="item.id" :value="item.id">
                        {{ item.name }} ({{ item.type }}, {{ item.rarity }})
                      </option>
                    </select>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="flex-1">
                      <label class="block text-sm font-medium text-base-content/80 mb-2">Quantité</label>
                      <input 
                        type="number" 
                        v-model="itemQuantity" 
                        min="1" 
                        class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                      />
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                      <Button @click="addItem" variant="secondary" size="sm" :disabled="!selectedItem">Ajouter</Button>
                      <Button @click="showItemSelector = false" variant="ghost" size="sm">Annuler</Button>
                    </div>
                  </div>
                </div>
                
                <div v-if="!showItemSelector" class="mt-3">
                  <Button @click="showItemSelector = true" variant="secondary" size="sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter un item
                  </Button>
                </div>
              </div>
            </div>
            
            <!-- Users Section (if not global) -->
            <div v-if="!form.is_global" class="mt-6">
              <h3 class="text-base font-semibold mb-2">Utilisateurs ciblés</h3>
              <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
                <div v-if="form.target_user_ids.length === 0" class="text-sm text-base-content/50 text-center py-2">
                  Aucun utilisateur sélectionné
                </div>
                <div v-else class="flex flex-wrap gap-2">
                  <Badge 
                    v-for="userId in form.target_user_ids" 
                    :key="userId" 
                    variant="secondary" 
                    size="sm"
                    removable
                    @remove="toggleUserSelection(userId)"
                  >
                    {{ props.users.find(u => u.id === userId)?.username || 'User' }}
                  </Badge>
                </div>
                
                <div class="mt-4">
                  <label class="block text-sm font-medium text-base-content/80 mb-2">Ajouter des utilisateurs</label>
                  <select 
                    class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
                    @change="event => event.target.value && toggleUserSelection(parseInt(event.target.value))"
                  >
                    <option value="">Sélectionner un utilisateur...</option>
                    <option 
                      v-for="user in props.users.filter(u => !form.target_user_ids.includes(u.id))" 
                      :key="user.id" 
                      :value="user.id"
                    >
                      {{ user.username }} ({{ user.email }})
                    </option>
                  </select>
                </div>
              </div>
              <p v-if="props.errors?.target_user_ids" class="mt-1 text-xs text-error">{{ props.errors.target_user_ids }}</p>
            </div>
            
            <div class="flex justify-end gap-3">
              <Button type="submit" :disabled="isLoading" variant="primary" size="lg">
                {{ isLoading ? 'Sauvegarde...' : 'Enregistrer les modifications' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
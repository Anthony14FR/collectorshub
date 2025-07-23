<script setup>
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import MultipleItemSelect from '@/Components/UI/MultipleItemSelect.vue';
import MultipleSelect from '@/Components/UI/MultipleSelect.vue';
import Select from '@/Components/UI/Select.vue';
import Toast from '@/Components/UI/Toast.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Save, Ticket } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  promoCode: Object,
  users: Array,
  items: Array,
  errors: Object,
  flash: Object
});

const form = ref({
  code: props.promoCode?.code || '',
  is_global: props.promoCode?.is_global,
  is_active: props.promoCode?.is_active,
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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Ticket" :size="28" class="inline align-middle mr-2" />
            MODIFIER UN CODE PROMO
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Édition de {{ props.promoCode?.code }}
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Ticket" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Link href="/admin/promocodes">
                    <Button variant="outline" size="sm" class="w-full justify-start">
                      <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                    </Button>
                  </Link>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Ticket" :size="18" />
                    INFOS ACTUELLES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Code:</span>
                    <span class="text-sm font-mono font-bold text-primary">{{ props.promoCode?.code }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Type:</span>
                    <Badge :variant="props.promoCode?.is_global ? 'info' : 'secondary'" size="sm">
                      {{ props.promoCode?.is_global ? 'Global' : 'Ciblé' }}
                    </Badge>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Statut:</span>
                    <Badge :variant="props.promoCode?.is_active ? 'success' : 'error'" size="sm">
                      {{ props.promoCode?.is_active ? 'Actif' : 'Inactif' }}
                    </Badge>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Cash:</span>
                    <span class="text-sm font-bold text-success">{{ props.promoCode?.cash }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Ticket" :size="18" />
                  FORMULAIRE DE MODIFICATION
                </h3>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <Alert v-if="notification" :type="notification.type" :message="notification.message" />

                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <Input
                        label="Code"
                        v-model="form.code"
                        required
                        :error="props.errors?.code"
                        placeholder="Ex: WELCOME2024"
                      />
                      <Input
                        label="Montant (cash)"
                        type="number"
                        v-model="form.cash"
                        min="0"
                        :error="props.errors?.cash"
                        placeholder="Ex: 1000"
                      />
                      <Input
                        label="Expiration"
                        type="date"
                        v-model="form.expires_at"
                        :error="props.errors?.expires_at"
                      />
                      <Select
                        label="Type"
                        v-model="form.is_global"
                        :options="[{value: true, label: 'Global'}, {value: false, label: 'Ciblé'}]"
                        :error="props.errors?.is_global"
                        @update:modelValue="val => form.is_global = val === true || val === 'true'"
                        placeholder="Choisir le type"
                      />
                      <Select
                        label="Statut"
                        v-model="form.is_active"
                        :options="[{value: true, label: 'Actif'}, {value: false, label: 'Inactif'}]"
                        :error="props.errors?.is_active"
                        placeholder="Choisir le statut"
                      />
                      <MultipleSelect
                        v-if="!form.is_global"
                        label="Utilisateurs ciblés"
                        :options="props.users.map(u => ({ id: u.id, name: u.username, email: u.email }))"
                        v-model="form.target_user_ids"
                        placeholder="Sélectionner des utilisateurs..."
                        :disabled="form.is_global"
                      />
                    </div>
                  </div>

                  <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content">Ciblage et récompenses</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <MultipleItemSelect
                        label="Items à attribuer"
                        :options="props.items.map(i => ({
                          id: i.id,
                          name: i.name,
                          image: i.image,
                          type: i.type,
                          rarity: i.rarity
                        }))"
                        v-model="form.items"
                        placeholder="Sélectionner des items..."
                      />
                    </div>
                  </div>

                  <div class="flex justify-end gap-3 mt-8">
                    <Button variant="ghost" type="button" @click="$inertia.visit('/admin/promocodes')">
                      Annuler
                    </Button>
                    <Button variant="primary" type="submit" :loading="isLoading">
                      <component :is="isLoading ? Loader2 : Save" :size="16" class="mr-2" :class="{ 'animate-spin': isLoading }" />
                      {{ isLoading ? 'Sauvegarde...' : 'Enregistrer les modifications' }}
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Toast v-if="showToast" :message="toastMessage" @close="showToast = false" />
  </div>
</template>

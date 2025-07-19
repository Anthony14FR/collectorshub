<script setup>
import { ref } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import MultipleSelect from '@/Components/UI/MultipleSelect.vue';
import Alert from '@/Components/UI/Alert.vue';
import Toast from '@/Components/UI/Toast.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';

const props = defineProps({
  users: Array,
  items: Array,
  errors: Object,
  flash: Object
});

const form = ref({
  code: '',
  is_global: true,
  is_active: true,
  cash: 0,
  expires_at: '',
  target_user_ids: [],
  items: []
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

const handleSubmit = () => {
  isLoading.value = true;
  router.post('/admin/promocodes', form.value, {
    onSuccess: () => {
      toastMessage.value = 'Code promotionnel créé avec succès';
      showToast.value = true;
    },
    onError: () => {
      notification.value = { type: 'error', message: 'Erreur lors de la création' };
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};
</script>

<template>
  <Head title="Créer un Code Promo" />
  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-3xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider">
              🎟️ Nouveau Code Promo
            </h1>
            <p class="text-xs text-base-content/70 uppercase tracking-wider">
              Créer un code promotionnel
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
              <Input label="Code" v-model="form.code" required :state="props.errors?.code ? 'error' : 'default'" :helperText="props.errors?.code" />
              <Input label="Montant (cash)" type="number" v-model="form.cash" min="0" :state="props.errors?.cash ? 'error' : 'default'" :helperText="props.errors?.cash" />
              <Input label="Expiration" type="date" v-model="form.expires_at" :state="props.errors?.expires_at ? 'error' : 'default'" :helperText="props.errors?.expires_at" />
              <Select label="Type" v-model="form.is_global" :options="[{value: true, label: 'Global'}, {value: false, label: 'Ciblé'}]" :state="props.errors?.is_global ? 'error' : 'default'" :helperText="props.errors?.is_global" />
              <Select label="Statut" v-model="form.is_active" :options="[{value: true, label: 'Actif'}, {value: false, label: 'Inactif'}]" :state="props.errors?.is_active ? 'error' : 'default'" :helperText="props.errors?.is_active" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <MultipleSelect
                v-if="!form.is_global"
                label="Utilisateurs ciblés"
                :options="props.users.map(u => ({ id: u.id, name: u.username, email: u.email }))"
                v-model="form.target_user_ids"
                placeholder="Sélectionner des utilisateurs..."
                :disabled="form.is_global"
              />
              <MultipleSelect
                label="Items à attribuer"
                :options="props.items.map(i => ({ id: i.id, name: i.name + ' (' + i.type + ')', description: i.rarity }))"
                v-model="form.items"
                placeholder="Sélectionner des items..."
              />
            </div>
            <div class="flex justify-end gap-3">
              <Button type="submit" :disabled="isLoading" variant="primary" size="lg">
                {{ isLoading ? 'Création...' : 'Créer le code promo' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

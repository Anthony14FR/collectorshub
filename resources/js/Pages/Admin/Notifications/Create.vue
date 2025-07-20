<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  username: string;
  level: number;
  role: string;
}

interface Props {
  users: User[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = ref({
  title: '',
  message: '',
  target_type: 'all' as 'all' | 'specific',
  user_ids: [] as number[]
});

const isSubmitting = ref(false);
const selectedUsers = ref<number[]>([]);

const targetTypeOptions = [
  { value: 'all', label: 'Tous les utilisateurs' },
  { value: 'specific', label: 'Utilisateurs spécifiques' }
];

const userOptions = computed(() => {
  return props.users.map(user => ({
    value: user.id,
    label: `${user.username || user.name} (${user.email})`
  }));
});

const addUser = () => {
  const userId = parseInt((document.getElementById('user-select') as HTMLSelectElement)?.value);
  if (userId && !selectedUsers.value.includes(userId)) {
    selectedUsers.value.push(userId);
    form.value.user_ids = selectedUsers.value;
  }
};

const removeUser = (userId: number) => {
  selectedUsers.value = selectedUsers.value.filter(id => id !== userId);
  form.value.user_ids = selectedUsers.value;
};

const getUserById = (id: number) => {
  return props.users.find(user => user.id === id);
};

const submit = () => {
  isSubmitting.value = true;

  router.post('/admin/notifications', form.value, {
    onSuccess: () => {
      router.visit('/admin');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin');
};

const resetForm = () => {
  form.value = {
    title: '',
    message: '',
    target_type: 'all',
    user_ids: []
  };
  selectedUsers.value = [];
};
</script>

<template>
  <Head title="Créer une annonce" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-2 tracking-wider">
            📢 CRÉER ANNONCE
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Diffuser un message à la communauté
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">📨</span>
                  INFORMATIONS ANNONCE
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Titre de l'annonce *
                  </label>
                  <Input
                    v-model="form.title"
                    placeholder="Titre de votre annonce"
                    class="w-full"
                    required
                  />
                  <p v-if="props.errors?.title" class="text-xs text-error mt-1">
                    {{ props.errors.title }}
                  </p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Message *
                  </label>
                  <textarea
                    v-model="form.message"
                    placeholder="Contenu de votre message..."
                    class="textarea textarea-bordered w-full bg-base-100/80 border-base-300/50 min-h-[120px]"
                    required
                  ></textarea>
                  <p v-if="props.errors?.message" class="text-xs text-error mt-1">
                    {{ props.errors.message }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Rédigez un message clair et concis pour vos utilisateurs
                  </p>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Type de diffusion *
                  </label>
                  <Select
                    v-model="form.target_type"
                    :options="targetTypeOptions"
                    class="w-full"
                    required
                  />
                  <p v-if="props.errors?.target_type" class="text-xs text-error mt-1">
                    {{ props.errors.target_type }}
                  </p>
                </div>

                <div v-if="form.target_type === 'specific'" class="space-y-4">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                    👥 Utilisateurs ciblés
                  </h4>

                  <div class="flex gap-3">
                    <select id="user-select" class="flex-1 bg-base-200/50 border border-base-300 rounded-lg px-3 py-2 text-sm">
                      <option value="">Sélectionner un utilisateur...</option>
                      <option v-for="user in props.users" :key="user.id" :value="user.id">
                        {{ user.username || user.name }} ({{ user.email }})
                      </option>
                    </select>
                    <Button @click="addUser" type="button" variant="outline" size="sm">
                      Ajouter
                    </Button>
                  </div>

                  <div v-if="selectedUsers.length > 0" class="space-y-2">
                    <div v-for="userId in selectedUsers" :key="userId" class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg">
                      <div>
                        <span class="font-medium">{{ getUserById(userId)?.username || getUserById(userId)?.name }}</span>
                        <span class="text-sm text-base-content/70 ml-2">({{ getUserById(userId)?.email }})</span>
                      </div>
                      <Button @click="removeUser(userId)" variant="ghost" size="sm" class="text-error">
                        ✕
                      </Button>
                    </div>
                  </div>

                  <p v-if="props.errors?.user_ids" class="text-xs text-error mt-1">
                    {{ props.errors.user_ids }}
                  </p>
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
                    <span v-else>📤</span>
                    {{ isSubmitting ? 'Envoi...' : 'Envoyer l\'annonce' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    ← Retour au dashboard
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
                  CONSEILS
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Titre efficace :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• Soyez concis et explicite</li>
                      <li>• Utilisez des mots-clés importants</li>
                      <li>• Évitez les majuscules excessives</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Message clair :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• Structure en paragraphes courts</li>
                      <li>• Informations essentielles en premier</li>
                      <li>• Ton professionnel mais accessible</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      💬 Les utilisateurs recevront une notification immédiate
                    </p>
                  </div>
                </div>
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
                  <div class="text-2xl font-bold text-primary">{{ props.users.length }}</div>
                  <div class="text-sm text-base-content/70">Utilisateurs totaux</div>
                </div>

                <div v-if="form.target_type === 'specific'" class="text-center">
                  <div class="text-2xl font-bold text-secondary">{{ selectedUsers.length }}</div>
                  <div class="text-sm text-base-content/70">Utilisateurs sélectionnés</div>
                </div>

                <div v-if="form.target_type === 'all'" class="pt-4 border-t border-base-300/30 text-center">
                  <div class="text-sm font-medium text-base-content mb-1">Diffusion globale</div>
                  <div class="text-lg font-bold text-success">
                    📢
                  </div>
                  <div class="text-xs text-base-content/60">
                    Tous les utilisateurs recevront l'annonce
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔧</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="resetForm"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🔄 Réinitialiser le formulaire
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard Admin
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

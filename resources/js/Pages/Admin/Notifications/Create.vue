<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import MultipleSelect from '@/Components/UI/MultipleSelect.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Zap, ArrowLeft, Plus, Save, Loader2, Info, RotateCcw, Home, Users, Target, BarChart3, Eye, Send, Bell } from 'lucide-vue-next';

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
}

const props = defineProps<Props>();

const form = ref({
  title: '',
  message: '',
  target_type: 'all' as 'all' | 'specific',
  user_ids: [] as number[]
});

const isSubmitting = ref(false);

const resetForm = () => {
  form.value = {
    title: '',
    message: '',
    target_type: 'all',
    user_ids: []
  };
};

const submit = async () => {
  if (isSubmitting.value) return;

  isSubmitting.value = true;

  router.post('/admin/notifications', form.value, {
    onSuccess: () => {
      resetForm();
      isSubmitting.value = false;
    },
    onError: () => {
      isSubmitting.value = false;
    }
  });
};

const goBack = () => {
  router.visit('/admin');
};

const userOptions = props.users.map(user => ({
  id: user.id,
  name: user.username || user.name,
  email: user.email,
  description: `Niveau ${user.level} • ${user.role}`,
  avatar: user.username?.charAt(0).toUpperCase() || user.name?.charAt(0).toUpperCase()
}));
</script>

<template>
  <Head title="Créer une annonce - Admin" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            CRÉER UNE ANNONCE
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Diffusion de messages administratifs
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
                    <component :is="Zap" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="goBack" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Dashboard Admin
                  </Button>
                  <Button @click="resetForm" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="RotateCcw" :size="16" class="mr-2" /> Réinitialiser
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Target" :size="18" />
                    TYPE D'ANNONCE
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <label class="flex items-center gap-3 cursor-pointer group">
                    <input v-model="form.target_type" type="radio" value="all"
                           class="radio radio-primary radio-sm" />
                    <div class="flex items-center gap-2">
                      <component :is="Bell" :size="16" />
                      <div>
                        <div class="text-sm font-medium group-hover:text-primary transition-colors">
                          Tous les utilisateurs
                        </div>
                        <div class="text-xs text-base-content/60">
                          Diffusion globale
                        </div>
                      </div>
                    </div>
                  </label>

                  <label class="flex items-center gap-3 cursor-pointer group">
                    <input v-model="form.target_type" type="radio" value="specific"
                           class="radio radio-primary radio-sm" />
                    <div class="flex items-center gap-2">
                      <component :is="Users" :size="16" />
                      <div>
                        <div class="text-sm font-medium group-hover:text-primary transition-colors">
                          Utilisateurs spécifiques
                        </div>
                        <div class="text-xs text-base-content/60">
                          Sélection ciblée
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    DESTINATAIRES
                  </h3>
                </div>
                <div class="p-4">
                  <div class="text-center">
                    <div class="text-2xl font-bold text-info mb-1">
                      {{ form.target_type === 'all' ? props.users.length : form.user_ids.length }}
                    </div>
                    <div class="text-xs text-base-content/60">
                      {{ form.target_type === 'all' ? 'Tous les utilisateurs' : 'Utilisateurs sélectionnés' }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Eye" :size="18" />
                    APERÇU
                  </h3>
                </div>
                <div class="p-4">
                  <div class="bg-base-200/50 rounded-lg p-3 space-y-2">
                    <div class="font-bold text-sm text-warning line-clamp-2">
                      {{ form.title || 'Titre de l\'annonce...' }}
                    </div>
                    <div class="text-xs text-base-content/70 line-clamp-3">
                      {{ form.message || 'Votre message apparaîtra ici...' }}
                    </div>
                    <div class="text-xs text-base-content/50 border-t border-base-300/30 pt-2">
                      Annonce • Admin
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Zap" :size="18" />
                  RÉDIGER L'ANNONCE
                </h3>
              </div>

              <form @submit.prevent="submit" class="flex-1 flex flex-col">
                <div class="flex-1 p-6 space-y-6 min-h-0">
                  <div>
                    <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                      Titre de l'annonce
                    </label>
                    <input v-model="form.title" type="text" required
                           placeholder="Titre accrocheur pour votre annonce..."
                           class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none" />
                  </div>

                  <div>
                    <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                      Message
                    </label>
                    <textarea v-model="form.message" rows="6" required
                              placeholder="Rédigez votre message ici. Soyez clair et informatif..."
                              class="w-full px-4 py-3 text-base bg-base-200/50 border border-base-300 focus:border-primary focus:bg-base-200/80 backdrop-blur-sm rounded-lg transition-all duration-200 focus:outline-none resize-none"></textarea>
                  </div>

                  <div v-if="form.target_type === 'specific'" class="flex-1 min-h-0">
                    <label class="block text-xs font-bold tracking-wider text-base-content/80 uppercase mb-1">
                      Sélectionner les utilisateurs
                    </label>
                    <MultipleSelect v-model="form.user_ids" :options="userOptions"
                                    placeholder="Rechercher et sélectionner des utilisateurs..."
                                    searchPlaceholder="Rechercher par nom, email ou niveau..."
                                    emptyText="Aucun utilisateur trouvé" max-height="max-h-24" :max-display-items="1"
                                    show-field="email" />
                  </div>
                </div>

                <div class="shrink-0 p-4 bg-gradient-to-r from-primary/5 to-secondary/5 border-t border-primary/20">
                  <div class="flex gap-3">
                    <Button type="button" @click="resetForm" variant="outline" size="sm" class="flex-1"
                            :disabled="isSubmitting">
                      <component :is="RotateCcw" :size="16" class="mr-2" />
                      Réinitialiser
                    </Button>
                    <Button type="submit" variant="primary" size="sm" class="flex-1"
                            :loading="isSubmitting"
                            :disabled="!form.title.trim() || !form.message.trim() || (form.target_type === 'specific' && form.user_ids.length === 0)">
                      <component :is="isSubmitting ? Loader2 : Send" :size="16" class="mr-2" :class="{ 'animate-spin': isSubmitting }" />
                      {{ isSubmitting ? 'Envoi...' : 'Envoyer' }}
                    </Button>
                  </div>

                  <div class="mt-3 text-xs text-center text-base-content/60">
                    <span v-if="form.target_type === 'all'">
                      {{ props.users.length }} utilisateurs
                    </span>
                    <span v-else-if="form.user_ids.length > 0">
                      {{ form.user_ids.length }} sélectionné(s)
                    </span>
                    <span v-else class="text-warning">
                      Sélectionnez des utilisateurs
                    </span>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

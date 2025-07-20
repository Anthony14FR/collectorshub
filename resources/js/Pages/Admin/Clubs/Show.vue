<template>
  <Head title="Détail du Club" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <img :src="club.icon" :alt="club.name" class="w-8 h-8 rounded-full border-2 border-primary">
            {{ club.name }}
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Détails du club
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
                  <Button @click="router.visit('/admin/clubs')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour à la liste
                  </Button>
                  <Button @click="openDeleteModal" variant="outline" size="sm" class="w-full justify-start text-error border-error hover:bg-error/10">
                    <component :is="Trash2" :size="16" class="mr-2" /> Supprimer
                  </Button>
                  <Button @click="router.visit('/admin/')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Trophy" :size="16" class="mr-2" /> Dashboard
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-base-200/40 rounded-lg p-2">
                      <div class="text-base-content/70">Membres</div>
                      <div class="font-bold text-lg">{{ club.members.length }}</div>
                    </div>
                    <div class="bg-primary/10 rounded-lg p-2">
                      <div class="text-primary/70">CP Total</div>
                      <div class="font-bold text-lg text-primary">{{ club.total_cp.toLocaleString() }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Info" :size="18" />
                    INFORMATIONS
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Consultez les détails de ce club et ses membres.
                  </div>
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Vous pouvez supprimer le club si nécessaire.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Trophy" :size="18" />
                    INFORMATIONS GÉNÉRALES
                  </h3>
                </div>
                <div class="p-6 space-y-4">
                  <div class="text-center mb-6">
                    <img :src="club.icon" :alt="club.name" class="w-16 h-16 rounded-full border-2 border-primary mx-auto mb-3">
                    <h2 class="text-xl font-bold text-base-content">{{ club.name }}</h2>
                    <p class="text-sm text-base-content/70">{{ club.members.length }} membre(s)</p>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <h3 class="font-medium text-base-content mb-2 flex items-center gap-2">
                        <component :is="Info" :size="16" />
                        Description
                      </h3>
                      <p class="text-sm text-base-content/80 bg-base-200/30 backdrop-blur-sm p-3 rounded-lg border border-base-300/20">
                        {{ club.description }}
                      </p>
                    </div>

                    <div>
                      <h3 class="font-medium text-base-content mb-2 flex items-center gap-2">
                        <component :is="BarChart3" :size="16" />
                        Statistiques
                      </h3>
                      <div class="bg-base-200/30 backdrop-blur-sm p-3 rounded-lg border border-base-300/20 space-y-2">
                        <div class="flex justify-between text-sm">
                          <span class="text-base-content/70">CP Total:</span>
                          <span class="font-medium">{{ club.total_cp.toLocaleString() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                          <span class="text-base-content/70">Créé le:</span>
                          <span class="font-medium">{{ formatDate(club.created_at) }}</span>
                        </div>
                      </div>
                    </div>

                    <div>
                      <h3 class="font-medium text-base-content mb-2 flex items-center gap-2">
                        <component :is="Crown" :size="16" />
                        Chef du club
                      </h3>
                      <div class="bg-primary/10 backdrop-blur-sm p-3 rounded-lg border border-primary/20 flex items-center gap-3">
                        <img :src="club.leader.avatar" :alt="club.leader.username"
                             class="w-10 h-10 rounded-full border-2 border-primary">
                        <div>
                          <p class="font-medium text-base-content">{{ club.leader.username }}</p>
                          <p class="text-xs text-base-content/70">Niveau {{ club.leader.level }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Users" :size="18" />
                    MEMBRES DU CLUB
                  </h3>
                </div>
                <div class="p-6">
                  <div v-if="club.members.length === 0" class="text-center py-8 text-base-content/60">
                    <component :is="Users" :size="32" class="mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Aucun membre dans ce club</p>
                  </div>

                  <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="member in club.members" :key="member.id"
                         class="bg-base-200/30 backdrop-blur-sm p-4 rounded-lg border border-base-300/20 flex items-center gap-3">
                      <img :src="member.avatar" :alt="member.username"
                           class="w-10 h-10 rounded-full border border-base-300">
                      <div class="flex-1">
                        <p class="font-medium text-sm text-base-content">{{ member.username }}</p>
                        <p class="text-xs text-base-content/70">Niveau {{ member.level }}</p>
                      </div>
                      <div v-if="member.pivot.role === 'leader'"
                           class="px-2 py-1 bg-primary/20 text-primary text-xs rounded-full font-medium flex items-center gap-1">
                        <component :is="Crown" :size="10" />
                        Chef
                      </div>
                      <div v-else
                           class="px-2 py-1 bg-base-300/50 text-base-content/70 text-xs rounded-full">
                        Membre
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="closeDeleteModal" max-width="md">
      <div class="p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-error/10 rounded-lg">
            <component :is="AlertTriangle" :size="24" class="text-error" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-base-content">Supprimer le club</h3>
            <p class="text-sm text-base-content/70">Cette action est irréversible</p>
          </div>
        </div>

        <div class="bg-base-200/30 rounded-lg p-4 mb-6">
          <div class="flex items-center gap-3">
            <img :src="club.icon" :alt="club.name" class="w-8 h-8 rounded-full border border-primary">
            <div>
              <h4 class="font-bold text-base-content">{{ club.name }}</h4>
              <p class="text-xs text-base-content/70">{{ club.members.length }} membre(s)</p>
            </div>
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-base-content mb-2">Raison de la suppression</label>
          <textarea
            v-model="deleteReason"
            rows="4"
            placeholder="Expliquez pourquoi vous supprimez ce club..."
            class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 resize-none placeholder:text-base-content/50"
            required
          ></textarea>
        </div>

        <div class="flex justify-end gap-3">
          <Button @click="closeDeleteModal" variant="outline">
            Annuler
          </Button>
          <Button @click="confirmDelete" variant="error" :disabled="!deleteReason.trim()">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Trophy, Zap, ArrowLeft, Trash2, BarChart3, Info, Users, Crown, AlertTriangle } from 'lucide-vue-next';

interface ClubMember {
  id: number;
  username: string;
  avatar: string;
  level: number;
  pivot: {
    role: 'leader' | 'member';
  };
}

interface Club {
  id: number;
  name: string;
  description: string;
  icon: string;
  leader_id: number;
  total_cp: number;
  created_at: string;
  leader: {
    id: number;
    username: string;
    avatar: string;
    level: number;
  };
  members: ClubMember[];
}

interface Props {
  club: Club;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);
const deleteReason = ref('');

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const openDeleteModal = () => {
  showDeleteModal.value = true;
  deleteReason.value = '';
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteReason.value = '';
};

const confirmDelete = () => {
  if (!deleteReason.value.trim()) return;

  router.delete(`/admin/clubs/${props.club.id}`, {
    data: {
      reason: deleteReason.value.trim()
    },
    onSuccess: () => {
      router.visit('/admin/clubs');
    },
    onError: (errors: any) => {
      console.error('Erreur:', errors);
    }
  });
};
</script>

<template>
  <Head title="Gestion des Clubs" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <component :is="Trophy" :size="28" class="inline align-middle mr-2" />
            GESTION DES CLUBS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Administration des clubs
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
                  <Button @click="router.visit('/admin/')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour Admin
                  </Button>
                  <Button @click="router.visit('/me')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="User" :size="16" class="mr-2" /> Profil
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
                      <div class="text-base-content/70">Total</div>
                      <div class="font-bold text-lg">{{ clubs.data.length }}</div>
                    </div>
                    <div class="bg-primary/10 rounded-lg p-2">
                      <div class="text-primary/70">Pages</div>
                      <div class="font-bold text-lg text-primary">{{ clubs.last_page }}</div>
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
                    Gérez tous les clubs de la plateforme depuis cette interface.
                  </div>
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Vous pouvez consulter les détails et supprimer des clubs si nécessaire.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Trophy" :size="18" />
                    LISTE DES CLUBS
                  </h3>
                  <div class="text-xs text-base-content/70">
                    {{ clubs.data.length }} club{{ clubs.data.length > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>

              <div class="p-6 max-h-[850px] overflow-y-scroll">
                <div v-if="clubs.data.length === 0" class="text-center py-12 text-base-content/60">
                  <component :is="Trophy" :size="64" class="mx-auto mb-4 opacity-50" />
                  <h3 class="text-lg font-medium mb-2">Aucun club</h3>
                  <p class="text-sm">Aucun club n'a été créé pour le moment.</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div v-for="club in clubs.data" :key="club.id"
                       class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 overflow-hidden transition-all duration-200 hover:shadow-lg hover:shadow-primary/10 hover:-translate-y-1">
                    <div class="p-4">
                      <div class="flex items-center gap-3 mb-3">
                        <img :src="club.icon" :alt="club.name" class="w-10 h-10 rounded-full border-2 border-primary">
                        <div class="flex-1">
                          <h3 class="font-bold text-base-content text-sm">{{ club.name }}</h3>
                          <p class="text-xs text-base-content/70">{{ club.members_count }} membre(s)</p>
                        </div>
                      </div>

                      <p class="text-xs text-base-content/80 mb-4 line-clamp-2">
                        {{ club.description }}
                      </p>

                      <div class="flex items-center gap-2 mb-4">
                        <img :src="club.leader.avatar" :alt="club.leader.username"
                             class="w-6 h-6 rounded-full border border-base-300">
                        <span class="text-xs font-medium">{{ club.leader.username }}</span>
                        <span class="text-xs text-primary">Chef</span>
                      </div>

                      <div class="flex gap-2">
                        <Button @click="router.visit(`/admin/clubs/${club.id}`)" variant="outline" size="sm" class="flex-1">
                          <component :is="Eye" :size="14" class="mr-1" /> Voir
                        </Button>
                        <Button @click="openDeleteModal(club)" variant="outline" size="sm" class="text-error border-error hover:bg-error/10">
                          <component :is="Trash2" :size="14" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="clubs.last_page > 1" class="mt-6 pt-4 border-t border-base-300/30 flex justify-center">
                  <div class="flex gap-2">
                    <Button v-if="clubs.prev_page_url" @click="router.visit(clubs.prev_page_url)" variant="outline" size="sm">
                      <component :is="ChevronLeft" :size="14" />
                    </Button>
                    <span class="px-3 py-2 text-sm font-medium bg-primary/20 text-primary rounded-lg">
                      {{ clubs.current_page }} / {{ clubs.last_page }}
                    </span>
                    <Button v-if="clubs.next_page_url" @click="router.visit(clubs.next_page_url)" variant="outline" size="sm">
                      <component :is="ChevronRight" :size="14" />
                    </Button>
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

        <div v-if="selectedClub" class="bg-base-200/30 rounded-lg p-4 mb-6">
          <div class="flex items-center gap-3">
            <img :src="selectedClub.icon" :alt="selectedClub.name" class="w-8 h-8 rounded-full border border-primary">
            <div>
              <h4 class="font-bold text-base-content">{{ selectedClub.name }}</h4>
              <p class="text-xs text-base-content/70">{{ selectedClub.members_count }} membre(s)</p>
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
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Trophy, Zap, ArrowLeft, User, BarChart3, Info, Eye, Trash2, ChevronLeft, ChevronRight, AlertTriangle } from 'lucide-vue-next';

interface Club {
  id: number;
  name: string;
  description: string;
  icon: string;
  leader_id: number;
  total_cp: number;
  members_count: number;
  leader: {
    id: number;
    username: string;
    avatar: string;
  };
}

interface Props {
  clubs: {
    data: Club[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
  };
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);
const selectedClub = ref<Club | null>(null);
const deleteReason = ref('');

const openDeleteModal = (club: Club) => {
  selectedClub.value = club;
  showDeleteModal.value = true;
  deleteReason.value = '';
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  selectedClub.value = null;
  deleteReason.value = '';
};

const confirmDelete = () => {
  if (!selectedClub.value || !deleteReason.value.trim()) return;

  router.delete(`/admin/clubs/${selectedClub.value.id}`, {
    data: {
      reason: deleteReason.value.trim()
    },
    onSuccess: () => {
      closeDeleteModal();
    },
    onError: (errors) => {
      console.error('Erreur:', errors);
    }
  });
};
</script>

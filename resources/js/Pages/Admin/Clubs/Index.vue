<template>
  <Head title="Gestion des Clubs" />
  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider">
              🏆 Gestion des Clubs
            </h1>
            <p class="text-xs text-base-content/70 uppercase tracking-wider">
              Administration des clubs
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin">
              <Button variant="ghost" size="sm">Retour Admin</Button>
            </Link>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-auto p-4">
        <div class="max-w-7xl mx-auto">
          <div v-if="clubs.data.length === 0" class="text-center py-12 text-base-content/60">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-lg font-medium mb-2">Aucun club</h3>
            <p class="text-sm">Aucun club n'a été créé pour le moment.</p>
          </div>

          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="club in clubs.data" :key="club.id"
                 class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden hover:scale-[1.02] transition-all duration-200">
              <div class="p-4">
                <div class="flex items-center gap-3 mb-3">
                  <img :src="club.icon" :alt="club.name" class="w-10 h-10 rounded-full border-2 border-primary">
                  <div class="flex-1">
                    <h3 class="font-bold text-lg">{{ club.name }}</h3>
                    <p class="text-xs text-base-content/60">{{ club.members_count }} membre(s)</p>
                  </div>
                </div>

                <p class="text-sm text-base-content/80 mb-4 line-clamp-2">
                  {{ club.description }}
                </p>

                <div class="flex items-center gap-2 mb-4">
                  <img :src="club.leader.avatar" :alt="club.leader.username"
                       class="w-6 h-6 rounded-full border border-base-300">
                  <span class="text-sm font-medium">{{ club.leader.username }}</span>
                  <span class="text-xs text-primary">Chef</span>
                </div>

                <div class="flex gap-2">
                  <Link :href="`/admin/clubs/${club.id}`" class="flex-1">
                    <Button variant="outline" size="sm" class="w-full">
                      👁️ Voir
                    </Button>
                  </Link>
                  <Button @click="openDeleteModal(club)" variant="secondary" size="sm">
                    🗑️ Supprimer
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="clubs.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex gap-2">
              <Link v-if="clubs.prev_page_url" :href="clubs.prev_page_url"
                    class="px-3 py-2 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                ←
              </Link>
              <span class="px-3 py-2 bg-primary/20 text-primary rounded-lg">
                {{ clubs.current_page }} / {{ clubs.last_page }}
              </span>
              <Link v-if="clubs.next_page_url" :href="clubs.next_page_url"
                    class="px-3 py-2 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-base-100 rounded-xl border border-base-300 max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4 text-error">
          🗑️ Supprimer le club "{{ selectedClub?.name }}"
        </h3>

        <p class="text-sm text-base-content/80 mb-4">
          Cette action est irréversible. Le chef du club recevra une notification avec votre message.
        </p>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Raison de la suppression</label>
          <textarea
            v-model="deleteReason"
            rows="4"
            placeholder="Expliquez pourquoi vous supprimez ce club..."
            class="w-full px-3 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors resize-none"
            required
          ></textarea>
        </div>

        <div class="flex gap-3">
          <Button @click="closeDeleteModal" variant="outline" class="flex-1">
            Annuler
          </Button>
          <Button @click="confirmDelete" variant="secondary" class="flex-1" :disabled="!deleteReason.trim()">
            Supprimer
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';

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

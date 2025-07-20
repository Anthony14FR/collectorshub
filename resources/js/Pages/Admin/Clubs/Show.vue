<template>
  <Head title="Détail du Club" />
  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider flex items-center gap-2">
              <img :src="club.icon" :alt="club.name" class="w-10 h-10 rounded-full border-2 border-primary"> {{ club.name }}
            </h1>
            <p class="text-xs mt-4 text-base-content/70 uppercase tracking-wider">
              Détails du club
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin/clubs">
              <Button variant="ghost" size="sm">← Retour à la liste</Button>
            </Link>
            <Button @click="openDeleteModal" variant="secondary" size="sm">
              🗑️ Supprimer
            </Button>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-auto p-4">
        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-1">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-6">
              <div class="text-center mb-6">
                <img :src="club.icon" :alt="club.name" class="w-10 h-10 rounded-full border-2 border-primary">
                <h2 class="text-xl font-bold">{{ club.name }}</h2>
                <p class="text-sm text-base-content/60">{{ club.members.length }} membre(s)</p>
              </div>

              <div class="space-y-4">
                <div>
                  <h3 class="font-medium mb-2">Description</h3>
                  <p class="text-sm text-base-content/80 bg-base-200/50 p-3 rounded-lg">
                    {{ club.description }}
                  </p>
                </div>

                <div>
                  <h3 class="font-medium mb-2">Statistiques</h3>
                  <div class="bg-base-200/50 p-3 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                      <span>CP Total:</span>
                      <span class="font-medium">{{ club.total_cp.toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span>Créé le:</span>
                      <span class="font-medium">{{ formatDate(club.created_at) }}</span>
                    </div>
                  </div>
                </div>

                <div>
                  <h3 class="font-medium mb-2">Chef du club</h3>
                  <div class="bg-primary/10 p-3 rounded-lg flex items-center gap-3">
                    <img :src="club.leader.avatar" :alt="club.leader.username"
                         class="w-10 h-10 rounded-full border-2 border-primary">
                    <div>
                      <p class="font-medium">{{ club.leader.username }}</p>
                      <p class="text-xs text-base-content/60">Niveau {{ club.leader.level }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-6">
              <h3 class="text-lg font-bold mb-4">Membres du club</h3>

              <div v-if="club.members.length === 0" class="text-center py-8 text-base-content/60">
                <p>Aucun membre dans ce club</p>
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="member in club.members" :key="member.id"
                     class="bg-base-200/50 p-4 rounded-lg flex items-center gap-3">
                  <img :src="member.avatar" :alt="member.username"
                       class="w-10 h-10 rounded-full border border-base-300">
                  <div class="flex-1">
                    <p class="font-medium">{{ member.username }}</p>
                    <p class="text-xs text-base-content/60">Niveau {{ member.level }}</p>
                  </div>
                  <div v-if="member.pivot.role === 'leader'"
                       class="px-2 py-1 bg-primary/20 text-primary text-xs rounded-full font-medium">
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

    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-base-100 rounded-xl border border-base-300 max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4 text-error">
          🗑️ Supprimer le club "{{ club.name }}"
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
import { Link, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';

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
    onError: (errors) => {
      console.error('Erreur:', errors);
    }
  });
};
</script>

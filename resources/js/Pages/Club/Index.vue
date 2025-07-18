<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import ClubCard from '@/Components/Cards/ClubCard.vue';

interface Club {
  id: number;
  name: string;
  description: string;
  icon: string;
  total_cp: number;
  leader: {
    username: string;
  };
  members: any[];
}

interface Props {
  clubs: Club[];
  userClub: Club | null;
  userPendingRequests: number[];
  canCreateClub: boolean;
}

const props = defineProps<Props>();

const searchQuery = ref('');
const sortBy = ref('total_cp');
const currentPage = ref(1);
const itemsPerPage = 9;

const sortOptions = [
  { value: 'total_cp', label: 'CP Total' },
  { value: 'members', label: 'Nombre de membres' },
  { value: 'name', label: 'Nom du club' }
];

const filteredClubs = computed(() => {
  let filtered = [...props.clubs];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(club => 
      club.name.toLowerCase().includes(query) ||
      club.description.toLowerCase().includes(query) ||
      club.leader.username.toLowerCase().includes(query)
    );
  }

  return filtered.sort((a, b) => {
    switch (sortBy.value) {
    case 'total_cp':
      return b.total_cp - a.total_cp;
    case 'members':
      return b.members.length - a.members.length;
    case 'name':
      return a.name.localeCompare(b.name);
    default:
      return 0;
    }
  });
});

const totalPages = computed(() => Math.ceil(filteredClubs.value.length / itemsPerPage));

const paginatedClubs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredClubs.value.slice(start, end);
});

const changePage = (page: number) => {
  currentPage.value = page;
};

const joinClub = (clubId: number) => {
  router.post(`/clubs/${clubId}/join`, {}, {
    preserveScroll: true
  });
};

const goBack = () => {
  router.visit('/me');
};

const resetFilters = () => {
  searchQuery.value = '';
  sortBy.value = 'total_cp';
  currentPage.value = 1;
};
</script> 

<template>
  <Head title="Clubs" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-8 px-4">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏆 CLUBS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            REJOIGNEZ OU CRÉEZ VOTRE CLUB
          </p>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-4 p-4 lg:p-8">
        <div class="w-full lg:w-64 order-1 lg:order-1">
          <Button
            @click="goBack"
            variant="secondary"
            size="sm"
            class="w-full mb-4"
          >
            ← Retour vers le menu
          </Button>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">🔍</span>
                FILTRES
              </h3>
            </div>

            <div class="p-3 space-y-3">
              <div>
                <label class="block text-xs font-medium text-base-content/70 mb-1">Rechercher</label>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Nom, description..."
                  class="w-full bg-base-200/50 border border-base-300/30 rounded-lg px-3 py-2 text-sm focus:border-info/50 focus:outline-none transition-colors"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-base-content/70 mb-1">Trier par</label>
                <select
                  v-model="sortBy"
                  class="w-full bg-base-200/50 border border-base-300/30 rounded-lg px-3 py-2 text-sm focus:border-info/50 focus:outline-none transition-colors"
                >
                  <option
                    v-for="option in sortOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>

              <Button
                @click="resetFilters"
                variant="secondary"
                size="sm"
                class="w-full"
              >
                🔄 Réinitialiser
              </Button>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📊</span>
                STATISTIQUES
              </h3>
            </div>
            
            <div class="p-3 space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Clubs total</span>
                <span class="text-sm font-bold text-success">{{ clubs.length }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70">Clubs trouvés</span>
                <span class="text-sm font-bold text-info">{{ filteredClubs.length }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex-1 order-2 lg:order-2">
          <div v-if="userClub" class="mb-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">👑</span>
                  VOTRE CLUB
                </h3>
              </div>
              
              <div class="p-4">
                <div class="flex items-center gap-4">
                  <img 
                    :src="userClub.icon" 
                    :alt="userClub.name" 
                    class="w-16 h-16 rounded-xl border-2 border-primary/30 bg-gradient-to-br from-primary/10 to-primary/5"
                  />
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-primary mb-1">{{ userClub.name }}</h3>
                    <p class="text-sm text-base-content/70 mb-2">{{ userClub.description }}</p>
                    <div class="flex gap-4 text-xs text-base-content/60">
                      <span>Chef: {{ userClub.leader.username }}</span>
                      <span>{{ userClub.members.length }}/30 membres</span>
                      <span>{{ userClub.total_cp.toLocaleString() }} CP total</span>
                    </div>
                  </div>
                  <Button
                    variant="secondary"
                    size="sm"
                    @click="router.visit(`/clubs/${userClub.id}`)"
                  >
                    Voir le Club
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">🏆</span>
                  CLUBS DISPONIBLES
                </h3>
                <Button
                  v-if="!userClub"
                  @click="router.visit('/clubs/create')"
                  variant="primary"
                  size="sm"
                >
                  ➕ Créer un Club
                </Button>
              </div>
            </div>

            <div class="p-4">
              <div v-if="paginatedClubs.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <ClubCard
                  v-for="club in paginatedClubs"
                  :key="club.id"
                  :club="club"
                  :show-join-button="!userClub && club.members.length < 30"
                  :has-pending-request="userPendingRequests.includes(club.id)"
                  @view="(id) => router.visit(`/clubs/${id}`)"
                  @join="joinClub"
                />
              </div>

              <div v-else class="text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-lg font-bold mb-2">Aucun club trouvé</h3>
                <p class="text-base-content/70 mb-4">Essayez de modifier vos critères de recherche.</p>
                <Button @click="resetFilters" variant="primary">
                  Réinitialiser les filtres
                </Button>
              </div>
            </div>

            <div v-if="totalPages > 1" class="bg-gradient-to-r from-info/10 to-info/5 px-4 py-3 border-t border-info/20">
              <div class="flex justify-center items-center gap-2">
                <Button
                  @click="changePage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  variant="ghost"
                  size="sm"
                >
                  ←
                </Button>

                <span v-for="page in totalPages" :key="page">
                  <Button
                    @click="changePage(page)"
                    :variant="currentPage === page ? 'primary' : 'ghost'"
                    size="sm"
                    class="min-w-[2rem]"
                  >
                    {{ page }}
                  </Button>
                </span>

                <Button
                  @click="changePage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  variant="ghost"
                  size="sm"
                >
                  →
                </Button>
              </div>
            </div>

            <div v-if="filteredClubs.length > 0" class="bg-gradient-to-r from-info/10 to-info/5 px-3 py-2 border-t border-info/20">
              <div class="text-xs text-center text-base-content/70">
                {{ filteredClubs.length }} club{{ filteredClubs.length > 1 ? 's' : '' }} trouvé{{ filteredClubs.length > 1 ? 's' : '' }}
                {{ totalPages > 1 ? ` - Page ${currentPage}/${totalPages}` : '' }}
              </div>
            </div>
          </div>
        </div>

        <div class="w-full lg:w-64 order-3 lg:order-3">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">⚙️</span>
                ACTIONS
              </h3>
            </div>

            <div class="p-3 space-y-2">
              <Button
                v-if="!userClub"
                @click="router.visit('/clubs/create')"
                variant="primary"
                size="sm"
                class="w-full"
              >
                🏗️ Créer un Club
              </Button>
              
              <Button
                v-if="userClub"
                @click="router.visit(`/clubs/${userClub.id}`)"
                variant="secondary"
                size="sm"
                class="w-full"
              >
                👑 Mon Club
              </Button>

              <Button
                @click="router.reload()"
                variant="ghost"
                size="sm"
                class="w-full"
              >
                🔄 Actualiser
              </Button>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">💡</span>
                GUIDE
              </h3>
            </div>
            
            <div class="p-3 space-y-2 text-xs text-base-content/70">
              <p>• <strong>Rejoindre :</strong> Cliquez sur un club pour voir les détails</p>
              <p>• <strong>Créer :</strong> Coûte 100,000 Cash</p>
              <p>• <strong>Maximum :</strong> 30 membres par club</p>
              <p>• <strong>CP Total :</strong> Somme des CP de tous les membres</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
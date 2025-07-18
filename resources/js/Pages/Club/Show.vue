<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import { getPokemonImageUrl } from '@/utils/marketplace';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Club, ClubRequest } from '@/types/club';

interface Props extends PageProps {
  club: Club & {
    leader: User;
    members: (User & {
      pokedex: Array<{
        id: number;
        cp: number;
        pokemon?: {
          id: number;
          name: string;
          image: string;
          is_shiny: boolean;
        };
      }>;
    })[];
  };
  isMember: boolean;
  isLeader: boolean;
  hasPendingRequest: boolean;
  canJoin: boolean;
  pendingRequests: (ClubRequest & {
    user: User;
  })[];
}

const {
  club,
  isMember,
  isLeader,
  hasPendingRequest,
  canJoin,
  pendingRequests
} = defineProps<Props>();

const showRequestsModal = ref(false);
const showRemoveMemberModal = ref(false);
const showLeaderActionsModal = ref(false);
const selectedMember = ref<User | null>(null);
const expandedMembers = ref<Set<number>>(new Set());
const currentPage = ref(1);
const itemsPerPage = 6;

const totalMembers = computed(() => club.members?.length || 0);
const totalTeamCP = computed(() => {
  if (!club.members) return 0;
  return club.members.reduce((total, member) => {
    const memberCP = (member.pokedex || []).filter(p => p.pokemon).reduce((memberCP, pokemon) => {
      const cp = (pokemon as any).cp || 0;
      return memberCP + cp;
    }, 0);
    return total + memberCP;
  }, 0);
});

const pendingRequestsCount = computed(() => pendingRequests?.length || 0);

const totalPages = computed(() => Math.ceil(totalMembers.value / itemsPerPage));

const paginatedMembers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return (club.members || []).slice(start, end);
});

const changePage = (page: number) => {
  currentPage.value = page;
};

const joinClub = () => {
  router.post(`/clubs/${club.id}/join`, {}, {
    preserveScroll: true
  });
};

const leaveClub = () => {
  router.post('/clubs/leave', {}, {
    onSuccess: () => {
      router.visit('/clubs');
    }
  });
};

const acceptRequest = (requestId: number) => {
  router.post(`/club-requests/${requestId}/accept`, {}, {
    preserveScroll: true
  });
};

const rejectRequest = (requestId: number) => {
  router.post(`/club-requests/${requestId}/reject`, {}, {
    preserveScroll: true
  });
};

const removeMember = (userId: number) => {
  router.post(`/clubs/${club.id}/remove-member`, {
    user_id: userId
  }, {
    preserveScroll: true
  });
  showRemoveMemberModal.value = false;
  selectedMember.value = null;
};

const openRemoveMemberModal = (member: User) => {
  selectedMember.value = member;
  showRemoveMemberModal.value = true;
};

const toggleMemberExpansion = (memberId: number) => {
  if (expandedMembers.value.has(memberId)) {
    expandedMembers.value.delete(memberId);
  } else {
    expandedMembers.value.add(memberId);
  }
};

const transferLeadership = (userId: number) => {
  router.post(`/clubs/${club.id}/transfer-leadership`, {
    user_id: userId
  }, {
    preserveScroll: true
  });
  showLeaderActionsModal.value = false;
};

const destroyClub = () => {
  if (confirm('Êtes-vous sûr de vouloir détruire ce club ? Cette action est irréversible.')) {
    router.delete(`/clubs/${club.id}`, {
      onSuccess: () => {
        router.visit('/clubs');
      }
    });
  }
};

const goBack = () => {
  router.visit('/clubs');
};

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('fr-FR').format(num);
};
</script>

<template>
  <Head :title="`Club ${club.name}`" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-8 px-4">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏆 {{ club.name.toUpperCase() }}
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            CLUB POKÉMON
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
            ← Retour aux clubs
          </Button>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
            <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">ℹ️</span>
                INFORMATIONS
              </h3>
            </div>

            <div class="p-4">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/10 rounded-xl border-2 border-primary/30 flex items-center justify-center">
                  <img 
                    v-if="club.icon && club.icon.startsWith('/')" 
                    :src="club.icon" 
                    :alt="club.name"
                    class="w-12 h-12 object-contain"
                    @error="(e) => { if (e.target) (e.target as HTMLElement).style.display = 'none' }"
                  />
                  <span v-else-if="club.icon && !club.icon.includes('/')">{{ club.icon }}</span>
                  <span v-else class="text-2xl">🏆</span>
                </div>
                <div class="flex-1 min-w-0">
                  <h2 class="font-bold text-lg text-primary truncate">{{ club.name }}</h2>
                  <p class="text-xs text-base-content/60">{{ club.leader.username }}</p>
                </div>
              </div>

              <p class="text-sm text-base-content/70 mb-4">{{ club.description }}</p>

              <div class="space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Membres</span>
                  <span class="text-sm font-bold text-primary">{{ totalMembers }}/30</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">CP Total</span>
                  <span class="text-sm font-bold text-warning">{{ formatNumber(totalTeamCP) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-xs text-base-content/70">Chef</span>
                  <span class="text-sm font-bold text-info">{{ club.leader.username }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">⚙️</span>
                ACTIONS
              </h3>
            </div>

            <div class="p-3 space-y-2">
              <Button
                v-if="canJoin && !isMember && !hasPendingRequest"
                @click="joinClub"
                variant="primary"
                size="sm"
                class="w-full"
              >
                ➕ Demander à Rejoindre
              </Button>
               
              <Button
                v-else-if="hasPendingRequest"
                variant="ghost"
                size="sm"
                class="w-full"
                disabled
              >
                📤 Demande Envoyée
              </Button>
               
              <Button
                v-if="isMember && !isLeader"
                @click="leaveClub"
                variant="outline"
                size="sm"
                class="w-full"
              >
                🚪 Quitter le Club
              </Button>
               
              <Button
                v-if="isLeader"
                @click="showLeaderActionsModal = true"
                variant="secondary"
                size="sm"
                class="w-full"
              >
                👑 Actions du Chef
              </Button>
               
              <Button
                v-if="isLeader && pendingRequestsCount > 0"
                @click="showRequestsModal = true"
                variant="secondary"
                size="sm"
                class="w-full"
              >
                📝 Demandes ({{ pendingRequestsCount }})
              </Button>
            </div>
          </div>
        </div>

        <div class="flex-1 order-2 lg:order-2">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">👥</span>
                MEMBRES DU CLUB
              </h3>
            </div>

            <div class="p-4">
              <div v-if="paginatedMembers.length > 0" class="space-y-3">
                <div
                  v-for="member in paginatedMembers"
                  :key="member.id"
                  @click="toggleMemberExpansion(member.id)"
                  class="bg-gradient-to-br from-base-200/50 to-base-300/30 backdrop-blur-sm border border-primary/20 rounded-xl p-4 hover:border-primary/40 transition-all duration-300 cursor-pointer"
                >
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 flex-shrink-0">
                      <Avatar 
                        :src="member.avatar || `/images/trainer/${(member.id % 10) + 1}.png`"
                        :alt="member.username"
                        :placeholder="member.username"
                        size="md" 
                      />
                      <div class="min-w-0">
                        <div class="font-bold text-base truncate flex items-center gap-2">
                          {{ member.username }}
                          <span v-if="member.id === club.leader_id" class="text-warning">👑</span>
                        </div>
                        <div class="text-sm text-base-content/70">
                          Niveau {{ member.level }}
                        </div>
                      </div>
                    </div>

                    <div class="flex-1 flex justify-end items-center gap-2">
                      <span class="text-sm font-medium text-base-content/70">⚔️ CP</span>
                      <CPBadge :cp="(member.pokedex || []).filter(p => p.pokemon).reduce((total, p) => {
                        const cp = (p as any).cp || 0;
                        return total + cp;
                      }, 0)" />
                      <svg
                        class="w-4 h-4 text-primary/60 transition-transform duration-300"
                        :class="{ 'rotate-180': expandedMembers.has(member.id) }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>

                    <div v-if="isLeader && member.id !== club.leader_id" class="flex-shrink-0">
                      <Button
                        @click.stop="openRemoveMemberModal(member)"
                        variant="outline"
                        size="sm"
                      >
                        🚫 Expulser
                      </Button>
                    </div>
                  </div>

                  <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-96"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 max-h-96"
                    leave-to-class="opacity-0 max-h-0"
                  >
                    <div v-if="expandedMembers.has(member.id)" class="mt-4 space-y-2 overflow-hidden">
                      <div class="grid grid-cols-6 gap-2">
                        <div
                          v-for="pokemon in (member.pokedex || []).filter(p => p.pokemon).slice(0, 6)"
                          :key="pokemon.id"
                          class="bg-gradient-to-br from-base-100/60 to-base-200/40 rounded-lg p-2 border border-primary/10"
                        >
                          <img
                            :src="getPokemonImageUrl({
                              id: pokemon.pokemon!.id,
                              name: pokemon.pokemon!.name,
                              is_shiny: pokemon.pokemon!.is_shiny
                            } as any)"
                            :alt="pokemon.pokemon?.name || 'Pokémon'"
                            class="w-full h-12 object-contain"
                          />
                          <div class="text-xs text-center mt-1 font-medium">
                            {{ formatNumber((pokemon as any).cp || 0) }}
                          </div>
                        </div>
                        <div
                          v-for="i in Math.max(0, 6 - (member.pokedex || []).filter(p => p.pokemon).length)"
                          :key="`empty-${i}`"
                          class="bg-gradient-to-br from-base-100/20 to-base-200/10 rounded-lg p-2 border border-dashed border-primary/20 flex items-center justify-center"
                        >
                          <span class="text-xs text-base-content/40">-</span>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <div class="text-6xl mb-4">👥</div>
                <p class="text-base-content/70 text-lg mb-2">Aucun membre dans ce club</p>
                <p class="text-base-content/50">Le club est vide pour le moment.</p>
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

            <div v-if="totalMembers > 0" class="bg-gradient-to-r from-info/10 to-info/5 px-3 py-2 border-t border-info/20">
              <div class="text-xs text-center text-base-content/70">
                {{ totalMembers }} membre{{ totalMembers > 1 ? 's' : '' }}
                {{ totalPages > 1 ? ` - Page ${currentPage}/${totalPages}` : '' }}
              </div>
            </div>
          </div>
        </div>

        <div class="w-full lg:w-64 order-3 lg:order-3">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
            <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📈</span>
                STATISTIQUES
              </h3>
            </div>
            
            <div class="p-3 space-y-3">
              <div class="bg-primary/10 rounded-xl p-3 text-center border border-primary/20">
                <div class="text-2xl font-bold text-primary">{{ totalMembers }}</div>
                <div class="text-xs text-primary/70 uppercase tracking-wide">Membres</div>
                <div class="text-xs text-base-content/60 mt-1">/ 30 maximum</div>
              </div>
              
              <div class="bg-warning/10 rounded-xl p-3 text-center border border-warning/20">
                <div class="text-2xl font-bold text-warning">{{ formatNumber(totalTeamCP) }}</div>
                <div class="text-xs text-warning/70 uppercase tracking-wide">CP Total</div>
                <div class="text-xs text-base-content/60 mt-1">Puissance collective</div>
              </div>
              
              <div class="bg-info/10 rounded-xl p-3 text-center border border-info/20">
                <div class="text-2xl font-bold text-info">{{ totalMembers > 0 ? Math.round(totalTeamCP / totalMembers) : 0 }}</div>
                <div class="text-xs text-info/70 uppercase tracking-wide">CP Moyen</div>
                <div class="text-xs text-base-content/60 mt-1">Par membre</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showRequestsModal" @close="showRequestsModal = false" max-width="2xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-info/20 to-info/40 rounded-lg flex items-center justify-center">
            <span class="text-lg">📝</span>
          </div>
          <div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent">
              Demandes d'Adhésion
            </h3>
            <p class="text-sm text-base-content/70">{{ pendingRequestsCount }} demande(s) en attente</p>
          </div>
        </div>
      </template>

      <div class="space-y-4">
        <div
          v-for="request in pendingRequests"
          :key="request.id"
          class="bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-xl p-4 border border-primary/20"
        >
          <div class="flex items-center gap-3 mb-3">
            <Avatar 
              :src="request.user.avatar || `/images/trainer/${(request.user.id % 10) + 1}.png`"
              :alt="request.user.username"
              :placeholder="request.user.username"
              size="md" 
            />
            <div class="flex-1">
              <div class="font-bold">{{ request.user.username }}</div>
              <div class="text-sm text-base-content/70">Niveau {{ request.user.level }}</div>
            </div>
          </div>
          
          <div class="flex gap-2">
            <Button
              @click="acceptRequest(request.id)"
              variant="success"
              size="sm"
            >
              ✅ Accepter
            </Button>
            <Button
              @click="rejectRequest(request.id)"
              variant="outline"
              size="sm"
            >
              ❌ Refuser
            </Button>
          </div>
        </div>

        <div v-if="pendingRequests.length === 0" class="text-center py-8">
          <div class="text-6xl mb-4">📭</div>
          <p class="text-base-content/70">Aucune demande en attente</p>
        </div>
      </div>
    </Modal>

    <Modal :show="showRemoveMemberModal" @close="showRemoveMemberModal = false" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-error/20 to-error/40 rounded-lg flex items-center justify-center">
            <span class="text-lg">⚠️</span>
          </div>
          <div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent">
              Expulser un Membre
            </h3>
          </div>
        </div>
      </template>

      <div v-if="selectedMember" class="text-center">
        <p class="text-lg mb-4">
          Êtes-vous sûr de vouloir expulser <strong>{{ selectedMember.username }}</strong> du club ?
        </p>
        <p class="text-base-content/70 mb-6">
          Cette action est irréversible.
        </p>
        
        <div class="flex gap-3 justify-center">
          <Button
            @click="removeMember(selectedMember.id)"
            variant="outline"
            size="md"
            class="!border-error !text-error hover:!bg-error hover:!text-white"
          >
            🚫 Confirmer l'Expulsion
          </Button>
          <Button
            @click="showRemoveMemberModal = false"
            variant="outline"
            size="md"
          >
            ❌ Annuler
          </Button>
        </div>
      </div>
    </Modal>

    <Modal :show="showLeaderActionsModal" @close="showLeaderActionsModal = false" max-width="2xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
            <span class="text-lg">👑</span>
          </div>
          <div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
              Actions du Chef
            </h3>
            <p class="text-sm text-base-content/70">Gérer le club et ses membres</p>
          </div>
        </div>
      </template>

      <div class="space-y-6">
        <div v-if="club.members && club.members.length > 1">
          <h4 class="text-lg font-bold mb-3">Transférer le Leadership</h4>
          <p class="text-base-content/70 mb-4">
            Choisissez un nouveau chef pour le club. Vous deviendrez membre du club après le transfert.
          </p>
          <div class="grid gap-2">
            <div
              v-for="member in club.members.filter(m => m.id !== club.leader_id)"
              :key="member.id"
              class="flex items-center justify-between p-3 bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-xl border border-primary/20"
            >
              <div class="flex items-center gap-3">
                <Avatar 
                  :src="member.avatar || `/images/trainer/${(member.id % 10) + 1}.png`"
                  :alt="member.username"
                  :placeholder="member.username"
                  size="sm" 
                />
                <div>
                  <div class="font-bold">{{ member.username }}</div>
                  <div class="text-sm text-base-content/70">Niveau {{ member.level }}</div>
                </div>
              </div>
              <Button
                @click="transferLeadership(member.id)"
                variant="primary"
                size="sm"
              >
                👑 Choisir comme Chef
              </Button>
            </div>
          </div>
        </div>

        <div v-if="club.members && club.members.length === 1">
          <h4 class="text-lg font-bold mb-3 text-error">Détruire le Club</h4>
          <p class="text-base-content/70 mb-4">
            Vous êtes le seul membre du club. Vous pouvez le détruire définitivement.
          </p>
          <Button
            @click="destroyClub"
            variant="outline"
            size="md"
            class="!border-error !text-error hover:!bg-error hover:!text-white"
          >
            🗑️ Détruire le Club
          </Button>
        </div>

        <div v-if="club.members && club.members.length === 1" class="text-center py-4">
          <div class="text-6xl mb-4">🏚️</div>
          <p class="text-base-content/70">Vous êtes le seul membre du club</p>
        </div>
      </div>
    </Modal>
  </div>
</template> 
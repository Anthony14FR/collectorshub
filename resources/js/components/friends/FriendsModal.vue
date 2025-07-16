<script setup lang="ts">
import { ref, watch } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import FriendsCard from "./FriendsCard.vue";
import Modal from "@/Components/UI/Modal.vue";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import Avatar from "@/Components/UI/Avatar.vue";
import Badge from "@/Components/UI/Badge.vue";
import type { UserFriend, FriendRequest } from "@/types/friend";
import type { User } from "@/types/user";

const props = defineProps<{
  show: boolean;
  onClose: () => void;
  friends: UserFriend[];
  friend_requests: FriendRequest[];
  suggestions: User[];
  currentUserId?: number;
}>();

const emit = defineEmits(['dataRefreshed']);

const searchQuery = ref("");
const searchResults = ref<User[]>([]);
const loadingSearch = ref(false);
const refreshingSuggestions = ref(false);
const refreshCountdown = ref(0);
const canRefresh = ref(true);
const activeTab = ref("friends");

const pendingRequests = ref<FriendRequest[]>([]);
const loadingPending = ref(false);
const hasSearched = ref(false);

const localFriends = ref<UserFriend[]>([]);
const localFriendRequests = ref<FriendRequest[]>([]);
const localSuggestions = ref<User[]>([]);

watch(() => props.friends, (newFriends) => {
  localFriends.value = [...newFriends];
}, { immediate: true });

watch(() => props.friend_requests, (newRequests) => {
  localFriendRequests.value = [...newRequests];
}, { immediate: true });

watch(() => props.suggestions, (newSuggestions) => {
  localSuggestions.value = [...newSuggestions.filter(user => user.id !== props.currentUserId)];
}, { immediate: true });



watch(
  () => props.show,
  (val) => {
    if (val) {
      localFriends.value = [...props.friends];
      localFriendRequests.value = [...props.friend_requests];
      localSuggestions.value = [...props.suggestions.filter(user => user.id !== props.currentUserId)];
      searchQuery.value = "";
      searchResults.value = [];
      hasSearched.value = false;
      activeTab.value = "friends";
      loadPendingRequests();
    }
  }
);

const handleSearch = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    hasSearched.value = false;
    return;
  }
  
  loadingSearch.value = true;
  hasSearched.value = true;
  
  try {
    const { data } = await axios.get("/friends/search", {
      params: { query: searchQuery.value.trim() },
    });
    
    const filtered = data.results.filter(user => user.id !== props.currentUserId);
    searchResults.value = filtered;
  } catch (error) {
    searchResults.value = [];
  } finally {
    loadingSearch.value = false;
  }
};

const REFRESH_COOLDOWN = 15;
const refreshSuggestions = async () => {
  if (!canRefresh.value || refreshingSuggestions.value) return;
  
  refreshingSuggestions.value = true;
  canRefresh.value = false;
  
  try {
    const { data } = await axios.post("/api/friends/refresh");
    
    if (data.success) {
      localFriends.value = data.friends;
      localFriendRequests.value = data.friend_requests;
      localSuggestions.value = data.suggestions.filter(user => user.id !== props.currentUserId);
      pendingRequests.value = data.pending_requests || [];
      
      emit('dataRefreshed', {
        friends: data.friends,
        friend_requests: data.friend_requests,
        suggestions: data.suggestions
      });
      
      refreshCountdown.value = REFRESH_COOLDOWN;
      startCountdown();
      
      if (searchQuery.value.trim()) {
        await handleSearch();
      }
    }
  } catch (error) {
    if (error.response?.status === 429) {
      const remainingTime = error.response.data.remaining_time || REFRESH_COOLDOWN;
      refreshCountdown.value = Math.ceil(remainingTime);
      startCountdown();
    } else {
      refreshCountdown.value = REFRESH_COOLDOWN;
      startCountdown();
    }
  } finally {
    refreshingSuggestions.value = false;
  }
};

const startCountdown = () => {
  const interval = setInterval(() => {
    refreshCountdown.value--;
    if (refreshCountdown.value <= 0) {
      clearInterval(interval);
      canRefresh.value = true;
      refreshCountdown.value = 0;
    }
  }, 1000);
};

const loadPendingRequests = async () => {
  loadingPending.value = true;
  
  try {
    const { data } = await axios.get("/api/friends/pending-requests");
    pendingRequests.value = data.requests || [];
  } catch (error) {
    pendingRequests.value = [];
  } finally {
    loadingPending.value = false;
  }
};

const handleClose = () => {
  props.onClose();
};

const acceptRequest = (requesterId) => {
  const acceptedRequest = localFriendRequests.value.find(req => req.user.id === requesterId);
  
  router.post(
    "/friends/accept-request",
    { requester_id: requesterId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        localFriendRequests.value = localFriendRequests.value.filter(
          req => req.user.id !== requesterId
        );
        
        if (acceptedRequest) {
          localFriends.value.push({
            id: acceptedRequest.user.id,
            username: acceptedRequest.user.username,
            level: acceptedRequest.user.level,
            avatar: acceptedRequest.user.avatar,
            hasSentGiftToday: false,
            hasGiftToClaim: false,
            giftId: null
          });
        }
        
        emit('dataRefreshed', {
          friends: localFriends.value,
          friend_requests: localFriendRequests.value,
          suggestions: localSuggestions.value
        });
      }
    }
  );
};

const refuseRequest = (targetId) => {
  router.post(
    "/friends/remove",
    { target_id: targetId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        localFriendRequests.value = localFriendRequests.value.filter(
          req => req.user.id !== targetId
        );
      }
    }
  );
};

const sendFriendRequest = (targetId) => {
  const userToAdd = searchResults.value.find(u => u.id === targetId) || 
    localSuggestions.value.find(u => u.id === targetId);
  
  searchResults.value = searchResults.value.filter(
    (user) => user.id !== targetId
  );
  localSuggestions.value = localSuggestions.value.filter(
    (user) => user.id !== targetId
  );
  
  router.post(
    "/friends/send-request",
    { target_id: targetId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        if (userToAdd) {
          pendingRequests.value.push({
            id: Date.now(),
            user: {
              id: userToAdd.id,
              username: userToAdd.username,
              level: userToAdd.level,
              avatar: userToAdd.avatar
            }
          });
        }
      }
    }
  );
};

const cancelPendingRequest = (targetId) => {
  router.post(
    "/friends/remove",
    { target_id: targetId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        pendingRequests.value = pendingRequests.value.filter(
          (req) => req.user.id !== targetId
        );
      }
    }
  );
};

const sendGift = (friendId) => {
  router.post(
    "/friend-gifts/send",
    { receiver_id: friendId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        const friendIndex = localFriends.value.findIndex(f => f.id === friendId);
        if (friendIndex !== -1) {
          const now = new Date();
          const nextAvailableTime = new Date(now.getTime() + 24 * 60 * 60 * 1000);
          localFriends.value[friendIndex] = {
            ...localFriends.value[friendIndex],
            isOnCooldown: true,
            nextGiftAvailableAt: nextAvailableTime.toISOString(),
          };
        }
      }
    }
  );
};

const claimGift = (giftId) => {
  router.post(
    "/friend-gifts/claim",
    { gift_id: giftId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        const friendIndex = localFriends.value.findIndex(f => f.giftId === giftId);
        if (friendIndex !== -1) {
          localFriends.value[friendIndex].hasGiftToClaim = false;
          localFriends.value[friendIndex].giftId = null;
        }
      }
    }
  );
};

const claimAllGifts = () => {
  router.post(
    "/friend-gifts/claim-all",
    {},
    { 
      preserveScroll: true,
      onSuccess: () => {
        localFriends.value.forEach(friend => {
          if (friend.hasGiftToClaim) {
            friend.hasGiftToClaim = false;
            friend.giftId = null;
          }
        });
      }
    }
  );
};

const sendGiftToAll = () => {
  router.post(
    "/friend-gifts/send-all",
    {},
    { 
      preserveScroll: true,
      onSuccess: () => {
        const now = new Date();
        localFriends.value = localFriends.value.map(friend => {
          if (!friend.isOnCooldown) {
            const nextAvailableTime = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            return {
              ...friend,
              isOnCooldown: true,
              nextGiftAvailableAt: nextAvailableTime.toISOString(),
            };
          } else {
            return friend;
          }
        });
      }
    }
  );
};

const removeFriend = (friendId) => {
  router.post(
    "/friends/remove",
    { target_id: friendId },
    { 
      preserveScroll: true,
      onSuccess: () => {
        localFriends.value = localFriends.value.filter(f => f.id !== friendId);
      }
    }
  );
};

const getAvatarSrc = (user: User) => {
  return user.avatar || `/images/trainer/${(user.id % 10) + 1}.png`;
};
</script>

<template>
  <Modal :show="props.show" @close="handleClose" max-width="4xl">
    <template #header>
      <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
          <span class="text-2xl">👥</span>
        </div>
        <div class="flex-1 text-center sm:text-left">
          <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Mes Amis
          </h3>
          <p class="text-sm text-base-content/70">
            Gérez vos amis et envoyez des cadeaux
          </p>
        </div>
        <Button
          v-if="activeTab === 'search'"
          @click="refreshSuggestions"
          :loading="refreshingSuggestions"
          :disabled="!canRefresh"
          size="sm"
          variant="secondary"
          class="mt-2 sm:mt-0 sm:mr-10 w-full sm:w-auto"
        >
          <span v-if="refreshCountdown > 0">
            🔄 Attendre {{ refreshCountdown }}s
          </span>
          <span v-else>
            🔄 Rafraîchir
          </span>
        </Button>
      </div>
    </template>

    <template #tabs>
      <div class="flex flex-wrap justify-center sm:justify-start">
        <button 
          @click="activeTab = 'friends'" 
          class="px-2 sm:px-4 py-2 sm:py-3 text-sm font-medium transition-colors duration-200 flex items-center gap-2"
          :class="activeTab === 'friends' ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-base-content'"
        >
          <span>👥</span>
          Amis ({{ props.friends.length }})
        </button>
        
        <button 
          @click="activeTab = 'requests'" 
          class="px-2 sm:px-4 py-2 sm:py-3 text-sm font-medium transition-colors duration-200 flex items-center gap-2"
          :class="activeTab === 'requests' ? 'text-warning border-b-2 border-warning' : 'text-base-content/70 hover:text-base-content'"
        >
          <span>📨</span>
          Demandes reçues ({{ props.friend_requests.length }})
          <Badge v-if="props.friend_requests.length > 0" variant="warning" size="xs">{{ props.friend_requests.length }}</Badge>
        </button>
        
        <button 
          @click="activeTab = 'pending'" 
          class="px-2 sm:px-4 py-2 sm:py-3 text-sm font-medium transition-colors duration-200 flex items-center gap-2"
          :class="activeTab === 'pending' ? 'text-info border-b-2 border-info' : 'text-base-content/70 hover:text-base-content'"
        >
          <span>⏳</span>
          En attente ({{ pendingRequests.length }})
          <Badge v-if="pendingRequests.length > 0" variant="info" size="xs">{{ pendingRequests.length }}</Badge>
        </button>
        
        <button 
          @click="activeTab = 'search'" 
          class="px-2 sm:px-4 py-2 sm:py-3 text-sm font-medium transition-colors duration-200 flex items-center gap-2"
          :class="activeTab === 'search' ? 'text-success border-b-2 border-success' : 'text-base-content/70 hover:text-base-content'"
        >
          <span>🔍</span>
          Rechercher
        </button>
      </div>
    </template>

    <template #default>
      <div class="space-y-6 px-2 sm:px-0">
        <div v-if="activeTab === 'friends'">
          <div v-if="localFriends.length > 0" class="space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <h4 class="text-lg font-semibold text-base-content text-center sm:text-left">Mes amis</h4>
              <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4">
                <div class="flex items-center gap-4 text-sm text-base-content/70">
                  <div class="flex items-center gap-1">
                    <span class="text-success">🎁</span>
                    <span>{{ localFriends.filter(f => f.hasGiftToClaim).length }} cadeaux</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="text-warning">✉️</span>
                    <span>{{ localFriends.filter(f => f.hasSentGiftToday).length }} envoyés</span>
                  </div>
                </div>
                
                <div class="flex gap-2 w-full sm:w-auto">
                  <Button
                    v-if="localFriends.filter(f => f.hasGiftToClaim).length > 0"
                    @click="claimAllGifts"
                    variant="success"
                    size="sm"
                    class="w-full sm:w-auto"
                  >
                    🎁 Tout récupérer
                  </Button>
                  
                  <Button
                    v-if="localFriends.filter(f => !f.hasSentGiftToday).length > 0"
                    @click="sendGiftToAll"
                    variant="primary"
                    size="sm"
                    class="w-full sm:w-auto"
                  >
                    💝 Envoyer à tous
                  </Button>
                </div>
              </div>
            </div>
            
            <div class="grid gap-3 grid-cols-1">
              <FriendsCard
                v-for="friend in localFriends"
                :key="friend.id"
                :friend="friend"
                @send-gift="sendGift"
                @claim-gift="claimGift"
                @remove-friend="removeFriend"
              />
            </div>
          </div>
          
          <div v-else class="flex flex-col items-center justify-center py-10 sm:py-16 text-center">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full flex items-center justify-center mb-4">
              <span class="text-3xl sm:text-4xl">👥</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-base-content mb-2">Aucun ami pour le moment</h3>
            <p class="text-base-content/70 mb-4">Commencez à ajouter des amis pour partager des cadeaux !</p>
            <Button @click="activeTab = 'search'" variant="primary" class="w-full sm:w-auto">
              Rechercher des amis
            </Button>
          </div>
        </div>

        <div v-if="activeTab === 'requests'">
          <div v-if="localFriendRequests.length > 0" class="space-y-4">
            <h4 class="text-lg font-semibold text-base-content">Demandes reçues</h4>
            
            <div class="space-y-3">
              <div
                v-for="req in localFriendRequests"
                :key="req.id"
                class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 sm:p-4 border border-warning/30 hover:border-warning/50 transition-all duration-300 shadow-lg"
              >
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                  <Avatar 
                    :src="getAvatarSrc(req.user)"
                    :alt="req.user.username"
                    size="md"
                    gradient
                  />
                  
                  <div class="flex-1 text-center sm:text-left">
                    <div class="flex items-center gap-2 mb-1 justify-center sm:justify-start">
                      <h3 class="font-bold text-base-content">{{ req.user.username }}</h3>
                      <Badge variant="warning" size="xs">Demande</Badge>
                    </div>
                    <p class="text-sm text-base-content/70">Niveau {{ req.user.level }}</p>
                  </div>
                  
                  <div class="flex gap-2 w-full sm:w-auto justify-center sm:justify-end">
                    <Button
                      size="sm"
                      variant="success"
                      @click="acceptRequest(req.user.id)"
                      class="w-1/2 sm:w-auto"
                    >
                      ✅ Accepter
                    </Button>
                    <Button
                      size="sm"
                      variant="error"
                      @click="refuseRequest(req.user.id)"
                      class="w-1/2 sm:w-auto"
                    >
                      ❌ Refuser
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="flex flex-col items-center justify-center py-10 sm:py-16 text-center">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-info/20 to-info/40 rounded-full flex items-center justify-center mb-4">
              <span class="text-3xl sm:text-4xl">📨</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-base-content mb-2">Aucune demande</h3>
            <p class="text-base-content/70">Vous n'avez pas de demande d'ami en attente.</p>
          </div>
        </div>

        <div v-if="activeTab === 'pending'">
          <div v-if="loadingPending" class="flex justify-center py-8">
            <div class="text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
              <p class="text-sm text-base-content/70">Chargement...</p>
            </div>
          </div>
          
          <div v-else-if="pendingRequests.length > 0" class="space-y-4">
            <h4 class="text-lg font-semibold text-base-content">Demandes envoyées</h4>
            
            <div class="space-y-3">
              <div
                v-for="req in pendingRequests"
                :key="req.id"
                class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 sm:p-4 border border-info/30 hover:border-info/50 transition-all duration-300 shadow-lg"
              >
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                  <Avatar 
                    :src="getAvatarSrc(req.user)"
                    :alt="req.user.username"
                    size="md"
                    gradient
                  />
                  
                  <div class="flex-1 text-center sm:text-left">
                    <div class="flex items-center gap-2 mb-1 justify-center sm:justify-start">
                      <h3 class="font-bold text-base-content">{{ req.user.username }}</h3>
                      <Badge variant="info" size="xs">En attente</Badge>
                    </div>
                    <p class="text-sm text-base-content/70">Niveau {{ req.user.level }}</p>
                  </div>
                  
                  <div class="flex gap-2 w-full sm:w-auto justify-center sm:justify-end">
                    <Button
                      size="sm"
                      variant="error"
                      @click="cancelPendingRequest(req.user.id)"
                      class="w-full sm:w-auto"
                    >
                      ❌ Annuler
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="flex flex-col items-center justify-center py-10 sm:py-16 text-center">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-info/20 to-info/40 rounded-full flex items-center justify-center mb-4">
              <span class="text-3xl sm:text-4xl">⏳</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-base-content mb-2">Aucune demande en attente</h3>
            <p class="text-base-content/70">Vous n'avez envoyé aucune demande d'ami en attente.</p>
          </div>
        </div>

        <div v-if="activeTab === 'search'">
          <div class="space-y-6">
            <div class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 sm:p-4 border border-info/30">
              <div class="flex flex-col sm:flex-row gap-3">
                <Input
                  v-model="searchQuery"
                  placeholder="Rechercher par pseudo..."
                  @keyup.enter="handleSearch"
                  class="flex-1"
                />
                <Button
                  @click="handleSearch"
                  :loading="loadingSearch"
                  variant="info"
                  class="w-full sm:w-auto"
                >
                  🔍 Rechercher
                </Button>
              </div>
            </div>

            <div v-if="searchResults.length > 0" class="space-y-4">
              <h4 class="text-lg font-semibold text-base-content">Résultats de la recherche</h4>
              <div class="space-y-3">
                <div
                  v-for="user in searchResults"
                  :key="user.id"
                  class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 sm:p-4 border border-base-300/30 hover:border-primary/50 transition-all duration-300 shadow-lg"
                >
                  <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                    <Avatar 
                      :src="getAvatarSrc(user)"
                      :alt="user.username"
                      size="md"
                      gradient
                    />
                    
                    <div class="flex-1 text-center sm:text-left">
                      <h3 class="font-bold text-base-content">{{ user.username }}</h3>
                      <p class="text-sm text-base-content/70">Niveau {{ user.level }}</p>
                    </div>
                    
                    <Button
                      size="sm"
                      variant="primary"
                      @click="sendFriendRequest(user.id)"
                      class="w-full sm:w-auto"
                    >
                      ➕ Ajouter
                    </Button>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="hasSearched && !loadingSearch && searchResults.length === 0" class="space-y-4">
              <div class="flex flex-col items-center justify-center py-10 sm:py-12 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-error/20 to-error/30 rounded-full flex items-center justify-center mb-4">
                  <span class="text-3xl sm:text-4xl">🔍</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-base-content mb-2">Aucun utilisateur trouvé</h3>
                <p class="text-base-content/70 mb-4">
                  Aucun utilisateur ne correspond à "{{ searchQuery }}"
                </p>
                <Button 
                  @click="searchQuery = ''; hasSearched = false;"
                  variant="secondary"
                  size="sm"
                  class="w-full sm:w-auto"
                >
                  Effacer la recherche
                </Button>
              </div>
            </div>

            <div v-if="localSuggestions.length > 0" class="space-y-4">
              <h4 class="text-lg font-semibold text-base-content">Suggestions d'amis</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div
                  v-for="user in localSuggestions"
                  :key="user.id"
                  class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-3 sm:p-4 border border-base-300/30 hover:border-primary/50 transition-all duration-300 shadow-lg group"
                >
                  <div class="flex flex-col items-center text-center gap-3">
                    <Avatar 
                      :src="getAvatarSrc(user)"
                      :alt="user.username"
                      size="lg"
                      gradient
                    />
                    
                    <div>
                      <h3 class="font-bold text-base-content">{{ user.username }}</h3>
                      <p class="text-sm text-base-content/70">Niveau {{ user.level }}</p>
                    </div>
                    
                    <Button
                      size="sm"
                      variant="primary"
                      @click="sendFriendRequest(user.id)"
                      class="w-full"
                    >
                      ➕ Ajouter
                    </Button>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else-if="!loadingSearch && !searchResults.length" class="flex flex-col items-center justify-center py-10 sm:py-16 text-center">
              <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-info/20 to-info/40 rounded-full flex items-center justify-center mb-4">
                <span class="text-3xl sm:text-4xl">🔍</span>
              </div>
              <h3 class="text-lg sm:text-xl font-bold text-base-content mb-2">Recherchez des amis</h3>
              <p class="text-base-content/70">Utilisez la barre de recherche pour trouver d'autres joueurs !</p>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>
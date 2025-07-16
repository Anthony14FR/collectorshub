<script setup lang="ts">
import { ref, watch } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import FriendsCard from "./FriendsCard.vue";
import Modal from "@/Components/UI/Modal.vue";
import Button from "@/Components/UI/Button.vue";
import Input from "@/Components/UI/Input.vue";
import type { UserFriend, FriendRequest } from "@/types/friend";
import type { User } from "@/types/user";

const props = defineProps<{
  show: boolean;
  onClose: () => void;
  friends: UserFriend[];
  friend_requests: FriendRequest[];
  suggestions: User[];
}>();

const searchQuery = ref("");
const searchResults = ref<User[]>([]);
const loadingSearch = ref(false);
const refreshingSuggestions = ref(false);

const suggestions = ref<User[]>([...props.suggestions]);

watch(
  () => props.show,
  (val) => {
    if (val) {
      suggestions.value = [...props.suggestions];
    }
  }
);

const handleSearch = async () => {
  if (!searchQuery.value) return;
  loadingSearch.value = true;
  try {
    const { data } = await axios.get("/friends/search", {
      params: { query: searchQuery.value },
    });
    searchResults.value = data.results;
  } finally {
    loadingSearch.value = false;
  }
};

const refreshSuggestions = async () => {
  refreshingSuggestions.value = true;
  try {
    const { data } = await axios.get("/api/friends/suggestions");
    suggestions.value = data.suggestions;
    if (searchQuery.value) {
      await handleSearch();
    }
  } finally {
    refreshingSuggestions.value = false;
  }
};

const handleClose = () => {
  props.onClose();
};

const acceptRequest = (requesterId) => {
  router.post(
    "/friends/accept-request",
    { requester_id: requesterId },
    { preserveScroll: true }
  );
};

const refuseRequest = (targetId) => {
  router.post(
    "/friends/remove",
    { target_id: targetId },
    { preserveScroll: true }
  );
};

const sendFriendRequest = (targetId) => {
  searchResults.value = searchResults.value.filter(
    (user) => user.id !== targetId
  );
  suggestions.value = suggestions.value.filter(
    (user) => user.id !== targetId
  );
  router.post(
    "/friends/send-request",
    { target_id: targetId },
    { preserveScroll: true }
  );
};

const sendGift = (friendId) => {
  router.post(
    "/friend-gifts/send",
    { receiver_id: friendId },
    { preserveScroll: true }
  );
};

const claimGift = (giftId) => {
  router.post(
    "/friend-gifts/claim",
    { gift_id: giftId },
    { preserveScroll: true }
  );
};
</script>

<template>
  <Modal :show="props.show" @close="handleClose" max-width="2xl">
    <template #header>
      <div class="flex items-center gap-2">
        <span class="font-bold text-lg">Mes amis</span>
        <Button
          @click="refreshSuggestions"
          :loading="refreshingSuggestions"
          size="xs"
          variant="secondary"
        >
          Rafraîchir suggestions
        </Button>
      </div>
    </template>
    <template #default>
      <div class="flex flex-col gap-6">
        <div class="flex gap-2 items-center">
          <Input
            v-model="searchQuery"
            placeholder="Rechercher par pseudo ou email..."
            @keyup.enter="handleSearch"
          />
          <Button
            @click="handleSearch"
            :loading="loadingSearch"
            size="sm"
          >
            Rechercher
          </Button>
        </div>
        <div
          v-if="searchResults.length"
          class="bg-base-200 rounded p-2 mt-2"
        >
          <div
            v-for="user in searchResults"
            :key="user.id"
            class="flex items-center gap-2 justify-between py-1"
          >
            <span>{{ user.username }} ({{ user.email }})</span>
            <Button
              size="xs"
              variant="primary"
              @click="sendFriendRequest(user.id)"
            >
              Ajouter
            </Button>
          </div>
        </div>

        <div>
          <div class="font-semibold mb-2">Suggestions d'amis</div>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="user in suggestions"
              :key="user.id"
              class="bg-base-200 rounded p-2 flex items-center gap-2"
            >
              <span>{{ user.username }}</span>
              <Button
                size="xs"
                variant="primary"
                @click="sendFriendRequest(user.id)"
              >
                Ajouter
              </Button>
            </div>
          </div>
        </div>

        <div v-if="props.friend_requests && props.friend_requests.length">
          <div class="font-semibold mb-2">Demandes reçues</div>
          <div class="flex flex-col gap-2">
            <div
              v-for="req in props.friend_requests"
              :key="req.id"
              class="flex items-center gap-2 justify-between"
            >
              <span>{{ req.user.username }}</span>
              <div class="flex gap-1">
                <Button
                  size="xs"
                  variant="success"
                  @click="acceptRequest(req.user.id)"
                >
                  Accepter
                </Button>
                <Button
                  size="xs"
                  variant="error"
                  @click="refuseRequest(req.user.id)"
                >
                  Refuser
                </Button>
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="font-semibold mb-2">Mes amis</div>
          <div class="flex flex-col gap-2">
            <FriendsCard
              v-for="friend in props.friends"
              :key="friend.id"
              :friend="friend"
              @send-gift="sendGift"
              @claim-gift="claimGift"
            />
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>

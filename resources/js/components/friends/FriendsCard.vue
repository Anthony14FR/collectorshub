<script setup lang="ts">
import { computed } from "vue";
import Button from "@/Components/UI/Button.vue";
import Avatar from "@/Components/UI/Avatar.vue";
import Badge from "@/Components/UI/Badge.vue";
import type { UserFriend } from '@/types/friend';

const props = defineProps<{
  friend: UserFriend;
}>();

const emit = defineEmits(["send-gift", "claim-gift", "remove-friend"]);

const canSendGift = computed(() => !props.friend.hasSentGiftToday);
const canClaimGift = computed(() => props.friend.hasGiftToClaim);

const getAvatarSrc = (friend: UserFriend) => {
  return friend.avatar || `/images/trainer/${friend.id % 10 + 1}.png`;
};
</script>

<template>
  <div class="group relative bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-4 border border-base-300/30 shadow-lg transition-all duration-300">
    <div v-if="canClaimGift" class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-success/80 to-success rounded-full flex items-center justify-center animate-pulse shadow-lg">
      <span class="text-white text-xs font-bold">!</span>
    </div>

    <div v-if="!canSendGift" class="absolute -top-2 -left-2 w-6 h-6 bg-gradient-to-br from-warning/80 to-warning rounded-full flex items-center justify-center shadow-lg">
      <span class="text-white text-xs">✓</span>
    </div>

    <div class="flex items-center gap-4">
      <div class="relative">
        <Avatar 
          :src="getAvatarSrc(friend)"
          :alt="friend.username"
          size="lg"
          :ring="canClaimGift"
          :ring-color="canClaimGift ? 'success' : 'primary'"
          gradient
        />
        
        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-gradient-to-br from-info/90 to-info rounded-full flex items-center justify-center border-2 border-base-100 shadow-lg">
          <span class="text-white text-xs font-bold">{{ friend.level }}</span>
        </div>
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="font-bold text-base-content truncate">{{ friend.username }}</h3>
          <Badge v-if="canClaimGift" variant="success" size="xs" pulse>
            Cadeau
          </Badge>
        </div>
        
        <div class="flex items-center gap-2 text-sm text-base-content/70">
          <div class="flex items-center gap-1">
            <span class="text-info">⭐</span>
            <span>Niveau {{ friend.level }}</span>
          </div>
          
          <div v-if="!canSendGift" class="flex items-center gap-1">
            <span class="text-warning">✉️</span>
            <span class="text-xs">Envoyé</span>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <Button
          v-if="canClaimGift"
          size="sm"
          variant="success"
          @click="emit('claim-gift', friend.giftId)"
          class="animate-pulse"
        >
          <span class="flex items-center gap-1">
            <span>🎁</span>
            Récupérer
          </span>
        </Button>
        
        <Button
          v-if="canSendGift"
          size="sm"
          variant="primary"
          @click="emit('send-gift', friend.id)"
        >
          <span class="flex items-center gap-1">
            <span>💝</span>
            Envoyer
          </span>
        </Button>
        
        <Button
          v-if="!canSendGift && !canClaimGift"
          size="sm"
          variant="secondary"
          disabled
        >
          <span class="flex items-center gap-1">
            <span>⏰</span>
            Demain
          </span>
        </Button>
        
        <Button
          size="xs"
          variant="error"
          @click="emit('remove-friend', friend.id)"
          class="mt-1"
        >
          <span class="flex items-center gap-1">
            <span>🗑️</span>
            Supprimer
          </span>
        </Button>
      </div>
    </div>

  </div>
</template>
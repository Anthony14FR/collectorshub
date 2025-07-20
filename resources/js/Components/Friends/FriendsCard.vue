<script setup lang="ts">
import FriendGiftCountdown from "@/Components/Friends/FriendGiftCountdown.vue";
import Avatar from "@/Components/UI/Avatar.vue";
import Badge from "@/Components/UI/Badge.vue";
import Button from "@/Components/UI/Button.vue";
import type { UserFriend } from '@/types/friend';
import { Link } from "@inertiajs/vue3";
import { AlertCircle, Check, Gift, Heart, Star, Trash2 } from 'lucide-vue-next';
import { computed } from "vue";

const props = defineProps<{
  friend: UserFriend;
}>();

const emit = defineEmits(["send-gift", "claim-gift", "remove-friend"]);

const canClaimGift = computed(() => props.friend.hasGiftToClaim);

const handleCountdownExpired = () => {
  props.friend.isOnCooldown = false;
  props.friend.nextGiftAvailableAt = null;
};

const getAvatarSrc = (friend: UserFriend) => {
  return friend.avatar || `/images/trainer/${(friend.id % 10) + 1}.png`;
};
</script>

<template>
  <div class="group relative bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-xl p-4 border border-base-300/30 shadow-lg transition-all duration-300">
    <div v-if="canClaimGift" class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-success/80 to-success rounded-full flex items-center justify-center animate-pulse shadow-lg">
      <AlertCircle :size="12" class="text-white" />
    </div>

    <div v-if="props.friend.isOnCooldown && props.friend.nextGiftAvailableAt" class="absolute -top-2 -left-2 w-6 h-6 bg-gradient-to-br from-warning/80 to-warning rounded-full flex items-center justify-center shadow-lg">
      <Check :size="12" class="text-white" />
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-4 text-center sm:text-left">
      <div class="relative flex-shrink-0 mb-2 sm:mb-0">
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

      <div class="flex-1 min-w-0 mb-2 sm:mb-0">
        <div class="flex items-center gap-2 mb-1 justify-center sm:justify-start">
          <Link :href="`/profile/${friend.username}`" class="font-bold text-base-content truncate text-primary hover:underline">
            {{ friend.username }}
          </Link>
          <Badge v-if="canClaimGift" variant="success" size="xs" pulse>
            Cadeau
          </Badge>
        </div>
        
        <div class="flex items-center gap-2 text-sm text-base-content/70 justify-center sm:justify-start">
          <div class="flex items-center gap-1">
            <Star :size="16" class="text-info" />
            <span>Niveau {{ friend.level }}</span>
          </div>
        </div>
      </div>

      <div class="flex flex-row sm:flex-col gap-2 w-full sm:w-auto justify-center sm:justify-end">
        <Button
          v-if="canClaimGift"
          size="sm"
          variant="success"
          @click="emit('claim-gift', friend.giftId)"
          class="animate-pulse w-full sm:w-auto"
        >
          <span class="flex items-center gap-1">
            <Gift :size="16" />
            Récupérer
          </span>
        </Button>
        
        <Button
          v-else-if="!props.friend.isOnCooldown"
          size="sm"
          variant="primary"
          @click="emit('send-gift', friend.id)"
          class="w-full sm:w-auto"
        >
          <span class="flex items-center gap-1">
            <Heart :size="16" />
            Envoyer
          </span>
        </Button>
        
        <div
          v-else
          class="w-full sm:w-auto flex flex-col items-center justify-center p-2 rounded-lg bg-base-200"
        >
          <FriendGiftCountdown :next-gift-available-at="props.friend.nextGiftAvailableAt" @expired="handleCountdownExpired" />
        </div>
        
        <Button
          size="sm"
          variant="secondary"
          @click="emit('remove-friend', friend.id)"
          class="mt-1 w-full sm:w-auto"
        >
          <span class="flex items-center gap-1">
            <Trash2 :size="16" />
            Supprimer
          </span>
        </Button>
      </div>
    </div>

  </div>
</template>
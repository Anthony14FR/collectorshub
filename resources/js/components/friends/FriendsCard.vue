<script setup lang="ts">
import { computed } from "vue";
import Button from "@/Components/UI/Button.vue";
import type { UserFriend } from '@/types/friend';

const props = defineProps<{
  friend: UserFriend;
}>();

const emit = defineEmits(["send-gift", "claim-gift"]);

const canSendGift = computed(() => !props.friend.hasSentGiftToday);
const canClaimGift = computed(() => props.friend.hasGiftToClaim);
</script>

<template>
  <div class="flex items-center gap-3 bg-base-100 rounded p-2 shadow">
    <div class="flex-1">
      <div class="font-bold">{{ friend.username }}</div>
      <div class="text-xs text-base-content/70">
        Niveau {{ friend.level }}
      </div>
    </div>
    <div class="flex gap-2 items-center">
      <Button
        v-if="canClaimGift"
        size="sm"
        variant="success"
        @click="emit('claim-gift', friend.giftId)"
      >
        Récupérer
      </Button>
      <Button
        v-if="canSendGift"
        size="sm"
        variant="primary"
        @click="emit('send-gift', friend.id)"
      >
        Envoyer
      </Button>
    </div>
    <div v-if="canClaimGift" class="ml-2">
      <span
        class="inline-block w-3 h-3 bg-error rounded-full animate-pulse"
      ></span>
    </div>
  </div>
</template>

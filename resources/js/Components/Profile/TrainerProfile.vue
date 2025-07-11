<script setup lang="ts">
import type { User } from '@/types/user';
import Button from '@/Components/UI/Button.vue';

interface Props {
  user: User;
  trainerImageId?: number;
  onOpenPokedexModal?: () => void;
}

const { user, trainerImageId = 2, onOpenPokedexModal } = defineProps<Props>();
</script>

<template>
  <div class="flex flex-col items-center space-y-4">
    <div class="w-48 h-48">
      <img
        :src="
          user.avatar
            ? user.avatar
            : `/images/trainer/${trainerImageId}.png`
        "
        :alt="user.username"
        class="w-full h-full object-cover"
        style="image-rendering: pixelated"
      />
    </div>

    <div class="text-center">
      <h2 class="text-3xl font-bold text-base-content mb-2">
        {{ user.username }}
      </h2>
      <div class="text-lg text-base-content/70">Maître Dresseur</div>
      <Button
        v-if="onOpenPokedexModal"
        @click="onOpenPokedexModal"
        variant="secondary"
        size="sm"
        class="mt-4"
      >
        🛡️ Gérer l'équipe
      </Button>
    </div>
  </div>
</template>

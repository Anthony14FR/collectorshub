<script setup lang="ts">
import type { User } from '@/types/user';

interface Props {
    user: User;
    trainerImageId?: number;
}

const { user, trainerImageId = 2 } = defineProps<Props>();
</script>

<template>
    <div class="flex flex-col items-center space-y-4">
        <!-- Avatar du dresseur -->
        <div class="w-48 h-48">
            <img 
                :src="`/images/trainer/${trainerImageId}.png`" 
                :alt="user.username" 
                class="w-full h-full object-cover"
                style="image-rendering: pixelated;"
            />
        </div>
        
        <!-- Infos du dresseur -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-base-content mb-2">
                {{ user.username }}
            </h2>
            <div class="text-lg text-base-content/70">
                Maître Dresseur
            </div>
        </div>
    </div>
</template> 
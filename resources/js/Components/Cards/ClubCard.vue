<template>
  <div class="group relative overflow-hidden bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg border-2 border-primary/20 rounded-2xl shadow-lg shadow-primary/10 hover:shadow-xl hover:shadow-primary/20 transition-all duration-300 hover:-translate-y-1">
    <div class="absolute inset-0 bg-gradient-to-r from-primary/0 via-primary/5 to-primary/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
    
    <div class="relative p-6">
      <div class="flex items-center gap-4 mb-4">
        <img :src="club.icon" :alt="club.name" class="w-12 h-12 rounded-xl border-2 border-primary/30">
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-primary">{{ club.name }}</h3>
          <p class="text-sm text-base-content/60">{{ club.leader.username }}</p>
        </div>
      </div>
      
      <p class="text-base-content/70 text-sm mb-4 line-clamp-2">{{ club.description }}</p>
      
      <div class="flex justify-between items-center text-sm text-base-content/60 mb-4">
        <span>{{ club.members.length }}/30 membres</span>
        <span>{{ club.total_cp.toLocaleString() }} CP</span>
      </div>

      <div class="flex gap-2">
        <Button
          variant="secondary"
          size="sm"
          class="flex-1"
          @click="$emit('view', club.id)"
        >
          Voir
        </Button>
        <Button
          v-if="showJoinButton && !hasPendingRequest"
          variant="success"
          size="sm"
          class="flex-1"
          @click="$emit('join', club.id)"
        >
          Demander à Rejoindre
        </Button>
        <Button
          v-else-if="hasPendingRequest"
          variant="secondary"
          size="sm"
          class="flex-1"
          disabled
        >
          Demande Envoyée
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/Components/UI/Button.vue'

interface Club {
  id: number
  name: string
  description: string
  icon: string
  total_cp: number
  leader: {
    username: string
  }
  members: any[]
}

interface Props {
  club: Club
  showJoinButton?: boolean
  hasPendingRequest?: boolean
}

defineProps<Props>()

defineEmits<{
  view: [id: number]
  join: [id: number]
}>()
</script> 
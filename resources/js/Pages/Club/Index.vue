<template>
  <div class="min-h-screen bg-gradient-to-br from-base-100 via-base-200 to-base-100">
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gradient-to-br from-info/20 to-info/40 rounded-xl flex items-center justify-center">
            <span class="text-2xl">🏆</span>
          </div>
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent">
              Clubs
            </h1>
            <p class="text-base-content/60 text-sm">Rejoignez ou créez votre club</p>
          </div>
        </div>
        <div class="flex gap-4">
          <Button
            v-if="!userClub"
            variant="primary"
            size="md"
            @click="router.visit('/clubs/create')"
          >
            <span class="text-lg mr-2">➕</span>
            Créer un Club
          </Button>
        </div>
      </div>

      <div v-if="userClub" class="mb-8">
        <Alert type="info" variant="ghost" class="mb-4">
          <template #icon>
            <span class="text-lg">👑</span>
          </template>
          <div class="flex items-center gap-4">
            <img :src="userClub.icon" :alt="userClub.name" class="w-16 h-16 rounded-xl border-2 border-info/30">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-info">{{ userClub.name }}</h3>
              <p class="text-base-content/70 text-sm">{{ userClub.description }}</p>
              <div class="flex gap-4 mt-2 text-xs text-base-content/60">
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
        </Alert>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ClubCard
          v-for="club in clubs"
          :key="club.id"
          :club="club"
          :show-join-button="!userClub && club.members.length < 30"
          :has-pending-request="userPendingRequests.includes(club.id)"
          @view="(id) => router.visit(`/clubs/${id}`)"
          @join="joinClub"
        />
      </div>

      <div v-if="clubs.length === 0" class="text-center py-12">
        <div class="w-24 h-24 bg-gradient-to-br from-base-content/10 to-base-content/5 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">🏆</span>
        </div>
        <p class="text-base-content/60 text-lg">Aucun club disponible</p>
        <p class="text-base-content/40 text-sm mt-2">Soyez le premier à créer un club !</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import Button from '@/Components/UI/Button.vue'
import Alert from '@/Components/UI/Alert.vue'
import ClubCard from '@/Components/Cards/ClubCard.vue'

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
  clubs: Club[]
  userClub: Club | null
  userPendingRequests: number[]
  canCreateClub: boolean
}

const props = defineProps<Props>()

const joinClub = (clubId: number) => {
  router.post(`/clubs/${clubId}/join`, {}, {
    preserveScroll: true
  })
}
</script> 
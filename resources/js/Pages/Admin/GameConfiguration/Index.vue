<template>
  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            🎮 CONFIGURATION DU JEU
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Paramètres de récompenses et probabilités
          </p>
        </div>
      </div>

      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📊</span>
              STATISTIQUES
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Catégories</span>
              <span class="text-sm font-bold text-info">{{ Object.keys(configurations).length }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Configurations</span>
              <span class="text-sm font-bold text-success">{{ totalConfigs }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Modifiées</span>
              <span class="text-sm font-bold text-warning">{{ modifiedConfigs }}</span>
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🛠️</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button @click="goBack" variant="secondary" size="sm" class="w-full">
              ← Retour au dashboard
            </Button>
            <Button @click="clearAllCache" variant="outline" size="sm" class="w-full">
              🗑️ Vider le cache
            </Button>
            <Button @click="exportConfigs" variant="outline" size="sm" class="w-full">
              📤 Exporter
            </Button>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">ℹ️</span>
              INFORMATIONS
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="text-xs text-base-content/70 leading-relaxed">
              Les modifications sont appliquées immédiatement et mises en cache pour les performances.
            </div>
            <div class="text-xs text-base-content/70 leading-relaxed">
              Utilisez le bouton "Vider le cache" pour forcer le rechargement des configurations.
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚠️</span>
              ATTENTION
            </h3>
          </div>
          <div class="p-3">
            <div class="text-xs text-error leading-relaxed">
              Les modifications affectent directement l'équilibrage du jeu. Testez avant de déployer en production.
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[800px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">⚙️</span>
                CONFIGURATIONS DISPONIBLES
              </h3>
              <div class="flex gap-2">
                <Button @click="expandAll" variant="outline" size="sm">
                  📖 Tout ouvrir
                </Button>
                <Button @click="collapseAll" variant="outline" size="sm">
                  📕 Tout fermer
                </Button>
              </div>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <div class="space-y-4">
              <div v-for="(configs, category) in configurations" :key="category" class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <div class="flex items-center justify-between">
                    <button 
                      @click="toggleCategory(category)"
                      class="flex items-center gap-2 transition-all duration-200 w-full text-left p-2 rounded-lg cursor-pointer group"
                    >
                      <span class="text-lg">{{ getCategoryIcon(category) }}</span>
                      <h4 class="text-sm font-bold tracking-wider flex-1">{{ categories[category] }}</h4>
                      <span class="text-xs text-base-content/70 bg-base-300/50 px-2 py-1 rounded-full">{{ configs.length }}</span>
                      <span class="text-xs transition-all duration-300 ease-in-out" :class="expandedCategories.includes(category) ? 'rotate-180 text-primary' : 'text-base-content/50'">
                        ▼
                      </span>
                    </button>
                    <Button @click="resetCategory(category)" variant="outline" size="sm">
                      🔄 Reset
                    </Button>
                  </div>
                </div>

                <div 
                  v-show="expandedCategories.includes(category)" 
                  class="p-3 space-y-3 animate-in slide-in-from-top-2 duration-300"
                >
                  <div v-for="(config, index) in configs" :key="config.id" class="bg-base-100/50 rounded-lg p-3 border border-base-300/20 transition-all duration-200">
                    <div class="flex items-start justify-between mb-2">
                      <div>
                        <h5 class="text-sm font-bold text-base-content">{{ config.key }}</h5>
                        <p class="text-xs text-base-content/70">{{ config.description }}</p>
                      </div>
                      <div class="flex gap-1">
                        <Button @click="editConfig(config)" variant="primary" size="sm">
                          ✏️ Modifier
                        </Button>
                      </div>
                    </div>

                    <div class="bg-base-300/30 rounded p-2">
                      <ConfigPreview :config="config" />
                    </div>
                  </div>
                </div>
                
                <div v-if="!expandedCategories.includes(category)" class="p-4 text-center">
                  <div class="text-xs text-base-content/50 italic flex items-center justify-center gap-2 animate-pulse">
                    <span>👆</span>
                    Cliquez pour voir {{ configs.length }} configuration{{ configs.length > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="shrink-0 bg-gradient-to-r from-primary/10 to-secondary/5 px-3 py-2 border-t border-primary/20">
            <div class="text-xs text-center text-base-content/70">
              {{ totalConfigs }} configurations dans {{ Object.keys(configurations).length }} catégories
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="editingConfig" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-base-100 rounded-xl border border-base-300 w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold flex items-center gap-2">
              <span class="text-xl">✏️</span>
              Modifier {{ editingConfig.key }}
            </h3>
            <Button @click="closeEdit" variant="ghost" size="sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </Button>
          </div>
          <p class="text-sm text-base-content/70 mt-1">{{ editingConfig.description }}</p>
        </div>

        <div class="p-4 overflow-y-auto max-h-[calc(90vh-120px)]">
          <ConfigEditor 
            :config="editingConfig" 
            :value="editValue"
            @update:value="editValue = $event"
            @save="saveConfig"
            @cancel="closeEdit"
          />
        </div>
      </div>
    </div>

    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'alert transition-all duration-300 transform max-w-md',
          notification.show ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0',
          notification.type === 'success' ? 'bg-green-100 border-green-500 text-green-800' :
          notification.type === 'error' ? 'bg-red-100 border-red-500 text-red-800' :
          notification.type === 'warning' ? 'alert-warning' :
          'alert-info'
        ]"
      >
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <svg
              v-if="notification.type === 'success'"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg
              v-else-if="notification.type === 'error'"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <svg
              v-else-if="notification.type === 'warning'"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <svg
              v-else
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ notification.message }}</span>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="btn btn-ghost btn-xs ml-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import ConfigPreview from './ConfigPreview.vue'
import ConfigEditor from './ConfigEditor.vue'

interface GameConfig {
  id: number
  category: string
  key: string
  value: any
  description: string
}

interface Props {
  configurations: Record<string, GameConfig[]>
  categories: Record<string, string>
  flash?: {
    success?: string
    error?: string
  }
}

const props = defineProps<Props>()

const editingConfig = ref<GameConfig | null>(null)
const editValue = ref<any>({})
const expandedCategories = ref<string[]>([])

watch(() => props.flash, (flash) => {
  if (flash?.success) {
    showNotification('success', flash.success)
  }
  if (flash?.error) {
    showNotification('error', flash.error)
  }
}, { immediate: true })

const notifications = ref<Array<{
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
  show: boolean
}>>([])

const totalConfigs = computed(() => {
  return Object.values(props.configurations).reduce((total, configs) => total + configs.length, 0)
})

const modifiedConfigs = computed(() => {
  return totalConfigs.value
})

const getCategoryIcon = (category: string): string => {
  const icons: Record<string, string> = {
    'level_rewards': '🎯',
    'rarity_probabilities': '🎲',
    'xp_rewards': '⭐'
  }
  return icons[category] || '⚙️'
}

const showNotification = (type: 'success' | 'error' | 'warning' | 'info', message: string) => {
  const id = Date.now().toString()
  const notification = {
    id,
    type,
    message,
    show: true
  }
  
  notifications.value.push(notification)
  
  setTimeout(() => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value[index].show = false
      setTimeout(() => {
        notifications.value.splice(index, 1)
      }, 300)
    }
  }, 4000)
}

const removeNotification = (id: string) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value[index].show = false
    setTimeout(() => {
      notifications.value.splice(index, 1)
    }, 300)
  }
}

const editConfig = (config: GameConfig) => {
  editingConfig.value = config
  editValue.value = JSON.parse(JSON.stringify(config.value))
}

const closeEdit = () => {
  editingConfig.value = null
  editValue.value = {}
}

const saveConfig = async () => {
  if (!editingConfig.value) return

  try {
    await router.post('/admin/game-configuration/update', {
      category: editingConfig.value.category,
      key: editingConfig.value.key,
      value: editValue.value
    })

    closeEdit()
  } catch (error) {
    showNotification('error', 'Erreur lors de la sauvegarde')
  }
}



const resetCategory = async (category: string) => {
  if (!confirm('Êtes-vous sûr de vouloir réinitialiser toutes les configurations de cette catégorie ?')) return

  try {
    await router.post('/admin/game-configuration/reset-category', {
      category: category
    })
  } catch (error) {
    showNotification('error', 'Erreur lors de la réinitialisation')
  }
}

const goBack = () => {
  router.visit('/admin')
}

const clearAllCache = async () => {
  try {
    await router.post('/admin/cache/clear')
    showNotification('success', 'Cache vidé avec succès')
  } catch (error) {
    showNotification('error', 'Erreur lors du vidage du cache')
  }
}

const exportConfigs = () => {
  const dataStr = JSON.stringify(props.configurations, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'game-configurations.json'
  link.click()
  URL.revokeObjectURL(url)
  
  showNotification('success', 'Configurations exportées')
}

const toggleCategory = (category: string) => {
  const index = expandedCategories.value.indexOf(category)
  if (index > -1) {
    expandedCategories.value.splice(index, 1)
  } else {
    expandedCategories.value.push(category)
  }
}

const expandAll = () => {
  expandedCategories.value = Object.keys(props.configurations)
}

const collapseAll = () => {
  expandedCategories.value = []
}
</script> 
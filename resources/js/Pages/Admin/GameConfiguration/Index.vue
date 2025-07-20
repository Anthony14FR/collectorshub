<template>
  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Zap" :size="28" class="inline align-middle mr-2" />
            CONFIGURATION DU JEU
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Paramètres de récompenses et probabilités
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
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
                <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Zap" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="goBack" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour au dashboard
                  </Button>
                  <Button @click="clearAllCache" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Trash2" :size="16" class="mr-2" /> Vider le cache
                  </Button>
                  <Button @click="exportConfigs" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Download" :size="16" class="mr-2" /> Exporter
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Info" :size="18" />
                    INFORMATIONS
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Les modifications sont appliquées immédiatement et mises en cache pour les performances.
                  </div>
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Utilisez le bouton "Vider le cache" pour forcer le rechargement des configurations.
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="AlertTriangle" :size="18" />
                    ATTENTION
                  </h3>
                </div>
                <div class="p-4">
                  <div class="text-xs text-warning leading-relaxed">
                    Les modifications affectent directement l'équilibrage du jeu. Testez avant de déployer en production.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Settings" :size="18" />
                    CONFIGURATIONS DISPONIBLES
                  </h3>
                  <div class="flex gap-2">
                    <Button @click="expandAll" variant="outline" size="sm">
                      <component :is="ChevronDown" :size="16" class="mr-2" /> Tout ouvrir
                    </Button>
                    <Button @click="collapseAll" variant="outline" size="sm">
                      <component :is="ChevronUp" :size="16" class="mr-2" /> Tout fermer
                    </Button>
                  </div>
                </div>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-4">
                  <div v-for="(configs, category) in configurations" :key="category" class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 overflow-hidden">
                    <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                      <div class="flex items-center justify-between">
                        <button
                          @click="toggleCategory(category)"
                          class="flex items-center gap-2 transition-all duration-200 w-full text-left p-2 rounded-lg cursor-pointer group"
                        >
                          <component :is="getCategoryIcon(category)" :size="18" />
                          <h4 class="text-sm font-bold tracking-wider flex-1">{{ categories[category] }}</h4>
                          <span class="text-xs text-base-content/70 bg-base-300/50 px-2 py-1 rounded-full">{{ configs.length }}</span>
                          <component :is="ChevronDown" :size="14" class="text-xs transition-all duration-300 ease-in-out" :class="expandedCategories.includes(category) ? 'rotate-180 text-primary' : 'text-base-content/50'" />
                        </button>
                        <Button @click="resetCategory(category)" variant="outline" size="sm">
                          <component :is="RotateCcw" :size="16" class="mr-2" /> Reset
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
                              <component :is="Edit" :size="16" class="mr-2" /> Modifier
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
                        <component :is="MousePointer" :size="14" />
                        Cliquez pour voir {{ configs.length }} configuration{{ configs.length > 1 ? 's' : '' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="shrink-0 bg-gradient-to-r from-primary/10 to-secondary/5 px-4 py-3 border-t border-primary/20">
                <div class="text-xs text-center text-base-content/70">
                  {{ totalConfigs }} configurations dans {{ Object.keys(configurations).length }} catégories
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="editingConfig" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-base-100/95 backdrop-blur-md rounded-xl border border-base-300/30 shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold flex items-center gap-3 text-base-content">
                <component :is="Edit" :size="24" class="text-primary" />
                Modifier {{ editingConfig.key }}
              </h3>
              <p class="text-sm text-base-content/70 mt-2">{{ editingConfig.description }}</p>
            </div>
            <Button @click="closeEdit" variant="ghost" size="sm">
              <component :is="X" :size="20" />
            </Button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)] bg-base-100/50">
          <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-6">
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
            <component :is="getNotificationIcon(notification.type)" :size="20" />
            <span class="text-sm font-medium">{{ notification.message }}</span>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="btn btn-ghost btn-xs ml-2"
          >
            <component :is="X" :size="16" />
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
import { Zap, ArrowLeft, Trash2, Download, BarChart3, Info, AlertTriangle, Settings, ChevronDown, ChevronUp, RotateCcw, Edit, MousePointer, X, CheckCircle, AlertCircle, Target, Sparkles, Star, Trophy } from 'lucide-vue-next'

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

const getCategoryIcon = (category: string) => {
  const icons: Record<string, any> = {
    'level_rewards': Target,
    'rarity_probabilities': Sparkles,
    'xp_rewards': Star,
    'shiny_rate': Trophy
  }
  return icons[category] || Settings
}

const getNotificationIcon = (type: string) => {
  const icons: Record<string, any> = {
    'success': CheckCircle,
    'error': X,
    'warning': AlertTriangle,
    'info': Info
  }
  return icons[type] || Info
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

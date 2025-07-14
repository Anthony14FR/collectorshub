<template>
  <Modal :show="show" max-width="5xl" @close="$emit('close')">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-warning/30 to-warning/40 rounded-lg flex items-center justify-center">
          <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
          </svg>
        </div>
        <div>
          <h3 class="text-base font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
            {{ expedition?.name }}
          </h3>
          <p class="text-xs text-base-content/60">Sélectionnez {{ maxPokemons }} Pokémons</p>
        </div>
      </div>
    </template>

    <div class="space-y-3">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <div class="bg-base-100/80 backdrop-blur-sm rounded-lg p-3 border border-info/20">
          <h4 class="font-semibold text-xs mb-2 flex items-center gap-2 text-info">
            <div class="w-1.5 h-1.5 bg-info rounded-full"></div>
            Exigences
          </h4>
          <div class="max-h-16 overflow-y-auto scrollbar-thin scrollbar-thumb-info/20">
            <div class="space-y-1">
              <div v-for="req in expedition?.requirements" :key="req.id" 
                   class="flex items-center gap-2 text-xs">
                <input 
                  type="checkbox" 
                  :checked="isRequirementMet(req)"
                  disabled
                  class="w-2.5 h-2.5 rounded border-info/30 flex-shrink-0"
                  :class="isRequirementMet(req) ? 'bg-success border-success' : 'bg-base-200 border-base-300'"
                >
                <span class="font-medium flex-shrink-0">{{ req.quantity }}</span>
                <span class="text-base-content/70 truncate">{{ getRequirementText(req) }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-base-100/80 backdrop-blur-sm rounded-lg p-3 border border-success/20">
          <h4 class="font-semibold text-xs mb-2 flex items-center gap-2 text-success">
            <div class="w-1.5 h-1.5 bg-success rounded-full"></div>
            Sélection: {{ selectedPokemons.length }}/{{ maxPokemons }}
          </h4>
          <div v-if="selectedPokemons.length > 0" class="grid grid-cols-6 gap-1 max-h-12 overflow-y-auto">
            <div v-for="pokemon in selectedPokemonDetails" :key="pokemon.id" 
                 class="relative w-6 h-6 bg-base-200/50 rounded-md flex items-center justify-center border border-base-300/30 group cursor-pointer"
                 @click="removePokemon(pokemon.id)">
              <img :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon?.id}.gif`" 
                   :alt="pokemon.pokemon?.name" 
                   class="w-5 h-5 object-contain"
                   style="image-rendering: pixelated;">
              <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-md flex items-center justify-center">
                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
            </div>
          </div>
          <p v-else class="text-xs text-base-content/60">Aucun sélectionné</p>
        </div>

        <div class="bg-base-100/80 backdrop-blur-sm rounded-lg p-3 border border-primary/20">
          <h4 class="font-semibold text-xs mb-2 flex items-center gap-2 text-primary">
            <div class="w-1.5 h-1.5 bg-primary rounded-full"></div>
            Actions
          </h4>
          <div class="flex gap-2">
            <Button 
              @click="autoSelectPokemons"
              variant="secondary" 
              size="sm"
              class="flex-1 text-xs"
              :disabled="selectedPokemons.length === maxPokemons || loading"
            >
              🤖 Auto
            </Button>
            <Button 
              @click="clearSelection"
              variant="ghost" 
              size="sm"
              class="flex-1 text-xs"
              :disabled="selectedPokemons.length === 0 || loading"
            >
              🗑️ Reset
            </Button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <Input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher..."
          size="sm"
        />
        
        <Select
          v-model="rarityFilter"
          :options="rarityOptions"
          size="sm"
        />
        
        <Select
          v-model="typeFilter"
          :options="typeOptions"
          size="sm"
        />
      </div>

      <div v-if="filteredPokemons.length === 0" class="text-center py-6">
        <div class="w-12 h-12 bg-base-200/50 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="font-semibold text-base-content/60 mb-1 text-sm">Aucun Pokémon éligible</h3>
        <p class="text-xs text-base-content/40">Modifiez vos filtres</p>
      </div>
      
      <div v-else class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-transparent">
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-2 p-2">
          <div
            v-for="pokemon in filteredPokemons"
            :key="pokemon.id"
            @click="togglePokemon(pokemon)"
            class="group relative bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-lg border transition-all duration-200 cursor-pointer hover:scale-105 flex flex-col p-2"
            :class="[
              selectedPokemons.includes(pokemon.id) 
                ? 'border-success/50 bg-gradient-to-br from-success/10 to-success/5 shadow-md shadow-success/20' 
                : 'border-base-300/30 hover:border-primary/40'
            ]"
          >
            <div class="absolute top-1 right-1 z-10">
              <div v-if="selectedPokemons.includes(pokemon.id)" 
                   class="w-3 h-3 bg-success rounded-full flex items-center justify-center">
                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>

            <div v-if="selectedPokemons.includes(pokemon.id)" 
                 class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center z-20"
                 @click.stop="removePokemon(pokemon.id)">
              <div class="w-5 h-5 bg-black/80 rounded-full flex items-center justify-center">
                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
            </div>

            <div class="flex-1 flex items-center justify-center mb-1">
              <img 
                :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon?.id}.gif`" 
                :alt="pokemon.pokemon?.name" 
                class="w-8 h-8 object-contain group-hover:scale-110 transition-transform duration-200"
                style="image-rendering: pixelated;"
              >
            </div>

            <div class="text-center">
              <div class="text-xs font-semibold text-base-content truncate mb-1">
                {{ pokemon.nickname || pokemon.pokemon?.name }}
              </div>
              <div class="flex items-center justify-center gap-1 mb-1">
                <div class="px-1 py-0.5 rounded text-xs font-medium" 
                     :class="getRarityBadgeClass(pokemon.pokemon?.rarity)">
                  {{ getRarityLabel(pokemon.pokemon?.rarity) }}
                </div>
              </div>
              <div class="text-xs text-base-content/60">
                Niv. {{ pokemon.level }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-base-300/30 pt-3">
        <div class="text-center mb-3">
          <div class="flex items-center justify-center gap-2 mb-1">
            <div class="w-1.5 h-1.5 rounded-full" 
                 :class="selectedPokemons.length === maxPokemons ? 'bg-success animate-pulse' : 'bg-warning'"></div>
            <p class="text-sm font-medium" 
               :class="selectedPokemons.length === maxPokemons ? 'text-success' : 'text-base-content/70'">
              {{ selectedPokemons.length }} / {{ maxPokemons }} sélectionnés
            </p>
          </div>
          <div v-if="selectedPokemons.length < maxPokemons" class="text-xs text-base-content/50">
            {{ maxPokemons - selectedPokemons.length }} de plus nécessaire{{ maxPokemons - selectedPokemons.length > 1 ? 's' : '' }}
          </div>
        </div>
        
        <div class="flex gap-3">
          <Button 
            variant="ghost" 
            @click="$emit('close')"
            class="flex-1"
            size="sm"
          >
            Annuler
          </Button>
          
          <Button 
            variant="primary" 
            @click="startExpedition"
            :disabled="selectedPokemons.length !== maxPokemons || loading"
            :loading="loading"
            class="flex-2"
            size="sm"
          >
            {{ loading ? 'Démarrage...' : `🚀 Démarrer (${selectedPokemons.length})` }}
          </Button>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Modal from '@/Components/UI/Modal.vue'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'
import Select from '@/Components/UI/Select.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  expedition: {
    type: Object,
    default: null
  },
  eligiblePokemons: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'success', 'error'])

const page = usePage()
const selectedPokemons = ref([])
const searchQuery = ref('')
const rarityFilter = ref('all')
const typeFilter = ref('all')
const loading = ref(false)

const rarityOptions = [
  { value: 'all', label: 'Toutes les raretés' },
  { value: 'normal', label: 'Normal' },
  { value: 'rare', label: 'Rare' },
  { value: 'epic', label: 'Épique' },
  { value: 'legendary', label: 'Légendaire' }
]

const typeOptions = computed(() => {
  const types = new Set()
  props.eligiblePokemons?.forEach(pokemon => {
    if (pokemon.pokemon?.types) {
      pokemon.pokemon.types.forEach(type => {
        const typeName = typeof type === 'object' ? type.name : type
        types.add(typeName)
      })
    }
  })
  
  return [
    { value: 'all', label: 'Tous les types' },
    ...Array.from(types).map(type => ({ value: type, label: type }))
  ]
})

const filteredPokemons = computed(() => {
  let filtered = props.eligiblePokemons || []
  
  if (searchQuery.value) {
    filtered = filtered.filter(pokemon => 
      pokemon.pokemon?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      pokemon.nickname?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  if (rarityFilter.value !== 'all') {
    filtered = filtered.filter(pokemon => 
      pokemon.pokemon?.rarity === rarityFilter.value
    )
  }
  
  if (typeFilter.value !== 'all') {
    filtered = filtered.filter(pokemon => {
      if (!pokemon.pokemon?.types) return false
      return pokemon.pokemon.types.some(type => {
        const typeName = typeof type === 'object' ? type.name : type
        return typeName === typeFilter.value
      })
    })
  }
  
  return filtered
})

const selectedPokemonDetails = computed(() => {
  return selectedPokemons.value.map(id => 
    props.eligiblePokemons?.find(p => p.id === id)
  ).filter(Boolean)
})

const maxPokemons = computed(() => {
  return props.expedition?.requirements?.reduce((sum, req) => sum + req.quantity, 0) || 0
})

const isRequirementMet = (requirement) => {
  const selectedDetails = selectedPokemonDetails.value
  
  switch (requirement.type) {
  case 'rarity':
    return selectedDetails.filter(p => p.pokemon?.rarity === requirement.value).length >= requirement.quantity
  case 'type':
    return selectedDetails.filter(p => 
      p.pokemon?.types?.some(type => {
        const typeName = typeof type === 'object' ? type.name : type
        return typeName === requirement.value
      })
    ).length >= requirement.quantity
  case 'level':
    return selectedDetails.filter(p => p.level >= requirement.value).length >= requirement.quantity
  default:
    return false
  }
}

const autoSelectPokemons = () => {
  if (!props.expedition?.requirements || selectedPokemons.value.length === maxPokemons.value) return
  
  clearSelection()
  
  const availablePokemons = [...(props.eligiblePokemons || [])]
  const selected = []
  
  for (const requirement of props.expedition.requirements) {
    let neededCount = requirement.quantity
    let candidatePokemons = []
    
    switch (requirement.type) {
    case 'rarity':
      candidatePokemons = availablePokemons.filter(p => 
        p.pokemon?.rarity === requirement.value && !selected.includes(p.id)
      )
      break
    case 'type':
      candidatePokemons = availablePokemons.filter(p => 
        p.pokemon?.types?.some(type => {
          const typeName = typeof type === 'object' ? type.name : type
          return typeName === requirement.value
        }) && !selected.includes(p.id)
      )
      break
    case 'level':
      candidatePokemons = availablePokemons.filter(p => 
        p.level >= requirement.value && !selected.includes(p.id)
      )
      break
    default:
      candidatePokemons = availablePokemons.filter(p => !selected.includes(p.id))
    }
    
    candidatePokemons.sort((a, b) => {
      const rarityOrder = { normal: 1, rare: 2, epic: 3, legendary: 4 }
      const rarityA = rarityOrder[a.pokemon?.rarity] || 1
      const rarityB = rarityOrder[b.pokemon?.rarity] || 1
      
      if (rarityA !== rarityB) return rarityA - rarityB
      return a.level - b.level
    })
    
    for (let i = 0; i < Math.min(neededCount, candidatePokemons.length); i++) {
      selected.push(candidatePokemons[i].id)
    }
  }
  
  selectedPokemons.value = selected.slice(0, maxPokemons.value)
}

const clearSelection = () => {
  selectedPokemons.value = []
}

const togglePokemon = (pokemon) => {
  const index = selectedPokemons.value.indexOf(pokemon.id)
  if (index > -1) {
    selectedPokemons.value.splice(index, 1)
  } else {
    if (selectedPokemons.value.length < maxPokemons.value) {
      selectedPokemons.value.push(pokemon.id)
    }
  }
}

const removePokemon = (pokemonId) => {
  const index = selectedPokemons.value.indexOf(pokemonId)
  if (index > -1) {
    selectedPokemons.value.splice(index, 1)
  }
}

const startExpedition = async () => {
  loading.value = true
  
  try {
    const response = await fetch(`/api/expeditions/${props.expedition.id}/start`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        pokemon_ids: selectedPokemons.value
      })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      emit('success', data.message)
      emit('close')
    } else {
      const errorMessage = data.message || `Erreur ${response.status}: ${response.statusText}`
      emit('error', errorMessage)
    }
  } catch (error) {
    console.error('Erreur:', error)
    emit('error', 'Erreur de connexion ou de traitement')
  } finally {
    loading.value = false
  }
}

const getRequirementText = (req) => {
  switch (req.type) {
  case 'rarity':
    return `Pokémon ${req.value}`
  case 'type':
    return `de type ${req.value}`
  case 'level':
    return `niveau ${req.value}+`
  default:
    return `${req.type} ${req.value}`
  }
}

const getRarityBadgeClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-base-300/30 text-base-content/70'
  case 'rare': return 'bg-info/20 text-info'
  case 'epic': return 'bg-secondary/20 text-secondary'
  case 'legendary': return 'bg-warning/20 text-warning'
  default: return 'bg-base-300/30 text-base-content/70'
  }
}

const getRarityLabel = (rarity) => {
  switch (rarity) {
  case 'normal': return 'N'
  case 'rare': return 'R'
  case 'epic': return 'E'
  case 'legendary': return 'L'
  default: return 'N'
  }
}

watch(() => props.show, (newShow) => {
  if (newShow) {
    selectedPokemons.value = []
    searchQuery.value = ''
    rarityFilter.value = 'all'
    typeFilter.value = 'all'
  }
})
</script>
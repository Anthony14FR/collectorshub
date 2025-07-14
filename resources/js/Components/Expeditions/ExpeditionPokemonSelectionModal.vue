<template>
  <div v-if="show" class="fixed inset-0 z-[70] flex items-center justify-center p-2 sm:p-4" @click.self="$emit('close')">
    <div class="absolute inset-0 bg-base-100/80 backdrop-blur-md"></div>
    
    <div class="relative w-full max-w-sm sm:max-w-2xl md:max-w-5xl h-[85vh] bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-2 border-warning/20 rounded-2xl sm:rounded-3xl shadow-2xl shadow-warning/20 overflow-hidden flex flex-col">
      
      <div class="bg-gradient-to-r from-warning/20 to-warning/30 border-b border-warning/20 px-4 sm:px-6 md:px-8 py-4 sm:py-6 flex-shrink-0">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-8 h-8 bg-gradient-to-br from-warning/30 to-warning/40 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
          </div>
          <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
            {{ expedition?.name }}
          </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-base-100/50 rounded-lg p-3">
            <h4 class="font-semibold text-sm mb-2 flex items-center gap-2">
              <span class="w-2 h-2 bg-info rounded-full"></span>
              Exigences
            </h4>
            <ul class="space-y-1">
              <li v-for="req in expedition?.requirements" :key="req.id" 
                  class="text-xs text-base-content/70 flex items-center gap-2">
                <span class="w-1 h-1 bg-base-content/50 rounded-full"></span>
                {{ req.quantity }} {{ req.type === 'rarity' ? 'Pokémon' : req.type === 'type' ? 'type' : 'niveau' }} {{ req.value }}{{ req.type === 'level' ? '+' : '' }}
              </li>
            </ul>
          </div>
          
          <div class="bg-base-100/50 rounded-lg p-3">
            <h4 class="font-semibold text-sm mb-2 flex items-center gap-2">
              <span class="w-2 h-2 bg-success rounded-full"></span>
              Sélection: {{ selectedPokemons.length }}
            </h4>
            <div v-if="selectedPokemons.length > 0" class="flex flex-wrap gap-1">
              <div v-for="pokemon in selectedPokemonDetails.slice(0, 6)" :key="pokemon.id" 
                   class="w-8 h-8 bg-base-200 rounded-lg flex items-center justify-center">
                <img :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon?.id}.gif`" 
                     :alt="pokemon.pokemon?.name" 
                     class="w-6 h-6 object-contain"
                     style="image-rendering: pixelated;">
              </div>
              <div v-if="selectedPokemons.length > 6" class="w-8 h-8 bg-base-200 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold">+{{ selectedPokemons.length - 6 }}</span>
              </div>
            </div>
            <p v-else class="text-xs text-base-content/60">Aucun Pokémon sélectionné</p>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-hidden flex flex-col">
        <div class="p-4 sm:p-6 border-b border-base-300/30 bg-base-200/50 flex-shrink-0">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
              <Input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher par nom..."
                class="w-full h-[42px]"
              />
            </div>
            
            <Select
              v-model="rarityFilter"
              :options="rarityOptions"
              class="w-full h-[42px]"
            />
            
            <Select
              v-model="typeFilter"
              :options="typeOptions"
              class="w-full h-[42px]"
            />
          </div>
        </div>

        <div class="flex-1 overflow-hidden flex flex-col">
          <div v-if="filteredPokemons.length === 0" class="flex-1 flex items-center justify-center">
            <div class="text-center">
              <div class="w-16 h-16 bg-base-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <p class="text-base-content/60 text-sm">Aucun Pokémon éligible trouvé</p>
            </div>
          </div>
          
          <div v-else class="flex-1 p-4 sm:p-6 overflow-hidden">
            <VirtualScroller
              :items="pokemonRows"
              :item-size="140"
              class="h-full"
              style="height: 100%;"
            >
              <template #item="{ item: rowPokemons }">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-2 sm:gap-3 p-2" style="height: 140px;">
                  <SimplePokemonCard
                    v-for="pokemon in rowPokemons"
                    :key="pokemon.id"
                    :entry="pokemon"
                    :selected="selectedPokemons.includes(pokemon.id)"
                    size="small"
                    @click="togglePokemon(pokemon)"
                  />
                </div>
              </template>
            </VirtualScroller>
          </div>
        </div>
      </div>

      <div class="flex-shrink-0 bg-base-200/50 border-t border-base-300/30 px-4 sm:px-6 md:px-8 py-4 sm:py-6">
        <div class="mb-4 text-center">
          <p class="text-sm text-base-content/70">
            {{ selectedPokemons.length }} / {{ maxPokemons }} Pokémons sélectionnés
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <Button 
            variant="ghost" 
            @click="$emit('close')"
            class="order-2 sm:order-1"
          >
            Annuler
          </Button>
          
          <Button 
            variant="primary" 
            @click="startExpedition"
            :disabled="selectedPokemons.length !== maxPokemons || loading"
            :loading="loading"
            class="order-1 sm:order-2 flex-1"
          >
            {{ loading ? 'Démarrage...' : `Démarrer avec ${selectedPokemons.length} Pokémon${selectedPokemons.length > 1 ? 's' : ''}` }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'
import Select from '@/Components/UI/Select.vue'
import SimplePokemonCard from '@/Components/PokemonUpgrade/SimplePokemonCard.vue'

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
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1280)

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

const itemsPerRow = computed(() => {
  if (windowWidth.value >= 1280) return 8
  if (windowWidth.value >= 1024) return 6
  if (windowWidth.value >= 768) return 5
  if (windowWidth.value >= 640) return 4
  return 3
})

const pokemonRows = computed(() => {
  const rows = []
  const pokemons = filteredPokemons.value
  const itemsPerRowValue = itemsPerRow.value
  
  for (let i = 0; i < pokemons.length; i += itemsPerRowValue) {
    rows.push(pokemons.slice(i, i + itemsPerRowValue))
  }
  
  return rows
})



const selectedPokemonDetails = computed(() => {
  return selectedPokemons.value.map(id => 
    props.eligiblePokemons?.find(p => p.id === id)
  ).filter(Boolean)
})

const maxPokemons = computed(() => {
  return props.expedition?.requirements?.reduce((sum, req) => sum + req.quantity, 0) || 0
})

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
      console.error('Erreur serveur:', {
        status: response.status,
        statusText: response.statusText,
        data: data
      })
    }
  } catch (error) {
    console.error('Erreur:', error)
    emit('error', 'Erreur de connexion ou de traitement')
  } finally {
    loading.value = false
  }
}

const updateWindowWidth = () => {
  if (typeof window !== 'undefined') {
    windowWidth.value = window.innerWidth
  }
}

watch(() => props.show, (newShow) => {
  if (newShow) {
    updateWindowWidth()
    if (typeof window !== 'undefined') {
      window.addEventListener('resize', updateWindowWidth)
    }
  } else {
    selectedPokemons.value = []
    searchQuery.value = ''
    rarityFilter.value = 'all'
    typeFilter.value = 'all'
    if (typeof window !== 'undefined') {
      window.removeEventListener('resize', updateWindowWidth)
    }
  }
})
</script> 
<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Pokedex } from '@/types/pokedex';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import PokedexModalCard from '@/Components/Pokedex/PokedexModalCard.vue';

interface Props {
    show: boolean;
    onClose: () => void;
    userPokemons: Pokedex[];
}

const props = defineProps<Props>();

const localPokemons = ref<Pokedex[]>([]);
const processing = ref(false);
const searchQuery = ref('');
const rarityFilter = ref('all');
const shinyFilter = ref('all');

const rarities = [
    { value: 'all', label: 'Toutes les raretés' },
    { value: 'normal', label: 'Normal' },
    { value: 'rare', label: 'Rare' },
    { value: 'epic', label: 'Épique' },
    { value: 'legendary', label: 'Légendaire' },
];

const shinyOptions = [
    { value: 'all', label: 'Tous' },
    { value: 'shiny', label: 'Shiny seulement' },
    { value: 'not_shiny', label: 'Non-Shiny' },
]

watch(() => props.userPokemons, (newPokemons) => {
    localPokemons.value = JSON.parse(JSON.stringify(newPokemons));
}, { immediate: true, deep: true });

const team = computed(() => localPokemons.value.filter(p => p.is_in_team).sort((a,b) => (a.team_position ?? 99) - (b.team_position ?? 99)));

const filteredAvailablePokemons = computed(() => {
    return localPokemons.value
        .filter(p => !p.is_in_team)
        .filter(p => {
            if (!searchQuery.value) return true;
            return p.pokemon.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        })
        .filter(p => {
            if (rarityFilter.value === 'all') return true;
            return p.pokemon.rarity === rarityFilter.value;
        })
        .filter(p => {
            if (shinyFilter.value === 'all') return true;
            if (shinyFilter.value === 'shiny') return p.pokemon.is_shiny;
            if (shinyFilter.value === 'not_shiny') return !p.pokemon.is_shiny;
            return false;
        });
});

const addToTeam = (pokemon: Pokedex) => {
    if (team.value.length >= 6 || processing.value) return;
    
    const existingPositions = team.value.map(p => p.team_position).filter(Boolean) as number[];
    let newPosition = 1;
    while(newPosition <= 6 && existingPositions.includes(newPosition)) {
        newPosition++;
    }

    if (newPosition > 6) return;

    processing.value = true;
    router.post(`/pokedex/${pokemon.id}/add-to-team`, { position: newPosition }, {
        preserveScroll: true,
        onSuccess: () => {
            pokemon.is_in_team = true;
            pokemon.team_position = newPosition;
        },
        onFinish: () => { processing.value = false; }
    });
};

const removeFromTeam = (pokemon: Pokedex) => {
    if (processing.value) return;
    processing.value = true;
    router.post(`/pokedex/${pokemon.id}/remove-from-team`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            pokemon.is_in_team = false;
            pokemon.team_position = null;
        },
        onFinish: () => { processing.value = false; }
    });
};

</script>

<template>
    <Modal :show="show" max-width="5xl" @close="onClose">
        <template #header>
             <h2 class="text-2xl font-bold">Gérer mon équipe</h2>
        </template>
        <div class="p-4 bg-base-100/70">
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-base-content">Mon Équipe ({{ team.length }}/6)</h3>
                <div class="grid grid-cols-6 gap-4">
                    <div v-for="n in 6" :key="`team-slot-${n}`" class="aspect-square bg-base-200/50 rounded-xl border-2 border-dashed border-base-300/50 flex items-center justify-center p-2">
                        <div v-if="team[n-1]" @click="removeFromTeam(team[n-1])" class="text-center w-full h-full relative group cursor-pointer">
                            <img 
                                :src="`/images/pokemon-gifs/${team[n-1].pokemon.is_shiny ? (team[n-1].pokemon.id - 1000) + '_S' : team[n-1].pokemon.id}.gif`" 
                                :alt="team[n-1].pokemon.name"
                                class="w-16 h-16 object-contain mx-auto"
                                style="image-rendering: pixelated;"
                            >
                             <p class="text-xs font-bold mt-1 truncate">{{ team[n-1].nickname || team[n-1].pokemon.name }}</p>
                             <div class="absolute inset-0 bg-black/70 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white font-bold text-lg tracking-wider">Retirer</span>
                            </div>
                        </div>
                         <span v-else class="text-4xl text-base-content/20">+</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-base-content">Pokémon Disponibles</h3>
                    <div class="flex gap-2">
                        <Input type="text" v-model="searchQuery" placeholder="Rechercher..." size="sm"/>
                        <Select v-model="rarityFilter" :options="rarities" size="sm"/>
                        <Select v-model="shinyFilter" :options="shinyOptions" size="sm"/>
                    </div>
                </div>
                
                <div class="h-[40vh] overflow-y-auto p-2 bg-base-200/30 rounded-lg">
                    <div v-if="filteredAvailablePokemons.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <div v-for="p in filteredAvailablePokemons" :key="p.id" @click="addToTeam(p)" class="relative group" :class="{'opacity-50 cursor-not-allowed': team.length >= 6 || processing}">
                           <PokedexModalCard :displayPokemon="{pokedexInfo: p, pokemon: p.pokemon, owned: true, count: 1 }" />
                           <div v-if="team.length < 6" class="absolute inset-0 bg-black/70 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                               <span class="text-white font-bold text-lg tracking-wider">Ajouter</span>
                           </div>
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-center h-full">
                         <p class="text-center text-base-content/50">Aucun Pokémon correspondant aux filtres.</p>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template> 
export type Pokemon = {
    id: number;
    pokedex_id: number;
    name: string;
    types: Array<{ name: string; image: string }>;
    resistances: Array<{ name: string; damage_multiplier: number; damage_relation: string }>;
    evolution_id?: number;
    pre_evolution_id?: number;
    description: string;
    height: number;
    weight: number;
    rarity: string;
    is_shiny: boolean;
    hp: number;
    attack: number;
    defense: number;
    speed: number;
    special_attack: number;
    special_defense: number;
    generation: number;
}

export type PokemonCard = {
    id: number;
    pokedex_id: number;
    level?: number;
    nickname?: string;
    name: string;
    types: Array<{ name: string; image: string }>;
    resistances: Array<{ name: string; damage_multiplier: number; damage_relation: string }>;
    evolution_id?: number;
    pre_evolution_id?: number;
    description: string;
    height: number;
    weight: number;
    rarity: string;
    is_shiny: boolean;
    hp: number;
    attack: number;
    defense: number;
    speed: number;
    special_attack: number;
    special_defense: number;
    generation: number;
}
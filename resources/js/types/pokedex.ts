import type { Pokemon } from './pokemon';

export type Pokedex = {
    id: number;
    user_id: number;
    pokemon_id: number;
    nickname?: string;
    level: number;
    star: number;
    hp_left?: number;
    is_in_team: boolean;
    is_favorite: boolean;
    obtained_at: string;
    pokemon: Pokemon;
}
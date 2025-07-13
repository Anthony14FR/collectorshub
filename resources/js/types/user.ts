export type User = {
    id: number;
    username: string;
    email: string;
    avatar?: string;
    level: number;
    experience: number;
    cash: number;
    role: string;
    status: string;
    last_login: string;
    pokedex?: PokedexEntry[];
    inventory?: InventoryItem[];
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    experience_for_current_level: number;
    experience_for_next_level: number;
    experience_percentage: number;
    claimed_level_rewards?: string[];
}

export type LevelReward = {
    type: string;
    level: number;
    cash: number;
    pokeballs: number;
    masterballs: number;
    is_available: boolean;
}

export type PokedexEntry = {
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
    created_at: string;
    updated_at: string;
    pokemon?: {
        id: number;
        name: string;
        types: any[];
        resistances: any[];
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
    };
}

export type InventoryItem = {
    id: number;
    user_id: number;
    item_id: number;
    quantity: number;
    created_at: string;
    updated_at: string;
}
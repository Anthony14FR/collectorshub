export type User = {
    id: number;
    username: string;
    email: string;
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
}

export type PokedexEntry = {
    id: number;
    user_id: number;
    pokemon_id: number;
    level: number;
    is_shiny: boolean;
    is_in_team: boolean;
    created_at: string;
    updated_at: string;
}

export type InventoryItem = {
    id: number;
    user_id: number;
    item_id: number;
    quantity: number;
    created_at: string;
    updated_at: string;
}
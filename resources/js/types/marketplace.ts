import type { Pokemon } from './pokemon';

export type Marketplace = {
    id: number;
    seller_id: number;
    pokemon_id: number;
    price: number;
    status: string;
    pokemon: Pokemon;
}
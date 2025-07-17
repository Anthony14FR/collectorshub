export interface InfernalTowerLevel {
    level: number;
    team: {
        pokemon_id: number;
        name: string;
        star: number;
        is_shiny: boolean;
        cp: number;
    }[];
    team_cp: number;
    rewards: {
        pokeballs?: number;
        masterballs?: number;
        money?: number;
        exp?: number;
    };
    success_rate: number;
} 
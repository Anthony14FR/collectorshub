export interface LeaderboardUser {
    rank: number;
    id: number;
    username: string;
    level: number;
    avatar?: string;
    background?: string;
    value?: number;
    team_cp?: number;
    team_pokemons?: any[];
    name?: string;
    total_cp?: number;
    leader_id?: number;
    icon?: string;
    tower_level?: number;
    enemy_team?: any[];
    enemy_team_cp?: number;
}

export interface LeaderboardData {
    top: LeaderboardUser[];
    current_user: LeaderboardUser;
}

export interface Leaderboards {
    cash?: LeaderboardData;
    experience?: LeaderboardData;
    pokemon_count?: LeaderboardData;
    team_cp?: LeaderboardData;
}

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

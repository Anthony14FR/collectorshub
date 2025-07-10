export interface LeaderboardEntry {
    rank: number;
    id: number;
    username: string;
    value: number;
    level: number;
}

export interface LeaderboardData {
    top: LeaderboardEntry[];
    current_user: LeaderboardEntry;
}

export interface Leaderboards {
    cash: LeaderboardData;
    experience: LeaderboardData;
    pokemon_count: LeaderboardData;
} 
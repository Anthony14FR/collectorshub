export interface User {
    id: number;
    username: string;
    email: string;
    level: number;
    experience: number;
    cash: number;
    last_login: string | null;
    role: 'user' | 'premium' | 'admin';
    status: 'active' | 'suspended' | 'banned';
    email_verified_at?: string | null;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
} 
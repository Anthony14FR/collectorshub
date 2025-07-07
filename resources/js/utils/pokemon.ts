export const getTypeColor = (type: string) => {
    const colors: { [key: string]: string } = {
        'feu': 'from-red-500 to-orange-500',
        'eau': 'from-blue-500 to-cyan-500',
        'plante': 'from-green-500 to-lime-500',
        'électrik': 'from-yellow-400 to-yellow-600',
        'psy': 'from-pink-500 to-purple-500',
        'glace': 'from-cyan-300 to-blue-400',
        'dragon': 'from-purple-600 to-indigo-600',
        'ténèbres': 'from-gray-700 to-gray-900',
        'fée': 'from-pink-300 to-pink-500',
        'combat': 'from-red-600 to-red-800',
        'poison': 'from-purple-500 to-purple-700',
        'sol': 'from-yellow-600 to-amber-600',
        'vol': 'from-indigo-400 to-blue-500',
        'insecte': 'from-green-400 to-green-600',
        'roche': 'from-yellow-700 to-stone-600',
        'spectre': 'from-purple-700 to-indigo-800',
        'acier': 'from-gray-400 to-gray-600',
        'normal': 'from-gray-400 to-gray-500',
    };
    return colors[type?.toLowerCase()] || colors.normal;
};

export const getRarityColor = (rarity: string) => {
    const colors: { [key: string]: string } = {
        normal: 'from-slate-400 to-slate-600',
        rare: 'from-sky-400 to-sky-600',
        epic: 'from-violet-400 to-violet-600',
        legendary: 'from-amber-400 to-amber-600'
    };
    return colors[rarity] || colors.normal;
}; 
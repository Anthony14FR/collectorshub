<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/Components/UI/Button.vue';

interface Props {
    title: string;
    ballType: string;
    quantity: number;
    isAnimating: boolean;
    disabled: boolean;
    variant?: 'normal' | 'legendary';
}

const { title, ballType, quantity, isAnimating, disabled, variant = 'normal' } = defineProps<Props>();

const emit = defineEmits<{
    invoke: [ballType: string, quantity: number];
}>();

const portalConfig = computed(() => {
    if (variant === 'legendary') {
        return {
            gradient: 'from-warning/20 via-error/20 to-warning/20',
            glowColor: 'shadow-warning/30',
            borderColor: 'border-warning/40',
            textColor: 'text-warning',
            ballEmoji: '🏐',
            ringGradient: 'from-warning to-error',
            particleColor: 'bg-warning/60'
        };
    }

    return {
        gradient: 'from-primary/20 via-secondary/20 to-primary/20',
        glowColor: 'shadow-primary/30',
        borderColor: 'border-primary/40',
        textColor: 'text-primary',
        ballEmoji: '⚾',
        ringGradient: 'from-primary to-secondary',
        particleColor: 'bg-primary/60'
    };
});

const canInvoke = (requiredQuantity: number) => {
    return quantity >= requiredQuantity && !disabled && !isAnimating;
};

const handleInvoke = (requiredQuantity: number) => {
    if (canInvoke(requiredQuantity)) {
        emit('invoke', ballType, requiredQuantity);
    }
};

const getRarityChances = () => {
    if (ballType === 'Masterball') {
        return [
            { rarity: 'Normal', chance: '34%', color: 'text-base-content/70' },
            { rarity: 'Rare', chance: '60%', color: 'text-info' },
            { rarity: 'Epic', chance: '5%', color: 'text-secondary' },
            { rarity: 'Légendaire', chance: '1%', color: 'text-warning' }
        ];
    }

    return [
        { rarity: 'Normal', chance: '70%', color: 'text-base-content/70' },
        { rarity: 'Rare', chance: '27%', color: 'text-info' },
        { rarity: 'Epic', chance: '2.7%', color: 'text-secondary' },
        { rarity: 'Légendaire', chance: '0.3%', color: 'text-warning' }
    ];
};
</script>

<template>
    <div class="relative group">
        <!-- Particules d'ambiance -->
        <div class="absolute inset-0 pointer-events-none">
            <div
                v-for="i in 12"
                :key="i"
                :class="[
                    'absolute w-1 h-1 rounded-full opacity-60 animate-pulse',
                    portalConfig.particleColor
                ]"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 2}s`
                }"
            />
        </div>

        <!-- Portail principal -->
        <div
            :class="[
                'relative bg-gradient-to-br backdrop-blur-sm rounded-2xl border-2 p-8 transition-all duration-500 hover:scale-105',
                portalConfig.gradient,
                portalConfig.borderColor,
                quantity === 0 ? 'opacity-60' : 'hover:shadow-2xl ' + portalConfig.glowColor,
                isAnimating ? 'animate-pulse shadow-2xl ' + portalConfig.glowColor : ''
            ]"
        >
            <!-- Anneaux d'énergie -->
            <div v-if="quantity > 0" class="absolute inset-4 rounded-xl opacity-30 pointer-events-none">
                <div
                    v-for="i in 3"
                    :key="i"
                    :class="[
                        'absolute inset-0 rounded-xl border-2 animate-ping',
                        'border-gradient-to-r ' + portalConfig.ringGradient
                    ]"
                    :style="{
                        animationDelay: `${i * 0.5}s`,
                        animationDuration: '2s'
                    }"
                />
            </div>

            <!-- En-tête du portail -->
            <div class="text-center mb-8">
                <div class="text-6xl mb-4 animate-bounce">
                    {{ portalConfig.ballEmoji }}
                </div>
                <h2 :class="['text-2xl font-bold mb-2', portalConfig.textColor]">
                    {{ title }}
                </h2>
                <div class="text-base-content/70 text-sm">
                    {{ quantity }} disponible{{ quantity > 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Statistiques de rareté -->
            <div class="bg-base-100/40 backdrop-blur-sm rounded-xl p-4 mb-6">
                <h3 class="text-sm font-semibold mb-3 text-center text-base-content/80">
                    Chances d'obtention
                </h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div
                        v-for="stat in getRarityChances()"
                        :key="stat.rarity"
                        class="flex justify-between items-center p-2 rounded-lg bg-base-100/30"
                    >
                        <span :class="stat.color">{{ stat.rarity }}</span>
                        <span class="font-medium">{{ stat.chance }}</span>
                    </div>
                </div>
            </div>

            <!-- Boutons d'invocation -->
            <div class="space-y-4">
                <!-- Invocation x1 -->
                <Button
                    @click="handleInvoke(1)"
                    :disabled="!canInvoke(1)"
                    variant="secondary"
                    size="lg"
                    class="w-full relative overflow-hidden"
                >
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <span>{{ isAnimating ? '⚡ Invocation...' : '🎯 Invoquer x1' }}</span>
                        <span v-if="!canInvoke(1)" class="text-xs bg-error/20 text-error px-2 py-1 rounded-full">
                            {{ quantity < 1 ? 'Aucune ball' : 'Indisponible' }}
                        </span>
                    </span>
                </Button>

                <!-- Invocation x10 -->
                <Button
                    @click="handleInvoke(10)"
                    :disabled="!canInvoke(10)"
                    variant="primary"
                    size="lg"
                    class="w-full relative overflow-hidden"
                >
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <span>{{ isAnimating ? '⚡ Invocation...' : '🌟 Invoquer x10' }}</span>
                        <span v-if="canInvoke(10)" class="text-xs bg-success/20 text-success px-2 py-1 rounded-full">
                            Meilleur !
                        </span>
                        <span v-else class="text-xs bg-error/20 text-error px-2 py-1 rounded-full">
                            {{ quantity < 10 ? `${quantity}/10` : 'Indisponible' }}
                        </span>
                    </span>
                </Button>
            </div>

            <!-- Informations supplémentaires -->
            <div class="mt-6 text-center text-xs text-base-content/60">
                <p>Les {{ ballType }}s sont consommées lors de l'invocation</p>
                <p class="mt-1">{{ ballType === 'Masterball' ? 'Taux de légendaire augmenté !' : 'Invocation standard' }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>

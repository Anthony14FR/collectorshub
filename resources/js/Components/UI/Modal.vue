<script setup lang="ts">

interface Props {
    show: boolean;
}

defineProps<Props>();
const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="close">

            <div class="absolute inset-0 bg-base-100/80 backdrop-blur-md"></div>

            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div v-if="show" class="relative w-full max-w-5xl max-h-[90vh] bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-2 border-primary/20 rounded-3xl shadow-2xl shadow-primary/20 overflow-hidden flex flex-col">

                    <div class="bg-gradient-to-r from-primary/20 to-secondary/20 px-8 py-6 border-b border-primary/20 flex items-center justify-between flex-shrink-0">
                        <div class="flex-1">
                            <slot name="header">
                                <h3 class="text-xl font-bold tracking-wider">Modal Title</h3>
                            </slot>
                        </div>
                        <button
                            @click="close"
                            class="w-10 h-10 rounded-xl bg-base-200/50 hover:bg-base-200 transition-colors duration-200 flex items-center justify-center text-2xl font-bold hover:text-error"
                        >
                            ×
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6">
                        <slot>Modal content goes here.</slot>
                    </div>

                    <div class="absolute top-6 right-16 w-6 h-6 border-2 border-success/20 rounded-full animate-pulse pointer-events-none"></div>
                    <div class="absolute bottom-6 left-6 w-4 h-4 border-2 border-primary/30 rounded-full animate-pulse delay-500 pointer-events-none"></div>
                    <div class="absolute top-12 left-12 w-2 h-2 bg-accent/40 rounded-full blur-sm animate-pulse opacity-60 delay-1000 pointer-events-none"></div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(4px);
}

.modal-container {
    min-width: 300px;
    width: 80%;
    max-width: 1000px;
    max-height: 90vh;
    margin: 0px auto;
    background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
    border: 4px solid #404040;
    box-shadow:
        inset 2px 2px 0px white,
        inset -2px -2px 0px #b0b0b0,
        8px 8px 0px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 10px 15px;
    border-bottom: 4px solid #404040;
    display: flex;
    align-items: center;
    position: relative;
    flex-shrink: 0;
}

.modal-close-button {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 32px;
    color: #202020;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    margin-right: 15px;
    line-height: 1;
    text-shadow: 1px 1px 0 #fff;
}

.modal-close-button:hover {
    color: #ff1b1b;
}

.modal-header-content {
    flex-grow: 1;
}

.modal-body {
    margin: 10px;
    padding: 10px;
    overflow-y: auto;
    flex-grow: 1;
}

.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.modal-body::-webkit-scrollbar-thumb {
    background: #404040;
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    transform: scale(0.9);
}
</style>

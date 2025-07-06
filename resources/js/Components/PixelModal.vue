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
    <Transition name="modal">
        <div v-if="show" class="modal-mask" @click.self="close">
            <div class="modal-container">
                <div class="modal-header">
                    <button class="modal-close-button" @click="close">
                        ×
                    </button>
                    <div class="modal-header-content">
                        <slot name="header">Titre de la modale</slot>
                    </div>
                </div>

                <div class="modal-body">
                    <slot>Le contenu de la modale est vide.</slot>
                </div>
            </div>
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

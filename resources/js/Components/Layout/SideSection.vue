<script setup lang="ts">
interface Props {
  position: 'left' | 'right';
  top?: boolean;
}

const { position, top = true } = defineProps<Props>();

const baseClasses = 'absolute w-72';
const positionClasses = {
  left: 'left-8',
  right: 'right-8'
};
const topClasses = top ? 'top-8' : 'top-24';
</script>

<template>
  <div :class="[baseClasses, positionClasses[position], topClasses]">
    <slot />
  </div>
</template> 
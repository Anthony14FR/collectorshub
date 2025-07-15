<script setup lang="ts">
interface Props {
  position: 'left' | 'right';
  top?: boolean;
  customClasses?: string | string[];
}

const { position, top = true, customClasses } = defineProps<Props>();

const baseClasses = 'absolute w-60 xl:w-72 2xl:w-96';
const positionClasses = {
  left: 'left-8',
  right: 'right-8'
};
const topClasses = top ? 'top-8' : 'top-24';
</script>

<template>
  <div :class="[baseClasses, positionClasses[position], topClasses, customClasses]">
    <slot />
  </div>
</template> 
<script setup lang="ts">
interface Props {
  count: number;
}

defineProps<Props>();
</script>

<template>
  <div
    v-if="count > 1"
    class="absolute right-5 -top-5 bg-primary text-primary-content rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold border-2 border-base-100"
  >
    {{ count }}
  </div>
</template> 
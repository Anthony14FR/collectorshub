<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/Components/UI/Button.vue';

interface Props {
  currentPage: number;
  totalPages: number;
  maxVisiblePages?: number;
}

const { currentPage, totalPages, maxVisiblePages = 5 } = defineProps<Props>();

const emit = defineEmits<{
  pageChanged: [page: number];
}>();

const visiblePages = computed(() => {
  const delta = Math.floor(maxVisiblePages / 2);
  let start = Math.max(currentPage - delta, 1);
  let end = Math.min(start + maxVisiblePages - 1, totalPages);
  
  if (end - start + 1 < maxVisiblePages) {
    start = Math.max(end - maxVisiblePages + 1, 1);
  }
  
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages && page !== currentPage) {
    emit('pageChanged', page);
  }
};
</script>

<template>
  <div class="flex items-center justify-center gap-1">
    <Button
      v-if="currentPage > 1"
      @click="goToPage(1)"
      variant="secondary"
      size="sm"
      class="hidden sm:flex"
    >
      ««
    </Button>
    
    <Button
      v-if="currentPage > 1"
      @click="goToPage(currentPage - 1)"
      variant="secondary"
      size="sm"
    >
      ‹
    </Button>
    
    <Button
      v-for="page in visiblePages"
      :key="page"
      @click="goToPage(page)"
      :variant="page === currentPage ? 'primary' : 'secondary'"
      size="sm"
      class="min-w-[2.5rem]"
    >
      {{ page }}
    </Button>
    
    <Button
      v-if="currentPage < totalPages"
      @click="goToPage(currentPage + 1)"
      variant="secondary"
      size="sm"
    >
      ›
    </Button>
    
    <Button
      v-if="currentPage < totalPages"
      @click="goToPage(totalPages)"
      variant="secondary"
      size="sm"
      class="hidden sm:flex"
    >
      »»
    </Button>
  </div>
</template>
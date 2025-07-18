<template>
  <div class="text-center">
    <div class="text-sm font-bold text-base-content/80 mb-1">
      {{ formattedTime }}
    </div>
    <div class="text-xs text-base-content/60">
      Prochain envoi dans :
    </div>
  </div>
</template>

<script setup lang="ts">
import { watch, type PropType } from 'vue'
import { useFriendGiftCountdown } from '@/composables/useFriendGiftCountdown'

const props = defineProps({
  nextGiftAvailableAt: {
    type: [String, Date, null] as PropType<string | Date | null | undefined>,
    required: false
  }
})

const emit = defineEmits(['expired'])

const { formattedTime, isExpired } = useFriendGiftCountdown(props.nextGiftAvailableAt)

watch(() => isExpired.value, (expired) => {
  if (expired) {
    emit('expired')
  }
})
</script> 
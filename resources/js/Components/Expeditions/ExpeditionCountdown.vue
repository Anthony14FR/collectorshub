<template>
  <div class="text-center">
    <div class="text-lg font-bold text-primary mb-1">
      {{ formattedTime }}
    </div>
    <div class="text-xs text-base-content/60">
      Fin prévue : {{ formattedEndTime }}
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'
import { useCountdown } from '@/composables/useCountdown'

const props = defineProps({
  endTime: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['expired'])

const { formattedTime, formattedEndTime, timeLeft } = useCountdown(props.endTime)

watch(() => timeLeft.value.isExpired, (isExpired) => {
  if (isExpired) {
    emit('expired')
  }
})
</script> 
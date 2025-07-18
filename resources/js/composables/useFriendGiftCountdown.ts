import { ref, computed, onMounted, onUnmounted } from 'vue'

export function useFriendGiftCountdown(nextGiftAvailableAt: string | Date | null | undefined) {
  const now = ref(new Date())
  const interval = ref<NodeJS.Timeout | null>(null)

  const endTime = computed(() => {
    if (!nextGiftAvailableAt) return null;
    return new Date(nextGiftAvailableAt);
  });

  const timeLeft = computed(() => {
    if (!endTime.value) {
      return {
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        isExpired: true
      }
    }

    const current = now.value
    const diff = endTime.value.getTime() - current.getTime()

    if (diff <= 0) {
      return {
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        isExpired: true
      }
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
    const seconds = Math.floor((diff % (1000 * 60)) / 1000)

    const result = {
      days,
      hours,
      minutes,
      seconds,
      isExpired: false
    };
    return result;
  })

  const formattedTime = computed(() => {
    const { days, hours, minutes, seconds, isExpired } = timeLeft.value
    
    if (isExpired) {
      return 'Prêt à envoyer'
    }

    let result = '';
    if (days > 0) {
      result += `${days}j `;
    }
    if (hours > 0 || days > 0) { 
      result += `${hours}h `;
    }
    if (minutes > 0 || hours > 0 || days > 0) { 
      result += `${minutes}m `;
    }
    result += `${seconds}s`;

    return result.trim();
  })

  const start = () => {
    if (interval.value) return
    
    interval.value = setInterval(() => {
      now.value = new Date()
    }, 1000)
  }

  const stop = () => {
    if (interval.value) {
      clearInterval(interval.value)
      interval.value = null
    }
  }

  onMounted(() => {
    start()
  })

  onUnmounted(() => {
    stop()
  })

  return {
    timeLeft,
    formattedTime,
    isExpired: computed(() => timeLeft.value.isExpired),
    start,
    stop
  }
} 
import { ref, computed, onMounted, onUnmounted } from 'vue'

export function useCountdown(endTime: string | Date) {
  const now = ref(new Date())
  const interval = ref<NodeJS.Timeout | null>(null)

  const timeLeft = computed(() => {
    const end = new Date(endTime)
    const current = now.value
    const diff = end.getTime() - current.getTime()

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

    return {
      days,
      hours,
      minutes,
      seconds,
      isExpired: false
    }
  })

  const formattedTime = computed(() => {
    const { days, hours, minutes, seconds, isExpired } = timeLeft.value
    
    if (isExpired) {
      return 'Terminé'
    }

    if (days > 0) {
      return `${days}j ${hours}h ${minutes}m`
    }
    
    if (hours > 0) {
      return `${hours}h ${minutes}m`
    }
    
    if (minutes > 0) {
      return `${minutes}m ${seconds}s`
    }
    
    return `${seconds}s`
  })

  const formattedEndTime = computed(() => {
    const end = new Date(endTime)
    return end.toLocaleString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
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
    formattedEndTime,
    start,
    stop
  }
} 
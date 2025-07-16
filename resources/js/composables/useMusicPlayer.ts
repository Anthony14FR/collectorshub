import { ref, computed } from 'vue'

export interface Track {
  id: number
  title: string
  file: string
}

const isPlaying = ref(false)
const isVisible = ref(true)
const currentTrackIndex = ref(0)
const currentTime = ref(0)
const duration = ref(0)
const volume = ref(0.7)
const isLoading = ref(false)

const tracks: Track[] = [
  { id: 1, title: 'Echoes', file: 'https://store-screenapp-development.storage.googleapis.com/vid/6878243709620647f5212107/3bad47ef-1658-4b63-8709-6f05917a6fd1.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=GOOG1EJXLJXCIBDJVU63TV65H42NJBSY3VRUX6VCUDFB2V4UJPWDRTEQNT6AQ%2F20250716%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20250716T221418Z&X-Amz-Expires=604800&X-Amz-Signature=eefd8b36a62e792f68c531f4d44d41c5e1dcdfd23055a36ddf17ad4479b783fa&X-Amz-SignedHeaders=host&response-content-type=attachment%3B%20filename%3D%223bad47ef-1658-4b63-8709-6f05917a6fd1.mp3%22%3B%20filename%2A%3D%20UTF-8%27%27Echoes.mp3%3B#t=0,' },
  { id: 2, title: 'Help Me', file: 'https://store-screenapp-development.storage.googleapis.com/vid/6878243709620647f5212107/5019592e-ab42-4acb-99ac-5a4b88149e67.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=GOOG1EJXLJXCIBDJVU63TV65H42NJBSY3VRUX6VCUDFB2V4UJPWDRTEQNT6AQ%2F20250716%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20250716T221625Z&X-Amz-Expires=604800&X-Amz-Signature=4068a9456762ff970841000e40b5869b033bb5ab82e357080e9da0b985f18e2a&X-Amz-SignedHeaders=host&response-content-type=attachment%3B%20filename%3D%225019592e-ab42-4acb-99ac-5a4b88149e67.mp3%22%3B%20filename%2A%3D%20UTF-8%27%27Help_me.mp3%3B#t=0,' },
  { id: 3, title: 'Midnight', file: 'https://store-screenapp-development.storage.googleapis.com/vid/6878243709620647f5212107/5019592e-ab42-4acb-99ac-5a4b88149e67.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=GOOG1EJXLJXCIBDJVU63TV65H42NJBSY3VRUX6VCUDFB2V4UJPWDRTEQNT6AQ%2F20250716%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20250716T221625Z&X-Amz-Expires=604800&X-Amz-Signature=4068a9456762ff970841000e40b5869b033bb5ab82e357080e9da0b985f18e2a&X-Amz-SignedHeaders=host&response-content-type=attachment%3B%20filename%3D%225019592e-ab42-4acb-99ac-5a4b88149e67.mp3%22%3B%20filename%2A%3D%20UTF-8%27%27Help_me.mp3%3B#t=0,' },
  { id: 4, title: 'Moonlight Static', file: 'https://store-screenapp-development.storage.googleapis.com/vid/6878243709620647f5212107/5019592e-ab42-4acb-99ac-5a4b88149e67.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=GOOG1EJXLJXCIBDJVU63TV65H42NJBSY3VRUX6VCUDFB2V4UJPWDRTEQNT6AQ%2F20250716%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20250716T221625Z&X-Amz-Expires=604800&X-Amz-Signature=4068a9456762ff970841000e40b5869b033bb5ab82e357080e9da0b985f18e2a&X-Amz-SignedHeaders=host&response-content-type=attachment%3B%20filename%3D%225019592e-ab42-4acb-99ac-5a4b88149e67.mp3%22%3B%20filename%2A%3D%20UTF-8%27%27Help_me.mp3%3B#t=0,' }
]

let audioElement: HTMLAudioElement | null = null

export const useMusicPlayer = () => {
  const currentTrack = computed(() => tracks[currentTrackIndex.value])
  const progress = computed(() => duration.value > 0 ? (currentTime.value / duration.value) * 100 : 0)

  const initAudio = (element: HTMLAudioElement) => {
    audioElement = element
    audioElement.volume = volume.value
    audioElement.src = currentTrack.value.file
    audioElement.load()
    
    audioElement.addEventListener('loadstart', () => {
      isLoading.value = true
    })
    
    audioElement.addEventListener('canplay', () => {
      isLoading.value = false
      duration.value = audioElement?.duration || 0
    })
    
    audioElement.addEventListener('timeupdate', () => {
      currentTime.value = audioElement?.currentTime || 0
    })
    
    audioElement.addEventListener('error', () => {
      isLoading.value = false
      isPlaying.value = false
    })
  }

  const play = async () => {
    if (!audioElement) return
    
    if (!audioElement.src || audioElement.src === location.href) {
      audioElement.src = currentTrack.value.file
      audioElement.load()
    }
    
    try {
      await audioElement.play()
      isPlaying.value = true
    } catch (error) {
      isPlaying.value = false
    }
  }

  const pause = () => {
    if (!audioElement) return
    audioElement.pause()
    isPlaying.value = false
  }

  const toggle = () => {
    if (isPlaying.value) {
      pause()
    } else {
      play()
    }
  }

  const nextTrack = () => {
    currentTrackIndex.value = (currentTrackIndex.value + 1) % tracks.length
  }

  const prevTrack = () => {
    currentTrackIndex.value = currentTrackIndex.value === 0 
      ? tracks.length - 1 
      : currentTrackIndex.value - 1
  }

  const setTrack = (index: number) => {
    if (index >= 0 && index < tracks.length) {
      currentTrackIndex.value = index
    }
  }

  const setVolume = (newVolume: number) => {
    volume.value = Math.max(0, Math.min(1, newVolume))
    if (audioElement) {
      audioElement.volume = volume.value
    }
  }

  const toggleVisibility = () => {
    isVisible.value = !isVisible.value
  }

  const formatTime = (time: number): string => {
    if (isNaN(time)) return '0:00'
    const minutes = Math.floor(time / 60)
    const seconds = Math.floor(time % 60)
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
  }

  return {
    isPlaying,
    isVisible,
    currentTrackIndex,
    currentTime,
    duration,
    volume,
    isLoading,
    tracks,
    currentTrack,
    progress,
    initAudio,
    play,
    pause,
    toggle,
    nextTrack,
    prevTrack,
    setTrack,
    setVolume,
    toggleVisibility,
    formatTime
  }
}
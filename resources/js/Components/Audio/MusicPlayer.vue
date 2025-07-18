<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useMusicPlayer } from '@/composables/useMusicPlayer'

const {
  isPlaying,
  isVisible,
  currentTrack,
  currentTrackIndex,
  currentTime,
  duration,
  volume,
  isLoading,
  progress,
  tracks,
  initAudio,
  toggle,
  nextTrack,
  prevTrack,
  setTrack,
  setVolume,
  toggleVisibility,
  formatTime
} = useMusicPlayer()

const audioRef = ref<HTMLAudioElement>()
const playerRef = ref<HTMLDivElement>()
const showTrackList = ref(false)
const showVolumeSlider = ref(false)
const isDraggingVolume = ref(false)

const handleSetTrack = (index: number) => {
  if (index === currentTrackIndex.value || !audioRef.value) return
  
  const wasPlaying = isPlaying.value
  
  if (wasPlaying) {
    audioRef.value.pause()
  }
  
  audioRef.value.src = tracks[index].file
  setTrack(index)
  audioRef.value.load()
  
  if (wasPlaying) {
    setTimeout(() => {
      audioRef.value?.play()
    }, 300)
  }
}

const handleNextTrack = () => {
  const newIndex = (currentTrackIndex.value + 1) % tracks.length
  handleSetTrack(newIndex)
}

const handlePrevTrack = () => {
  const newIndex = currentTrackIndex.value === 0 ? tracks.length - 1 : currentTrackIndex.value - 1
  handleSetTrack(newIndex)
}

const handleVolumeMouseDown = (event: MouseEvent) => {
  isDraggingVolume.value = true
  const volumeBar = event.currentTarget as HTMLElement
  
  const updateVolume = (e: MouseEvent) => {
    const rect = volumeBar.getBoundingClientRect()
    const clickX = e.clientX - rect.left
    const percentage = Math.max(0, Math.min(100, (clickX / rect.width) * 100))
    const newVolume = percentage / 100
    setVolume(newVolume)
  }
  
  updateVolume(event)
  
  const handleMouseMove = (e: MouseEvent) => {
    if (isDraggingVolume.value) {
      updateVolume(e)
    }
  }
  
  const handleMouseUp = () => {
    isDraggingVolume.value = false
    document.removeEventListener('mousemove', handleMouseMove)
    document.removeEventListener('mouseup', handleMouseUp)
  }
  
  document.addEventListener('mousemove', handleMouseMove)
  document.addEventListener('mouseup', handleMouseUp)
}

const handleProgressClick = (event: MouseEvent) => {
  if (!audioRef.value || !duration.value) return
  
  const progressBar = event.currentTarget as HTMLElement
  const rect = progressBar.getBoundingClientRect()
  const clickX = event.clientX - rect.left
  const percentage = Math.max(0, Math.min(100, (clickX / rect.width) * 100))
  const targetTime = (percentage / 100) * duration.value
  
  audioRef.value.currentTime = targetTime
}

const handleVolumeWheel = (event: WheelEvent) => {
  event.preventDefault()
  const delta = event.deltaY > 0 ? -0.05 : 0.05
  const newVolume = Math.max(0, Math.min(1, volume.value + delta))
  setVolume(newVolume)
}

const handleClickOutside = (event: MouseEvent) => {
  if (
    isVisible.value && 
    playerRef.value && 
    !playerRef.value.contains(event.target as Node)
  ) {
    toggleVisibility()
    showTrackList.value = false
    showVolumeSlider.value = false
  }
}

onMounted(() => {
  if (audioRef.value) {
    audioRef.value.addEventListener('ended', () => {
      handleNextTrack()
    })
    
    initAudio(audioRef.value)
  }
  
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  if (isDraggingVolume.value) {
    isDraggingVolume.value = false
  }
  
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<template>
  <audio ref="audioRef" preload="auto" />

  <Teleport to="body">
    <div
      v-if="isVisible"
      ref="playerRef"
      class="fixed bottom-4 right-4 z-50 bg-base-100/80 backdrop-blur-lg rounded-xl border border-base-300/40 shadow-2xl overflow-hidden transition-all duration-300 ease-in-out"
      :class="showTrackList ? 'w-80' : 'w-72'"
    >
      <div class="p-3 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-gradient-to-br from-primary/30 to-secondary/30 rounded-lg flex items-center justify-center">
              <span class="text-xs">🎵</span>
            </div>
            <h3 class="text-xs font-bold tracking-wider text-base-content/80">MUSIC PLAYER</h3>
          </div>
          
          <div class="flex items-center gap-1">
            <button
              @click="showTrackList = !showTrackList"
              class="w-6 h-6 rounded-md hover:bg-base-200/50 transition-colors flex items-center justify-center"
              :class="showTrackList ? 'bg-primary/20 text-primary' : 'text-base-content/60'"
            >
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
              </svg>
            </button>

            <button
              @click="toggleVisibility"
              class="w-6 h-6 rounded-md hover:bg-base-200/50 transition-colors flex items-center justify-center text-base-content/60"
            >
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center gap-2 mb-3">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-base-content truncate">
              {{ currentTrack.title }}
            </p>
            <p class="text-xs text-base-content/60">
              {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
            </p>
          </div>
        </div>

        <div 
          class="w-full h-2 bg-base-200/60 rounded-full cursor-pointer mb-3 overflow-hidden group"
          @click="handleProgressClick"
        >
          <div 
            class="h-full bg-gradient-to-r from-primary to-secondary transition-all duration-100 ease-out group-hover:from-primary/80 group-hover:to-secondary/80"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1">
            <button
              @click="handlePrevTrack"
              class="w-8 h-8 rounded-lg hover:bg-base-200/50 transition-colors flex items-center justify-center text-base-content/70 hover:text-base-content"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/>
              </svg>
            </button>

            <button
              @click="toggle"
              :disabled="isLoading"
              class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 hover:from-primary/30 hover:to-secondary/30 transition-all duration-200 flex items-center justify-center border border-primary/20 hover:border-primary/30 disabled:opacity-50"
            >
              <svg v-if="isLoading" class="w-5 h-5 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else-if="isPlaying" class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 4a1 1 0 011-1h1a1 1 0 011 1v12a1 1 0 01-1 1H6a1 1 0 01-1-1V4zM12 4a1 1 0 011-1h1a1 1 0 011 1v12a1 1 0 01-1 1h-1a1 1 0 01-1-1V4z"/>
              </svg>
              <svg v-else class="w-5 h-5 text-primary ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.3 2.841A1 1 0 005 3.75v12.5a1 1 0 001.3.909l10-5a1 1 0 000-1.818l-10-5z"/>
              </svg>
            </button>

            <button
              @click="handleNextTrack"
              class="w-8 h-8 rounded-lg hover:bg-base-200/50 transition-colors flex items-center justify-center text-base-content/70 hover:text-base-content"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798L4.555 5.168z"/>
              </svg>
            </button>
          </div>

          <div class="relative">
            <button
              @click="showVolumeSlider = !showVolumeSlider"
              @wheel="handleVolumeWheel"
              class="w-8 h-8 rounded-lg hover:bg-base-200/50 transition-colors flex items-center justify-center text-base-content/70 hover:text-base-content"
              :class="showVolumeSlider ? 'bg-primary/10 text-primary' : ''"
            >
              <svg v-if="volume > 0.5" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.791L4.975 14H2a1 1 0 01-1-1V7a1 1 0 011-1h2.975l3.408-2.791zM15.609 5.05a1 1 0 011.398.203 6.935 6.935 0 010 9.494 1 1 0 11-1.601-1.195 4.935 4.935 0 000-7.102 1 1 0 01.203-1.4z"/>
                <path d="M13.78 7.22a1 1 0 011.414 0 3.935 3.935 0 010 5.56 1 1 0 01-1.414-1.414 1.935 1.935 0 000-2.732 1 1 0 010-1.414z"/>
              </svg>
              <svg v-else-if="volume > 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.791L4.975 14H2a1 1 0 01-1-1V7a1 1 0 011-1h2.975l3.408-2.791zM13.78 7.22a1 1 0 011.414 0 3.935 3.935 0 010 5.56 1 1 0 01-1.414-1.414 1.935 1.935 0 000-2.732 1 1 0 010-1.414z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.791L4.975 14H2a1 1 0 01-1-1V7a1 1 0 011-1h2.975l3.408-2.791zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z"/>
              </svg>
            </button>

            <div
              v-if="showVolumeSlider"
              class="absolute bottom-full right-0 mb-2 bg-base-100/90 backdrop-blur-sm rounded-lg border border-base-300/30 p-2 shadow-lg"
            >
              <div class="flex flex-col items-center gap-2">
                <span class="text-xs text-base-content/60">{{ Math.round(volume * 100) }}%</span>
                <div 
                  class="relative w-20 h-3 bg-base-200 rounded-full overflow-hidden cursor-pointer select-none"
                  @mousedown="handleVolumeMouseDown"
                  :class="isDraggingVolume ? 'cursor-grabbing' : 'cursor-pointer'"
                >
                  <div 
                    class="h-full bg-gradient-to-r from-primary to-secondary rounded-full transition-all duration-150 pointer-events-none"
                    :style="{ width: `${volume * 100}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showTrackList" class="max-h-48 overflow-y-auto border-t border-base-300/20">
        <div class="p-2">
          <div class="text-xs font-bold text-base-content/60 mb-2 px-2">PLAYLIST ({{ tracks.length }})</div>
          <div
            v-for="(track, index) in tracks"
            :key="track.id"
            @click="handleSetTrack(index)"
            class="flex items-center gap-2 p-2 rounded-lg cursor-pointer transition-colors hover:bg-base-200/50"
            :class="index === currentTrackIndex ? 'bg-primary/10 text-primary' : 'text-base-content/80'"
          >
            <div class="w-6 h-6 rounded-md bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center flex-shrink-0">
              <span class="text-xs font-bold">{{ index + 1 }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium truncate">{{ track.title }}</p>
            </div>
            <div v-if="index === currentTrackIndex && isPlaying" class="flex-shrink-0">
              <div class="flex items-center space-x-0.5">
                <div class="w-1 h-3 bg-primary rounded-full animate-pulse"></div>
                <div class="w-1 h-2 bg-primary rounded-full animate-pulse delay-100"></div>
                <div class="w-1 h-4 bg-primary rounded-full animate-pulse delay-200"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>

  <Teleport to="body">
    <button
      v-if="!isVisible"
      @click="toggleVisibility"
      class="fixed bottom-4 right-4 z-50 w-12 h-12 bg-gradient-to-br from-primary/20 to-secondary/20 backdrop-blur-lg rounded-full border border-primary/30 shadow-lg flex items-center justify-center transition-all duration-300 hover:from-primary/30 hover:to-secondary/30 hover:scale-110"
      :class="isPlaying ? 'music-playing' : ''"
    >
      <span class="text-lg transition-transform duration-300" :class="isPlaying ? 'music-note-playing' : ''">🎵</span>
    </button>
  </Teleport>
</template>

<style scoped>

.music-playing {
  animation: musicPulse 1.5s ease-in-out infinite;
  box-shadow: 0 0 20px rgba(var(--primary), 0.4), 0 8px 25px rgba(0, 0, 0, 0.2);
}

.music-note-playing {
  animation: musicNoteFloat 2s ease-in-out infinite;
}

@keyframes musicPulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 15px rgba(var(--primary), 0.3), 0 4px 20px rgba(0, 0, 0, 0.2);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 25px rgba(var(--primary), 0.6), 0 6px 30px rgba(0, 0, 0, 0.3);
  }
}

@keyframes musicNoteFloat {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-2px) rotate(-3deg);
  }
  50% {
    transform: translateY(-1px) rotate(0deg);
  }
  75% {
    transform: translateY(-3px) rotate(3deg);
  }
}

.music-playing::before {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(45deg, rgba(var(--primary), 0.3), rgba(var(--secondary), 0.3));
  border-radius: 50%;
  z-index: -1;
  animation: musicRipple 2s linear infinite;
}

@keyframes musicRipple {
  0% {
    transform: scale(0.8);
    opacity: 1;
  }
  100% {
    transform: scale(1.2);
    opacity: 0;
  }
}
</style>
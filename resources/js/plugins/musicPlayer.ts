import type { App } from 'vue'
import { createApp } from 'vue'
import MusicPlayer from '@/Components/Audio/MusicPlayer.vue'

export default {
  install(app: App) {
    const mountMusicPlayer = () => {
      if (document.getElementById('global-music-player')) {
        return
      }

      const container = document.createElement('div')
      container.id = 'global-music-player'
      document.body.appendChild(container)

      const musicPlayerApp = createApp(MusicPlayer)
      musicPlayerApp.mount(container)
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', mountMusicPlayer)
    } else {
      setTimeout(mountMusicPlayer, 100)
    }
  }
}
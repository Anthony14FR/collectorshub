<template>
  <Modal :show="show" @close="onClose" max-width="6xl">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-info/20 to-info/40 rounded-lg flex items-center justify-center">
          <span class="text-lg">❓</span>
        </div>
        <div>
          <h3 class="text-xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent">
            Centre d'aide
          </h3>
          <p class="text-sm text-base-content/70">Guide des fonctionnalités</p>
        </div>
      </div>
    </template>
  
    <div class="p-6 max-h-[70vh] overflow-y-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🎒</span>
            <h4 class="font-bold text-base-content">Sac</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Votre porte-monnaie ainsi que vos objets.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🏪</span>
            <h4 class="font-bold text-base-content">Boutique</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Acheter des Pokeball / Masterball, des avatars et des fonds.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🎰</span>
            <h4 class="font-bold text-base-content">Invocation</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Invocation Unique x1 ou multiple x10 pour Pokeball et Masterball.
          </p>
          <div class="text-xs text-base-content/70 space-y-1">
            <div>• Chaque Pokémon invoqué possède 1/100 d'être shiny</div>
            <div>• Rareté : Normal, rare, epic, légendaire</div>
            <div>• Taux de capture pour Rare, epic et légendaire augmentés avec Masterball</div>
          </div>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🛒</span>
            <h4 class="font-bold text-base-content">Marketplace</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Mettre en vente des Pokémon OU en acheter auprès d'autres joueurs qui ont mis en vente.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🏆</span>
            <h4 class="font-bold text-base-content">Badges</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Succès déverrouillés au fur et à mesure de votre aventure qui donnent des récompenses d'exp et de cash.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🏅</span>
            <h4 class="font-bold text-base-content">Classement</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Top 100 pour : Niveau de compte, Team CP, Pokémon en possession et Progression dans la tour.
          </p>
          <div class="text-xs text-base-content/70">C'est purement du flex.</div>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">👥</span>
            <h4 class="font-bold text-base-content">Amis</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Ajouter jusqu'à 30 amis, vous pouvez envoyer des cadeaux une fois par jour à un ami (100 money).
          </p>
          <div class="text-xs text-base-content/70 space-y-1">
            <div>• Avec 30 amis qui vous envoient des cadeaux, ça monte à 3000 argent par jour</div>
            <div>• Suggestion d'amis, recherche d'amis</div>
          </div>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🎁</span>
            <h4 class="font-bold text-base-content">Récompenses de Niveau</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Offrant des récompenses à CHAQUE niveau.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">👤</span>
            <h4 class="font-bold text-base-content">Profile</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Changer informations de compte, 2FA, avatar, fond...
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🎟️</span>
            <h4 class="font-bold text-base-content">Codes promo</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Entrez des codes promo et recevez des récompenses.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">📚</span>
            <h4 class="font-bold text-base-content">Pokédex</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Pour voir la progression de votre pokédex (normal et shiny).
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">⭐</span>
            <h4 class="font-bold text-base-content">Amélioration</h4>
          </div>
          <p class="text-sm text-base-content/80">
            Améliorer vos Pokémon d'étoile pour augmenter leurs CP pour grimper dans le classement ou pourfendre la tour.
          </p>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🗺️</span>
            <h4 class="font-bold text-base-content">Expédition</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Lancer des équipes de Pokémon en expédition, et gagner des récompenses une fois revenus de leur aventure !
          </p>
          <div class="text-xs text-base-content/70">
            Plusieurs paliers d'expédition : Normal, rare, epic, légendaire, demandant plus ou moins de Pokémons et offrant plus ou moins de récompenses.
          </div>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🏗️</span>
            <h4 class="font-bold text-base-content">Tour Infernale</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Combattez les dresseurs de la tour et gagnez des récompenses en MASSE !
          </p>
          <div class="text-xs text-base-content/70">
            Vous avez le droit à 10 défaites par JOURS ! Tant que vous gagnez, grimpez !
          </div>
        </div>
  
        <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/20">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🎵</span>
            <h4 class="font-bold text-base-content">Lecteur MP3</h4>
          </div>
          <p class="text-sm text-base-content/80 mb-2">
            Petit lecteur MP3 en bas à droite !
          </p>
          <div class="text-xs text-base-content/70">
            La musique augmentera vos chances d'obtenir des shiny ! (non)
          </div>
        </div>
  
      </div>
    </div>
  </Modal>
</template>
  
<script setup lang="ts">
import Modal from '@/Components/UI/Modal.vue'
  
interface Props {
  show: boolean
  onClose: () => void
}
  
defineProps<Props>()
</script>
<template>
  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <Head title="Logs Laravel" />
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col items-center justify-start pt-10">
      <div class="w-full max-w-6xl mx-auto">
        <div class="flex flex-wrap items-center gap-3 mb-6">
          <Button @click="goDashboard" variant="outline" size="sm" class="font-bold">← Dashboard</Button>
          <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider flex items-center gap-2 mx-auto justify-center">
            <span>📝</span> Logs Laravel
          </h1>
          <div class="ml-auto flex items-center gap-3">
            <Input v-model="search" placeholder="Rechercher dans les logs..." class="w-64" size="sm" />
            <Button @click="refreshInertia" variant="primary" size="sm">Rafraîchir</Button>
            <Button v-if="!debug" @click="goDebug" variant="secondary" size="sm" class="font-bold text-white shadow-lg">Mode debug</Button>
            <Button v-else @click="goNormal" variant="secondary" size="sm" class="font-bold">Mode normal</Button>
          </div>
        </div>
        <div ref="logContainer" class="border border-base-400 rounded-xl bg-base-100/80 p-0 shadow-lg overflow-auto px-8" style="height: 80vh;">
          <pre class="whitespace-pre-wrap text-xs m-0 p-6 pl-8">
            <template v-if="!debug">
              <template v-for="(line, idx) in filteredLogs" :key="idx">
                <span :class="['log-line', getLineClass(line)]">{{ line }}</span>
              </template>
            </template>
            <template v-else>
              <template v-for="(entry, idx) in filteredDebugLogs" :key="idx">
                <span :class="['log-line', getDebugLineClass(entry.type)]">{{ entry.line }}</span>
              </template>
            </template>
          </pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch, computed, onMounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';

const props = defineProps({
  logs: {
    type: Array,
    required: true,
  },
  debug: {
    type: Boolean,
    required: false,
    default: false,
  },
  debugLogs: {
    type: Array,
    required: false,
    default: () => [],
  },
});

const logs = ref(props.logs);
const debug = ref(props.debug);
const debugLogs = ref(props.debugLogs);
const search = ref('');
const logContainer = ref(null);

const goDashboard = () => {
  router.visit('/admin');
};

const refreshInertia = () => {
  if (debug.value) {
    router.visit('/admin/logs/debug', { preserveScroll: true, preserveState: true });
  } else {
    router.visit('/admin/logs', { preserveScroll: true, preserveState: true });
  }
};

const goDebug = () => {
  router.visit('/admin/logs/debug');
};
const goNormal = () => {
  router.visit('/admin/logs');
};

const scrollToBottom = () => {
  if (logContainer.value) {
    logContainer.value.scrollTop = logContainer.value.scrollHeight;
  }
};

const filteredLogs = computed(() => {
  if (!search.value) return logs.value;
  return logs.value.filter(line => line.toLowerCase().includes(search.value.toLowerCase()));
});

const filteredDebugLogs = computed(() => {
  if (!search.value) return debugLogs.value;
  return debugLogs.value.filter(entry => entry.line.toLowerCase().includes(search.value.toLowerCase()));
});

const getLineClass = (line) => {
  if (line.includes('local.ERROR') || line.includes('PDOException')) {
    return 'text-red-500';
  }
  return '';
};

const getDebugLineClass = (type) => {
  if (type === 'ERROR') return 'text-red-500';
  if (type === 'DEBUG') return 'text-yellow-400';
  if (type === 'INFO') return 'text-blue-400';
  if (type === 'PDO') return 'text-white';
  return '';
};

watch(logs, () => {
  nextTick(scrollToBottom);
});
watch(debugLogs, () => {
  nextTick(scrollToBottom);
});

onMounted(() => {
  scrollToBottom();
});
</script>

<style scoped>
pre {
  font-family: 'Fira Mono', 'Consolas', 'Menlo', monospace;
  margin: 0;
  line-height: 1;
  padding: 0;
}
.log-line {
  display: block;
  padding-left: 2em;
  text-indent: -2em;
  white-space: pre-wrap;
  word-break: break-word;
}
.text-red-500 {
  color: #ef4444;
}
.text-yellow-400 {
  color: #facc15;
}
.text-blue-400 {
  color: #60a5fa;
}
</style> 
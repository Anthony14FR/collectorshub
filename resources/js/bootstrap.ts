import axios from 'axios';

declare global {
    interface Window {
        axios: typeof axios;
    }
}

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (import.meta.env.VITE_APP_URL) {
  window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL;
} 
import { createApp } from 'vue';
import App from './App.vue';
import Diagnostic from './pages/Workflows/WorkflowDiagnostic.vue';

import PrimeVue from 'primevue/config';
import 'primeicons/primeicons.css';
import Aura from '@primeuix/themes/aura';


function mountIfExists(id, component) {
  const el = document.getElementById(id);
  if (el) {
    const app = createApp(component);

    app.use(PrimeVue, {
      theme: {
          preset: Aura,
          options: {
              prefix: 'p',
              darkModeSelector: '.p-dark',
              cssLayer: false,
          }
      }
  });

    app.mount(`#${id}`);
  }
}

mountIfExists('app', App);
mountIfExists('workflow-diagnostic', Diagnostic);

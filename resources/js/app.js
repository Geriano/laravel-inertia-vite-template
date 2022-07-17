import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import Themes from './themes'
import Swal from 'sweetalert2';
import { Inertia } from '@inertiajs/inertia';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mixin({
                methods: {
                    themes: () => Themes,
                },
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });

window.Swal = Swal
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    showCloseButton: true,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

window.Toast = Toast

Inertia.on('finish', () => {
    const { $flash } = usePage().props.value
    const { success, error, info, warning } = $flash
    
    if (success) {
      Toast.fire({
        text: success,
        timer: 3000,
        icon: 'success',
      })
    }
    
    if (error) {
      Toast.fire({
        text: error,
        icon: 'error',
      })
    }
    
    if (info) {
      Toast.fire({
        text: info,
        timer: 3000,
        icon: 'info',
      })
    }
    
    if (warning) {
      Toast.fire({
        text: warning,
        timer: 3000,
        icon: 'warning',
      })
    }
})
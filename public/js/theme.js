/**
 * VETCORESSEN - Sistema de temas Dark/Light mode con Alpine.js
 * 
 * ¿Qué hace? Gestiona el toggle entre dark y light mode con persistencia.
 * ¿Por qué? El usuario quiere poder cambiar entre modos claro y oscuro.
 * 
 * Prioridad de lectura:
 * 1. localStorage (preferencia guardada)
 * 2. prefers-color-scheme del sistema
 * 3. Fallback: dark (default del sistema)
 */

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        // Estado reactivo - true = dark mode
        isDark: true,

        /**
         * Inicializa el tema leyendo la preferencia guardada
         * Se llama automáticamente al registrar el store
         */
        init() {
            const saved = localStorage.getItem('vc_theme');
            if (saved) {
                this.isDark = saved === 'dark';
            } else {
                // Detectar preferencia del sistema operativo
                this.isDark = !window.matchMedia('(prefers-color-scheme: light)').matches;
            }
            this._apply();
        },

        /**
         * Alterna entre dark y light mode
         */
        toggle() {
            this.isDark = !this.isDark;
            localStorage.setItem('vc_theme', this.isDark ? 'dark' : 'light');
            this._apply();
        },

        /**
         * Aplica el tema al DOM con transición suave
         */
        _apply() {
            const html = document.documentElement;
            
            // Activar transición temporal para cambio suave
            html.classList.add('transitioning');

            if (this.isDark) {
                html.classList.remove('light');
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
                html.classList.add('light');
            }

            // Remover clase de transición después de completar
            setTimeout(() => {
                html.classList.remove('transitioning');
            }, 400);
        },
    });
});

// Sincronizar entre pestañas
window.addEventListener('storage', (e) => {
    if (e.key === 'vc_theme') {
        const isDark = e.newValue === 'dark';
        if (Alpine.store('theme').isDark !== isDark) {
            Alpine.store('theme').isDark = isDark;
            Alpine.store('theme')._apply();
        }
    }
});

// Re-aplicar el tema tras navegación de Livewire (SPA) para evitar pérdida de clase
document.addEventListener('livewire:navigated', () => {
    if (window.Alpine) {
        Alpine.store('theme')._apply();
    }
});

// Prevención FOUC: Auto-aplicar el tema INMEDIATAMENTE antes de que el body se renderice.
(function() {
    const saved = localStorage.getItem('vc_theme');
    const isDark = saved ? saved === 'dark' : !window.matchMedia('(prefers-color-scheme: light)').matches;
    const html = document.documentElement;
    if (isDark) {
        html.classList.add('dark');
        html.classList.remove('light');
    } else {
        html.classList.add('light');
        html.classList.remove('dark');
    }
})();

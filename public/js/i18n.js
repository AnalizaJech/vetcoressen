/**
 * VETCORESSEN - Sistema de internacionalización (i18n) con Alpine.js
 */

document.addEventListener("alpine:init", () => {
    Alpine.store("i18n", {
        locale: localStorage.getItem("vc_locale") || "es",
        dict: {},
        loaded: false,

        async init() {
            await this.loadTranslations(this.locale);
        },

        async loadTranslations(lang) {
            this.dict = {};
            this.loaded = false;
            try {
                const response = await fetch(`/locales/${lang}.json`);
                if (response.ok) {
                    const dictionary = await response.json();
                    if (!dictionary || typeof dictionary !== 'object' || Array.isArray(dictionary)) {
                        throw new Error('Translation dictionary must be an object');
                    }
                    this.dict = dictionary;
                } else {
                    throw new Error(`Unable to load translations (${response.status})`);
                }
            } catch (e) {
                console.error("Failed to load translations", e);
            } finally {
                this.loaded = true;
                window.dispatchEvent(new CustomEvent('language-changed'));
            }
        },

        async setLocale(lang) {
            this.loaded = false;
            this.locale = lang;
            localStorage.setItem("vc_locale", lang);
            await this.loadTranslations(lang);
        },

        toggle() {
            this.setLocale(this.locale === "en" ? "es" : "en");
        },

        t(key, fallback = null) {
            this.locale; // Ensure Alpine tracks this dependency for reactivity
            if (!this.loaded || !this.dict) return fallback !== null ? fallback : '';
            if (!key) return fallback !== null ? fallback : '';
            
            const keys = key.split(".");
            let result = this.dict;
            for (const k of keys) {
                if (result && typeof result === "object" && k in result) {
                    result = result[k];
                } else {
                    return fallback !== null ? fallback : '';
                }
            }
            return result;
        }
    });
});

function updateDocumentTitle() {
    const metaKey = document.querySelector('meta[name="current-title-key"]')?.content;
    const store = Alpine.store('i18n');
    if (!metaKey || !store) return;
    
    let translated = store.t('title.' + metaKey) || 
                     store.t('sidebar.' + metaKey) || 
                     store.t('nav.' + metaKey) || 
                     store.t('page.' + metaKey);
                     
    if (translated) {
        document.title = `${translated} - VETCORESSEN`;
    }
}

window.addEventListener('language-changed', updateDocumentTitle);
document.addEventListener('livewire:navigated', updateDocumentTitle);
document.addEventListener('alpine:initialized', () => {
    setTimeout(updateDocumentTitle, 50);
});

window.addEventListener("storage", (e) => {
    if (e.key === "vc_locale" && e.newValue) {
        Alpine.store("i18n").setLocale(e.newValue);
    }
});

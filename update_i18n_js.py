import re

with open(r'c:\xampp\htdocs\vetcoressen\public\js\i18n.js', 'r', encoding='utf-8') as f:
    content = f.read()

# We need to replace the content inside Alpine.store('i18n', { ... }) to fetch from /locales
# We can just replace the whole file since it's standard.

new_content = """/**
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
            try {
                const response = await fetch(`/locales/${lang}.json`);
                if (response.ok) {
                    this.dict = await response.json();
                } else {
                    console.error("Error loading translations");
                }
            } catch (e) {
                console.error("Failed to load translations", e);
            } finally {
                this.loaded = true;
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
            if (!this.loaded || !this.dict) return fallback !== null ? fallback : key;
            const keys = key.split(".");
            let result = this.dict;
            for (const k of keys) {
                if (result && typeof result === "object" && k in result) {
                    result = result[k];
                } else {
                    return fallback !== null ? fallback : key;
                }
            }
            return result;
        }
    });
});

window.addEventListener("storage", (e) => {
    if (e.key === "vc_locale" && e.newValue) {
        Alpine.store("i18n").setLocale(e.newValue);
    }
});
"""

with open(r'c:\xampp\htdocs\vetcoressen\public\js\i18n.js', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Updated i18n.js")

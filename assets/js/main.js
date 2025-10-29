document.addEventListener('DOMContentLoaded', () => {
    'use strict'

    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    setTheme(getPreferredTheme())

    const showActiveTheme = theme => {
        const themeSwitcher = document.querySelector('[data-bs-theme-toggle]')
        if (!themeSwitcher) {
            return
        }
        const themeSwitcherIcon = themeSwitcher.querySelector('i')
        const activeThemeIcon = document.querySelector(`[data-bs-theme-value="${theme}"] i`).getAttribute('class')
        
        themeSwitcherIcon.setAttribute('class', activeThemeIcon)
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme()
        if (!storedTheme) {
            setTheme(getPreferredTheme())
        }
    })

    window.addEventListener('DOMContentLoaded', () => {
        showActiveTheme(getPreferredTheme())

        document.querySelectorAll('[data-bs-theme-value]')
            .forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault()
                    const theme = toggle.getAttribute('data-bs-theme-value')
                    setStoredTheme(theme)
                    setTheme(theme)
                    showActiveTheme(theme)
                })
            })
    })
});
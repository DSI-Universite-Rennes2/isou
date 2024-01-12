/*!
  * Adapté de l'exemple donné sur la documentation de bootstrap.
  *
  * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
  * Copyright 2011-2022 The Bootstrap Authors
  * Licensed under the Creative Commons Attribution 3.0 Unported License.
  */
(() => {
    'use strict'

    // Récupère la préférence de thème stockée dans le navigateur.
    const storedTheme = localStorage.getItem('theme')

    // Fonction pour calculer le thème préféré en fonction de la préférence stocké ou du schéma de couleur actuel du navigateur.
    const getPreferredTheme = () => {
        if (storedTheme) {
            return storedTheme
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    // Fonction pour changer le schéma de couleur et adapte le libellé dans le menu des préférences.
    const setTheme = function (theme) {
        if (theme !== 'dark' && theme !== 'light') {
            theme = 'light';
        }

        document.documentElement.setAttribute('data-bs-theme', theme);

        localStorage.setItem('theme', theme);

        if (theme === 'dark') {
            document.getElementById('toggle-theme').innerHTML = '<i aria-hidden="true" class="bi-brightness-high-fill"> </i>Activer le thème clair';
        } else {
            document.getElementById('toggle-theme').innerHTML = '<i aria-hidden="true" class="bi-moon-fill"> </i>Activer le thème sombre';
        }
    }

    // Change le schéma de couleur.
    setTheme(getPreferredTheme())

    // Détecte le changement de schéma de couleur via le menu de préférence.
    document.getElementById('toggle-theme').addEventListener('click', () => {
        if (document.documentElement.getAttribute('data-bs-theme') === 'light') {
            setTheme('dark');
        } else {
            setTheme('light');
        }
    })

    // Détecte les changements de schéma de couleur du navigateur.
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (storedTheme !== 'light' || storedTheme !== 'dark') {
            setTheme(getPreferredTheme())
        }
    })
})()

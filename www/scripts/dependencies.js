/**
 * Script appelé sur le formulaire d'ajout de contenu dans un groupe de dépendances.
 *
 */

/**
 * Fonction pour faire fonctionner la barre d'onglet du formulaire d'ajout de dépendances.
 *
 * - retire la classe 'active' sur l'ancien onglet et l'ancien contenu associé.
 * - ajoute la classe 'active' sur le nouvel onglet sélectionné et le contenu associé.
 *
 */
function toggleBackends(link) {
    var oldlink = document.querySelector('#isou-dependencies-contents-backends-dd > ul > li > a.active');
    if (oldlink) {
        oldlink.classList.remove('active');
        var oldpane = document.getElementById(oldlink.getAttribute('aria-controls'));
        if (oldpane) {
            oldpane.classList.remove('active');
        }
    }

    link.classList.add('active');
    var newpane = document.getElementById(link.getAttribute('aria-controls'));
    if (newpane) {
        newpane.classList.add('active');
    }
}

// Récupère chaque onglet représentant un backend de monitoring.
var links = document.querySelectorAll('#isou-dependencies-contents-backends-dd > ul > li > a');
for (var i = 0; i < links.length; i++) {
    if (i === 0) {
        // Rend actif par défaut le premier onglet.
        toggleBackends(links[i]);
    }

    // Ajoute un écouteur pour chaque clic effectué sur un onglet.
    links[i].addEventListener('click', function(event) {
        event.preventDefault();
        toggleBackends(event.target);
    });
}

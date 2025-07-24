# language: fr
# @navigation @liste
Fonctionnalité: Teste la navigation sur la vue "liste".

  Scénario: Sur la vue liste, je dois voir la liste des services.
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "Liste"
    Alors je devrais voir "Outils collaboratifs"
    Et je devrais voir "Applications formation/recherche"
    Et je devrais voir "Applications métiers"
    Et je devrais voir "Réseau"

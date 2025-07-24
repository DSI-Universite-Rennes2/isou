# language: fr
# @admin @dépendances
Fonctionnalité: Teste la navigation sur la vue "dépendances".

  Scénario: Sur la vue dépendances, je dois voir le message "Groupes instables".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "dépendances"
    Alors je devrais voir "Groupes instables"

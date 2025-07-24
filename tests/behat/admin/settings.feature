# language: fr
# @admin @configuration
Fonctionnalité: Teste la navigation sur la vue "configuration".

  Scénario: Sur la vue configuration, je dois voir le message "Page d'accueil par défaut".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "configuration"
    Alors je devrais voir "Page d'accueil par défaut"

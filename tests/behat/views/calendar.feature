# language: fr
# @navigation @calendrier
Fonctionnalité: Teste la navigation sur la vue "calendrier".

  Scénario: Sur la vue calendrier, je dois voir le message "Liste des opérations de maintenance prévues.".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "Calendrier"
    Alors je devrais voir "Liste des opérations de maintenance prévues."

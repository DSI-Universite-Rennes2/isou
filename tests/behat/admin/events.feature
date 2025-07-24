# language: fr
# @admin @évènements
Fonctionnalité: Teste la navigation sur la vue "évènements".

  Scénario: Sur la vue évènements, je dois voir le message "Aucune interruption enregistrée.".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "évènements"
    Alors je devrais voir "Aucune interruption enregistrée."

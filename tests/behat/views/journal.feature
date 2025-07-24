# language: fr
# @navigation @journal
Fonctionnalité: Teste la navigation sur la vue "journal".

  Scénario: Sur la vue journal, je dois voir le message "Journal des services monitorés".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "Journal"
    Alors je devrais voir "Journal des services monitorés"

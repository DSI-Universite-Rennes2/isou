# language: fr
# @navigation @rss
Fonctionnalité: Teste la navigation sur la vue "Flux RSS".

  Scénario: Sur la vue flux RSS, je dois voir le message "Le suivi par flux RSS n'est pas activé.".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "Flux RSS"
    Alors je devrais voir "Le suivi par flux RSS n'est pas activé."

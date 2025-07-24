# language: fr
# @admin @statistiques
Fonctionnalité: Teste la navigation sur la vue "statistiques".

  Scénario: Sur la vue statistiques, je dois voir le texte "Nombre de résultat par page".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "statistiques"
    Alors je devrais voir "Nombre de résultat par page"

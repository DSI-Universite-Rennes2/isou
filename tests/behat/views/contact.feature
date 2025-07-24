# language: fr
# @navigation @contact
Fonctionnalité: Teste la navigation sur la vue "contact".

  Scénario: Sur la vue contact, je dois voir le message "Page en construction".
    Sachant que je suis sur la page d'accueil
    Lorsque je clique sur le lien "Contact"
    Alors je devrais voir "Page en construction"

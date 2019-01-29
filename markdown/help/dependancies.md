# <span id="dependances"></span>Dépendances

Les dépendances permettent de définir l'état d'un service en fonction d'un autre service. Il est possible de définir un ou plusieurs groupes de dépendances pour les états instables et indisponibles de chaque service.

## <span id="groupes"></span>Groupes
Il existe 2 types de groupes:
- les groupes redondés, où tant qu'il y a au moins une dépendance en état de fonctionnement, l'état du service n'est pas altéré
- les groupes non redondés, où dès qu'il y a une dépendance instable ou indisponible, l'état du service est altéré

# <span id="services"></span>Services

Il existe différents types de services. Seuls les services de type Isou peuvent être affichés sur les pages publiques.

L'état des services Isou peuvent dépendre de l'état de d'autres services Isou, mais aussi Nagios ou Shinken.

## <span id="isou"></span>Services Isou

### <span id="visibilite"></span>Visibilité
Il est possible de ne pas afficher un service Isou sur les pages publiques.

Par exemple, on peut définir un service "base de données", qui serait utilisé par plusieurs autres services indépendants. L'état d'un service base de données n'est pas intéressant pour l'utilisateur final. Par contre, en créant ce service Isou masqué, il est possible de définir un évènement de maintenance, qui sera répercuté automatiquement sur tous les autres services dépendants de cette base de données.

### <span id="verrouillage"></span>Verrouillage
Il est possible de forcer l'état d'un service Isou. Peu importe l'état de ses dépendances, c'est la valeur utilisée pour forcer l'état du service qui sera affichée sur les pages publiques.

## <span id="shinken"></span>Services Shinken

L'ajout de nouveaux services Shinken peut s'effectuer par une expression régulière.

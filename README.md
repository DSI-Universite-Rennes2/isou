# ISOU

Isou est une application permettant d'informer de l'état des services numériques d'une structure.

## Fonctionnalités
- propose différentes vues des états passés, présents et à venir
- s'interface avec un ou plusieurs logiciels de monitoring comme Nagios ou Shinken
- permet de regrouper les services en catégorie
- dispose de plusieurs types d'évènements
- garde un historique des incidents
- utilise les notifications web pour avertir d'un incident en cours

## Configuration
### Configuration requise
- Serveur HTTP (ex: Apache, Nginx)
- PHP 7.0 ou supérieur
- [Composer](https://getcomposer.org)
- Un shell PHP
- Un système de tâches planifiées (cronie, vixie-cron, dcron, etc)

### Configuration facultative
- Un système d'authentification CAS/LDAP
- Un système de monitoring Nagios
- Un système de monitoring Shinken/Thruk

## Installation et mise à jour
```bash
cd /chemin/installation
git clone git://git.renater.fr/isounagios.git isou
cd isou
cp -i distribution/config.php .
php composer.phar install
php upgrade.php
```

### Mise à jour
```bash
git pull origin master
php composer.phar install
php upgrade.php
```

## Fonctionnement
Isou permet de définir différents services numériques. Chaque service Isou peut être affecté par un évènement passé, présent ou à venir.
Les services Isou peuvent être associés à des services issus d'outils de monitoring comme Nagios ou Shinken. Un script exécuté aussi régulièrement
 que possible se charge de mettre à jour l'état d'un service Isou en fonction de ses dépendances associées.

## Développements à venir
(ROADMAP)[ROADMAP.md]

## Problèmes courants
### Isou et Nagios
Pour fonctionner avec Nagios, Isou doit être en mesure de lire le fichier `status.dat` de Nagios. Il faut donc :
- soit installer Isou sur le même serveur que Nagios
- soit exposer le fichier `status.dat` en http

### Notifications Web
Pour faire fonctionner les notifications web avec Firefox 60 ESR, il faut activer les flags `dom.serviceWorkers.enabled` et `dom.push.enabled` sur la page `about:config`.
Voir aussi [https://caniuse.com/#feat=serviceworkers](https://caniuse.com/#feat=serviceworkers).

## Licence
(Domaine public)[LICENSE]

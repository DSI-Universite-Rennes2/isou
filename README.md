# ISOU

Isou est une application permettant d'informer de l'état des services numériques d'une structure.

## Fonctionnalités
- propose différentes vues des états passés, présents et à venir
- s'interface avec un ou plusieurs logiciels de monitoring comme Nagios (en natif), Nagios, Icinga, Shinken et Naemon (via Thruk) ou Zabbix (en natif)
- permet de regrouper les services en catégorie
- dispose de plusieurs types d'évènements
- garde un historique des incidents
- utilise les notifications web pour avertir d'un incident en cours

## Configuration
### Configuration requise
- Serveur HTTP (ex: Apache, Nginx)
- PHP 8.2 ou supérieur
- [Composer](https://getcomposer.org)
- Un shell PHP
- Un système de tâches planifiées (cronie, vixie-cron, dcron, etc)

### Configuration facultative
- Un système d'authentification CAS/LDAP
- Un système de monitoring Nagios
- Un système de monitoring Thruk
- Un système de monitoring Zabbix

## Installation et mise à jour
```bash
# Installer composer (https://getcomposer.org/)
cd /chemin/installation
git clone git://git.renater.fr/isou.git
cd isou
cp -i distribution/config.php .
composer install
```

### Mise à jour
```bash
git pull origin master
composer install
```

## Fonctionnement
Isou permet de définir différents services numériques. Chaque service Isou peut être affecté par un évènement passé, présent ou à venir.
Les services Isou peuvent être associés à des services issus d'outils de monitoring comme Nagios ou Shinken. Un script exécuté aussi régulièrement
 que possible se charge de mettre à jour l'état d'un service Isou en fonction de ses dépendances associées.

## Utilisateur par défaut
Par défaut, l'application génère un utilisateur local nommé `isou` dont le mot de passe est `isou`. Une fois l'authentification CAS configurée, il est fortement recommandé de ne pas utiliser la méthode d'authentification locale.

## Développements à venir
[ROADMAP](https://github.com/DSI-Universite-Rennes2/isou/milestones)

## Problèmes courants
### Isou et Nagios
Pour fonctionner avec Nagios (en natif), Isou doit être en mesure de lire le fichier `status.dat` de Nagios. Il faut donc :
- soit installer Isou sur le même serveur que Nagios
- soit exposer le fichier `status.dat` en http(s)

### Notifications Web
Pour faire fonctionner les notifications web avec Firefox 60 ESR, il faut activer les flags `dom.serviceWorkers.enabled` et `dom.push.enabled` sur la page `about:config`.
Voir aussi [https://caniuse.com/#feat=serviceworkers](https://caniuse.com/#feat=serviceworkers).

## Licence
Isou est dans le [domaine public](LICENSE).

Isou utilise le CDN [Unpkg.com](https://unpkg.com/) pour le thème [bootstrap](https://getbootstrap.com/).

## Reporting security issues
We take security seriously. If you discover a security issue, please bring it to their attention right away!

Please **DO NOT** file a public issue, instead send your report privately to [foss-security@univ-rennes2.fr](mailto:foss-security@univ-rennes2.fr).

Security reports are greatly appreciated and we will publicly thank you for it.

# <span id="configuration"></span>Configuration générale
L'onglet [configuration](../configuration) permet de gérer la configuration générale de l'application.

## <span id="apparence"></span>Apparence
Le sous-onglet [apparence](../configuration/apparence) permet de :
- modifier le nom de l'application
- choisir les vues présentées sur la page d'accueil
- sélectionner le thème graphique de l'application

## <span id="authentification"></span>Authentification
Le sous-onglet [authentification](../configuration/authentification) gère les différents modules d'authentification.
Il existe 2 modules :
- [locale](../configuration/authentification/manual)
- [CAS](../configuration/authentification/cas)

### <span id="authentification-locale"></span>Authentification locale
Ce module sert uniquement à ouvrir une session administrateur après l'installation. Une fois l'authentification CAS configurée, testée et validée, ce module DOIT être désactivé.

### <span id="authentification-cas"></span>Authentification CAS
Ce module permet de s'authentifier auprès d'un [serveur CAS](https://www.apereo.org/projects/cas). Il peut être couplé avec un serveur LDAP afin d'attribuer automatiquement des droits aux utilisateurs.

## <span id="utilisateurs"></span>Utilisateurs
Le sous-onglet [utilisateurs](../configuration/utilisateurs) affiche tous les utilisateurs enregistrés dans la table `users` d'Isou.

## <span id="monitoring"></span>Monitoring

### <span id="isou"></span>Isou
Le sous-onglets [isou](../configuration/monitoring/isou) permet de configurer les options d'affichage des évènements Isou.

### <span id="nagios"></span>Nagios
Le sous-onglets [nagios](../configuration/monitoring/nagios) permet d'activer l'échange de données entre le logiciel de monitoring [Nagios](https://www.nagios.org/) et Isou, par l'intermédiaire du fichier `status.dat`.
Pour cela, il est impératif qu'Isou puisse lire ce fichier, soit en étant installé sur le même serveur que Nagios, soit en exposant via HTTP le fichier `status.dat`.

### <span id="thruk"></span>Thruk
Le sous-onglets [thruk](../configuration/monitoring/thruk) permet d'activer l'échange de données entre le logiciel de monitoring [Thruk](http://www.thruk-monitoring.org/) et Isou, par l'intermédiaire de l'API REST de [Thruk](https://www.thruk.org/).

## <span id="notifications-web"></span>Notifications Web
Le sous-onglets [notifications web](../configuration/notifications) permet d'activer les notifications web (web push). Cette fonctionnalité est réservée aux utilisateurs connectés.

## <span id="rapport"></span>Rapport
Le sous-onglet [rapport](../configuration/rapport) permet d'activer et de configurer l'heure et l'adresse mail de destination d'un rapport quotidien des évènements de la veille.

## <span id="diagnostics"></span>Diagnostics
Le sous-onglet [diagnostics](../configuration/diagnostics) affiche tous les avertissements ou erreurs rencontrés par le logiciel Isou.

## <span id="informations"></span>Informations
Le sous-onglet [informations](../configuration/informations) contient diverses informations techniques sur le logiciel Isou.

## <span id="changelog"></span>Changelog
Le sous-onglet [changelog](../configuration/changelog) contient pour chaque version toutes les nouveautés et modifications développées sur le logiciel Isou.

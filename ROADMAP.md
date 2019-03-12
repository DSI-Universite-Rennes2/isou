# Roadmap

## Vues
### Liste
- [ ] simplifier l'affichage des dates (ex: si un évènement a lieu le même jour, afficher une seule fois la date)
- [ ] mettre un indice visuel pour afficher les évènements passés et à venir

## Administration
### Évènements
- [ ] ajouter le javascript pour pouvoir changer de type d'évènements à la volée
- [ ] supprimer les descriptions en double
- [ ] permettre de réutiliser les descriptions d'évènements

### Annonce
- [ ] remplacer le module annonce par un type d'évènements

### Statistiques
- [ ] générer des graphiques

### Catégorie
- [ ] proposer la notion de campus ; un campus contiendrait plusieurs services
    - [ ] services.univ-rennes2.fr/isou/index.php/actualite/campus/saint-brieuc afficherai uniquement les services pour ce campus
        ou services.univ-rennes2.fr/isou/index.php/campus/saint-brieuc/actualite

## Développements
### Authentification
- [ ] permettre de modifier son mot de passe
- [ ] permettre de modifier les droits

### Cron
- [ ] mettre en place un système d'enregistrement de site
- [ ] détecter la disponibilité des mises à jour sur sourcesup
- [ ] ajouter une variable pour le debug/trace

### Code
- [ ] utiliser pimple
- [ ] remplacer la bibliothèque `monolog` par les fonctions natives `openlog()`/`syslog()`

### Internationalisation
- [ ] utiliser gettext()

### InfluxDB
- [ ] créer un script d'export vers influxdb

### Fédération
- [ ] créer un plugin fédération permettant de lier des services Isou d'une autre instance
- [ ] ajouter un attribut `id` dans le fichier json

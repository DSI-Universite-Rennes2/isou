# Roadmap

## Vues
- [ ] corriger la vue 'journal'
    - [ ] marquer comme en test
- [x] corriger le thème classic
    - [ ] marquer comme obsolete
- [ ] rendre les vues modulables, comme l'authentification et le monitoring

## Administration
### Évènements
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

### Internationalisation
- [ ] utiliser gettext()

### InfluxDB
- [ ] créer un script d'export vers influxdb

### Fédération
- [ ] créer un plugin fédération permettant de lier des services Isou d'une autre instance
- [ ] ajouter un attribut `id` dans le fichier json

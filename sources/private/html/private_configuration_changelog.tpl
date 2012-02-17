<div id="content">

<h2>Build 2012-02-16.1</h2>
<ul>
	<li>Nouveautés
		<ul>
			<li>Scission de la page configuration en 3 nouvelles pages (configuration générale, avancée et changelog)</li>
			<li>Réorganisation de l'arborescence de l'application
			<ul>
				<li>./sources/public devient le répertoire sur lequel le serveur web doit pointer</li>
				<li>./sources/private est un répertoire qui ne devrait jamais être accessible via le serveur web</li>
			</ul>
			</li>
			<li>Nouveau système de mise à jour/installation
			<ul>
				<li>Le nouveau système se repose complètement sur GIT</li>
			</li>
		</ul>
	</li>
	<li>Améliorations diverses
		<ul>
			<li>Les services verrouillés/forcés sont affichés au-dessus de la liste des évènements, et non dans la liste parmi les autres évènements</li>
			<li>Remplacement de la commande <code>readline</code> (non disponible dans les paquets debian) par <code>fgets</code></li>
		</ul>
	</li>
	<li>Correction de bugs
		<ul>
			<li>Suppression des transactions PDO qui semblent poser problème avec sqlite3 (et entrainaient le plantage du script exécuté par le cron)</li>
			<li>Correction d'un bug d'affichage de l'icône du calendrier sur la page évènement</li>
			<li>Correction de l'affichage de l'année dans les exemples données sur la page 'évènement'</li>
		</ul>
	</li>
</ul>

</div>

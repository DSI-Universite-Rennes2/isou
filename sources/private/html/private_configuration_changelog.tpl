<div id="content">

{if count($newversion) > 0}
{if isset($smarty.post.success)}
	<p id="update">{$smarty.post.success}</p>
{else}
	<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=changelog&amp;version={$smarty.get.version}#form-newvar" id="form-newvar">
	<h2>Nouvelles variables</h2>

	{if isset($newversion.201202161)}
		{if isset($smarty.post.error.localmail)}
		{if count($smarty.post.error.localmail) == 0}
			<p class="update">Le mail a été ajouté.</p>
		{else}
			{foreach $smarty.post.error.localmail as $error}
			<p class="update">{$error}</p>
			{/foreach}
		{/if}
		{/if}

		<p>
			<span class="label"><label for="localmail">Expéditeur des mails envoyés par {$smarty.const.NAME}.</label><br />
			<span class="example">note : si l'expéditeur n'est pas renseigné, c'est l'adresse des destinataires qui sera utilisée</span></span>
			<span class="input">
			<input type="text" name="localmail" id="localmail" value="{$smarty.post.localmail|default:''}" />
			</span><br />
			<span class="key">local_mail</span><br />
		</p>

		{if isset($smarty.post.error.autobackup)}
		{if count($smarty.post.error.autobackup) == 0}
			<p class="update">La clé 'auto_backup' a été ajoutée.</p>
		{else}
			{foreach $smarty.post.error.autobackup as $error}
			<p class="update">{$error}</p>
			{/foreach}
		{/if}
		{/if}

		<p>
			<span class="label"><label for="autobackup">Activer la sauvegarde automatique lors de mise à jour ?</label><br />
			<span class="example">attention :  peut considérablement ralentir le processus de mise à jour</span></span>
			<span class="input">
			{html_options name='autobackup' options=$autobackup selected=$smarty.post.autobackup|default:'1'}
			</span><br />
			<span class="key">auto_backup</span><br />
		</p>
	{/if}

	<input type="submit" name="update" value="enregistrer" />
	</form>
{/if}
{/if}

<h2 id="index">Index des différentes mises à jour</h2>
<ul class="fastlink">
	<li><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#2013.1">Dernière version</a></li>
	<li><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#2013.1">Build 2013.1</a></li>
	<li><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#2012-02-16.1">Build 2012-02-16.1</a></li>
	<li><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#2012-03-16.1">Build 2012-03-16.1</a></li>
</ul>

<h2 id="2013.1">Build 2013.1</h2>
<ul>
	<li>Nouveautés
		<ul>
			<li>Nouveau système de stockage des dates en base de données</li>
			<li>Suppression de l'interface de mise à jour par le web</li>
			<li>Possibilité d'exporter les historiques des interruptions de services au format CSV</li>
		</ul>
	</li>
	<li>Correction de bugs
		<ul>
			<li>Correction du bug provoqué lors des changements d'heure hiver/été (bug reporté par l'université de La Rochelle)</li>
			<li>Correction de bugs dans le script d'installation (bugs reportés par l'université de Bretagne occidentale)</li>
		</ul>
	</li>
</ul>
<p class="fastlink"><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#index">Retour à l'index</a></p>

<h2 id="2012-02-16.1">Build 2012-02-16.1</h2>
<ul>
	<li>Nouveautés
		<ul>
			<li>Mise à jour de sécurité
			<ul>
				<li>Corrections de toutes les failles XSS présentes dans les pages d'administration</li>
			</ul>
			</li>
			<li>Nouvelles variables
			<ul>
				<li>auto_backup : permet de définir la création automatique d'une sauvegarde lors des mises à jour</li>
				<li>local_mail : permet de définir l'expéditeur de mails générés par l'application</li>
			</ul>
			</li>
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
			</ul>
			</li>
			<li>Création de tests unitaires</li>
		</ul>
	</li>
	<li>Améliorations diverses
		<ul>
			<li>Les services verrouillés/forcés sont affichés au-dessus de la liste des évènements, et non dans la liste parmi les autres évènements</li>
			<li>Les services verrouillés/forcés état de fonctionnement, n'engendre plus la création d'un évènement</li>
			<li>Remplacement de la commande <code>readline</code> (non disponible dans les paquets debian) par <code>fgets</code></li>
			<li>Ajout des services verrouillés/forcés dans le rapport quotidien</li>
		</ul>
	</li>
	<li>Correction de bugs
		<ul>
			<li>Suppression des transactions PDO qui semblent poser problème avec sqlite3 (et entrainaient le plantage du script exécuté par le cron)</li>
			<li>Correction d'un bug d'affichage de l'icône du calendrier sur la page évènement</li>
			<li>Correction de l'affichage de l'année dans les exemples données sur la page 'évènement'</li>
			<li>Correction d'un bug qui empêchait le lancement du cron hebdomadaire</li>
		</ul>
	</li>
</ul>
<p class="fastlink"><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#index">Retour à l'index</a></p>

<h2 id="2012-03-16.1">Build 2012-03-16.1</h2>
<ul>
	<li>Améliorations diverses
		<ul>
			<li>La modification d'un évènement dont le service passerait à l'état 'en fonctionnement', 
				engendre la suppression automatique de l'évènement.</li>
			<li>Les numéros de version des différentes bibliothèques tierces utilisées dans Isou sont maintenant affichés 
				dans le menu "configuration" rubrique "générale".</li>
			<li>Possibilité d'indiquer explicitement le chemin vers le répertoire du fichier config.php lors d'une mise à jour en ligne de commande</li>
			<li>Prise en compte de l'oscillation d'état dans Nagios</li>
		</ul>
	</li>
	<li>Correction de bugs
		<ul>
			<li>Mise à jour du champ "last_update" après une installation d'une nouvelle version</li>
		</ul>
	</li>
</ul>
<p class="fastlink"><a href="{$smarty.const.URL}/index.php/configuration?type=changelog#index">Retour à l'index</a></p>

</div>

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
			<span class="label"><label for="autobackup">Activer la sauvegarde automatique lors de mise à jour ?</label></span>
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

<h2>Build 2012-02-16.1</h2>
<ul>
	<li>Nouveautés
		<ul>
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

</div>

<div id="content">

<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-general" id="form-general">
	<h2>Générale</h2>
	{if isset($smarty.post.error.general)}
	{if count($smarty.post.error.general) === 0}
	<p id="update">Les mises à jour ont bien été enregistrées</p>
	{else}
	<ul id="update">
		{foreach $smarty.post.error.general as $error}
		<li>{$error}</li>
		{/foreach}
	</ul>
	{/if}
	{/if}
	<p>
		<span class="label"><label for="tolerance">Tolérance acceptée pour un service interrompu.</label><br />
		<span class="example">exemple : ne pas afficher un service interrompu moins de 5 minutes</span></span>
		<span class="input"><input type="text" name="tolerance" id="tolerance" value="{$smarty.post.tolerance|default:$CFG.tolerance}" /></span><br />
		<span class="key">tolerance</span>
	</p>

	<p>
		<label class="label" for="dailycronhour">Heure d'exécution du cron quotidien.</label>
		<span class="input"><input type="text" name="dailycronhour" id="dailycronhour" value="{$smarty.post.dailycronhour|default:$CFG.daily_cron_hour}" /></span><br />
		<span class="key">daily_cron_hour</span>
	</p>

	<p>
		<span class="label"><label for="localpassword">Mot de passer permettant l'authentification en local, sans passer par CAS.</label><br />
		<span class="example">note : si le mot de passe n'est pas renseigné, l'authentification locale est désactivée</span></span>
		<span class="input"><input type="text" name="localpassword" id="localpassword" value="{if isset($smarty.post.localpassword)}{$smarty.post.localpassword}{else}{$CFG.local_password}{/if}" /></span><br />
		<span class="key">local_password</span>
	</p>

	<p>
		<input type="submit" name="generalsubmit" />
	</p>
</form>



<h2>Plages IP</h2>
<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-iplocal" id="form-iplocal">
{if isset($smarty.post.error.iplocal)}
{if count($smarty.post.error.iplocal) == 0}
	<p id="update">La plage a été ajoutée.</p>
{else}
	{foreach $smarty.post.error.iplocal as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}
{if isset($smarty.post.error.ip_local)}
{if count($smarty.post.error.ip_local) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.ip_local as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="iplocal">Plage IP des campus de l'université.</label>
	<span class="input">
	{foreach $CFG.ip_local as $ip}
	{if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}
		<a href="{$smarty.const.URL}/index.php/configuration?action=drop&key=ip_local&index={$ip@iteration}#form-iplocal" title="supprimer la plage ip {if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	de <input type="text" name="iplocal1" id="iplocal1" value="{$smarty.post.iplocal1|default:''}" />
	à <input type="text" name="iplocal2" id="iplocal2" value="{$smarty.post.iplocal2|default:''}" /><input type="submit" name="iplocalsubmit" value="ajouter" />
	</span><br />
	<span class="key">ip_local</span><br />
</p>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-ipservice" id="form-ipservice">
{if isset($smarty.post.error.ipservice)}
{if count($smarty.post.error.ipservice) == 0}
	<p id="update">La plage a été ajoutée.</p>
{else}
	{foreach $smarty.post.error.ipservice as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}
{if isset($smarty.post.error.ip_service)}
{if count($smarty.post.error.ip_service) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.ip_service as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="ipservice">Plage IP du service administrant Isou.</label>
	<span class="input">
	{foreach $CFG.ip_service as $ip}
	{if isset($ip.1)}{$ip.0} - {$ip.1}{else}{$ip.0}{/if}
		<a href="{$smarty.const.URL}/index.php/configuration?action=drop&key=ip_service&index={$ip@iteration}#form-ipservice" title="supprimer la plage ip {if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	de <input type="text" name="ipservice1" id="ipservice1" value="{$smarty.post.ipservice1|default:''}" />
	à <input type="text" name="ipservice2" id="ipservice2" value="{$smarty.post.ipservice2|default:''}" /><input type="submit" name="ipservicesubmit" value="ajouter" />
	</span><br />

	<span class="key">ip_service</span>
</p>
</form>

<h2>Administrateurs</h2>
<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-adminusers" id="form-adminusers">

{if isset($smarty.post.error.adminusers)}
{if count($smarty.post.error.adminusers) == 0}
	<p id="update">L'administrateur a été ajouté.</p>
{else}
	{foreach $smarty.post.error.adminusers as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

{if isset($smarty.post.error.admin_users)}
{if count($smarty.post.error.admin_users) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.admin_users as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="adminusers">Liste des utilisateurs administrateurs.</label>
	<span class="input">
	{foreach $CFG.admin_users as $user}
	{$user}
		<a href="{$smarty.const.URL}/index.php/configuration?action=drop&key=admin_users&index={$user@iteration}#form-adminusers" title="supprimer l'utilisateur {$user}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	<input type="text" name="adminusers" id="adminusers" value="{$smarty.post.adminusers|default:''}" /><input type="submit" name="adminuserssubmit" value="ajouter" />
	</span><br />
	<span class="key">admin_users</span><br />
</p>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-adminmails" id="form-adminmails">
{if isset($smarty.post.error.adminmails)}
{if count($smarty.post.error.adminmails) == 0}
	<p id="update">Le mail a été ajouté.</p>
{else}
	{foreach $smarty.post.error.adminmails as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

{if isset($smarty.post.error.admin_mails)}
{if count($smarty.post.error.admin_mails) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.admin_mails as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}


<p>
	<span class="label"><label for="adminmails">Liste des mails administrateurs.</label><br />
	<span class="example">note : sert à la notification quotidienne</span></span>
	<span class="input">
	{foreach $CFG.admin_mails as $mail}
	{$mail}
		<a href="{$smarty.const.URL}/index.php/configuration?action=drop&key=admin_mails&index={$mail@iteration}#form-adminmails" title="supprimer l'utilisateur {$mail}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	<input type="text" name="adminmails" id="adminmails" value="{$smarty.post.adminmails|default:''}" /><input type="submit" name="adminmailssubmit" value="ajouter" />
	</span><br />
	<span class="key">admin_mails</span><br />
</p>
</form>


<h2 id="cron">Crons</h2>
{if isset($smarty.post.error.last_cron_update)}
	{if isset($smarty.post.error.last_cron_update.error_db)}
	<p id="update">{$smarty.post.error.last_cron_update.error_db}</p>
	{else}
	<p id="update">{$smarty.post.error.last_cron_update.none}</p>
	{/if}
{/if}
{if isset($smarty.post.error.last_daily_cron_update)}
	{if isset($smarty.post.error.last_daily_cron_update.error_db)}
	<p id="update">{$smarty.post.error.last_daily_cron_update.error_db}</p>
	{else}
	<p id="update">{$smarty.post.error.last_daily_cron_update.none}</p>
	{/if}
{/if}
{if isset($smarty.post.error.last_weekly_cron_update)}
	{if isset($smarty.post.error.last_weekly_cron_update.error_db)}
	<p id="update">{$smarty.post.error.last_weekly_cron_update.error_db}</p>
	{else}
	<p id="update">{$smarty.post.error.last_weekly_cron_update.none}</p>
	{/if}
{/if}
{if isset($smarty.post.error.last_yearly_cron_update)}
	{if isset($smarty.post.error.last_yearly_cron_update.error_db)}
	<p id="update">{$smarty.post.error.last_yearly_cron_update.error_db}</p>
	{else}
	<p id="update">{$smarty.post.error.last_yearly_cron_update.none}</p>
	{/if}
{/if}
<p>
	<label class="label">Dernier lancement du cron.</label>
	<span class="disabled">{if $CFG.last_cron_update == 0}Aucune valeur{else}{$CFG.last_cron_update|date_format:'%c'}{/if}</span>
		<a href="{$smarty.const.URL}/index.php/configuration?action=reset&key=last_cron_update#cron" title="réinitialiser la valeur de last_cron_update">
			<img alt="remettre à 0" src="{$smarty.const.URL}/images/action_reset.gif" />
		</a><br />
	<span class="key">last_cron_update</span><br />

	<label class="label">Dernier lancement du cron quotidien.</label>
	<span class="disabled">{if $CFG.last_daily_cron_update == 0}Aucune valeur{else}{$CFG.last_daily_cron_update|date_format:'%c'}{/if}</span>
		<a href="{$smarty.const.URL}/index.php/configuration?action=reset&key=last_daily_cron_update#cron" title="réinitialiser la valeur de last_daily_cron_update">
			<img alt="remettre à 0" src="{$smarty.const.URL}/images/action_reset.gif" />
		</a><br />
	<span class="key">last_daily_cron_update</span><br />


	<label class="label">Dernier lancement du cron hebdomadaire.</label>
	<span class="disabled">{if $CFG.last_weekly_cron_update == 0}Aucune valeur{else}{$CFG.last_weekly_cron_update|date_format:'%c'}{/if}</span>
		<a href="{$smarty.const.URL}/index.php/configuration?action=reset&key=last_weekly_cron_update#cron" title="réinitialiser la valeur de last_weekly_cron_update">
			<img alt="remettre à 0" src="{$smarty.const.URL}/images/action_reset.gif" />
		</a><br />
	<span class="key">last_weekly_cron_update</span><br />

	<label class="label">Dernier lancement du cron annuel.</label>
	<span class="disabled">{if $CFG.last_yearly_cron_update == 0}Aucune valeur{else}{$CFG.last_yearly_cron_update|date_format:'%c'}{/if}</span>
		<a href="{$smarty.const.URL}/index.php/configuration?action=reset&key=last_yearly_cron_update#cron" title="réinitialiser la valeur de last_yearly_cron_update">
			<img alt="remettre à 0" src="{$smarty.const.URL}/images/action_reset.gif" />
		</a><br />
	<span class="key">last_yearly_cron_update</span>
</p>
</form>

<h2>Mise à jour</h2>

<p>
	<span class="label">Numéro de version actuelle d'Isou.</span>
	<span class="input">{$CFG.version}</span><br />
	<span class="key">version</span>
</p>

<p>
	<span class="label">Dernière date de mise à jour d'Isou.</span>
	<span class="input">{$CFG.last_update|date_format:'%c'}</span><br />
	<span class="key">last_update</span>
</p>

<p>
	<span class="label">Dernière vérification de mise à jour d'Isou.</span>
	<span class="input">{$CFG.last_check_update|date_format:'%c'}</span><br />
	<span class="key">last_check_update</span>
</p>

</div>

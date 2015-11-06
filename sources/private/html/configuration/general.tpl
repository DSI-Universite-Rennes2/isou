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
		<span class="label"><label for="tolerance">Tolérance (en seconde) acceptée pour un service interrompu.</label><br />
		<span class="example">exemple : ne pas afficher un service interrompu moins de 300 secondes (5 minutes)</span></span>
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

<h2 id="configuration-des-menus">Configuration des menus</h2>
<form method="post" action="{$smarty.const.URL}/index.php/configuration#form-general" id="form-general">
	<fieldset>
		<legend>Configuration du menu</legend>
		{html_checkboxes name=menu options=$menu_options selected=$active_menu}
	</fieldset>

	<dl>
		<div>
			<dt><label for="default-menu">Page d'accueil par défaut</label></dt>
			<dd>{html_options id="default-menu" name=default_menu options=$active_menu_options selected=$CFG.default_menu}</dd>
		</div>
	</dl>

	<p>
		<input type="submit" name="menusubmit" />
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

<h2>Version des bibliothèques tierces utilisées</h2>

<p>
	<span class="label">HTMLPurifier</span>
	<span class="input">{$HTMLPurifierVersion}</span><br />
</p>

<p>
	<span class="label">jQuery</span>
	<span class="input" id="jquery-version">Non définie</span><br />
</p>

<p>
	<span class="label">phpCAS</span>
	<span class="input">{$phpCASVersion}</span>
</p>

<p>
	<span class="label">Smarty</span>
	<span class="input">{$smarty.version}</span><br />
</p>

</div>

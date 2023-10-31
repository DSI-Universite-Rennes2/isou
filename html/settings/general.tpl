<form method="post" action="{$smarty.const.URL}/index.php/configuration/general">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt>
				<label for="site-name">Nom du service</label>
			</dt>
			<dd>
				<input class="form-control" id="site-name" name="site_name" type="text" value="{$CFG.site_name}" />
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="site-header">Titre ou entête du service</label>
			</dt>
			<dd>
				<input class="form-control" id="site-header" name="site_header" type="text" value="{$CFG.site_header}" />
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="site-url">URL du service</label>
			</dt>
			<dd>
				<input class="form-control" id="site-url" name="site_url" type="text" value="{$CFG.site_url}" />
			</dd>
		</div>
	</dl>

	<fieldset>
		<legend>Page d'accueil par défaut</legend>
		{html_radios class="isou-radios" name="menu_default" options=$menus selected=$CFG.menu_default}
	</fieldset>

	<fieldset>
		<legend>Thèmes</legend>
		{html_radios class="isou-radios" name="theme" options=$themes selected=$CFG.theme}
	</fieldset>

	<fieldset>
		<legend>Recherche automatique de mises à jour</legend>
		{html_radios class="isou-radios" name="check_updates_enabled" options=$options_yes_no selected=$CFG.check_updates_enabled}
		<p class="help-block small">Une fois par jour, une requête HTTPS est envoyée au site Github.com pour vérifier si une nouvelle mise à jour d'Isou est disponible.</p>
	</fieldset>

	<fieldset>
		<legend>Collecte de statistiques</legend>
		{html_radios class="isou-radios" name="gather_statistics_enabled" options=$options_yes_no selected=$CFG.gather_statistics_enabled}
		<p class="help-block small">Une fois par mois, une requête HTTPS est envoyée à un serveur de l'Université Rennes 2 afin de collecter des statistiques.<br />
Seuls l'URL de votre instance, le numéro de version utilisé, la liste des plugins activés et leur version, ainsi que la date de la première et de la dernière requête sont enregistrés.<br />
Ces informations sont collectées uniquement pour connaître les utilisateurs actifs de l'application. Ces données ne seront ni rendues publiques, ni partagées à des tiers sans votre accord. Si un jour nous sommes amenés à collecter plus de données sur votre instance Isou, nous ferons en sorte de vous faire valider la collecte de ces nouvelles données (opt-in).</p>
	</fieldset>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

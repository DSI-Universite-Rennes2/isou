<form method="post" action="{$smarty.const.URL}/index.php/configuration/apparence">

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
		<legend>Affichage des menus</legend>
		{html_checkboxes name="menus_active" options=$menus selected=$menus_active}
	</fieldset>

	<fieldset>
		<legend>Page d'accueil par défaut</legend>
		{html_radios name="menu_default" options=$menus selected=$CFG.menu_default}
	</fieldset>

	<fieldset>
		<legend>Thèmes</legend>
		{html_radios name="theme" options=$themes selected=$CFG.theme}
	</fieldset>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration/apparence">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="site-name">Nom du service</label>
			</dt>
			<dd class="form-values-dd">
				<input class="input-extra-large" type="text" name="site_name" id="site-name" value="{$CFG.site_name}" />
			</dd>
		</div>

		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="site-header">Titre ou entête du service</label>
			</dt>
			<dd class="form-values-dd">
				<input class="input-extra-large" type="text" name="site_header" id="site-header" value="{$CFG.site_header}" />
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

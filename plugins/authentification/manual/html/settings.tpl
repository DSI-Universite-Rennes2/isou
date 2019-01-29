<form method="post" action="{$smarty.const.URL}/index.php/configuration/authentification/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-manual-enable">Activer</dt>
				<dd>
					{html_radios class="isou-radios" aria-labelledby="plugin-manual-enable" name="plugin_manual_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>
		</dl>
	</fieldset>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-shinken-enable">Activer</dt>
				<dd>
					{html_radios class="isou-radios" aria-labelledby="plugin-shinken-enable" name="plugin_shinken_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>

			<div class="form-group">
				<dt>
					<label for="plugin-shinken-path">URL de Thruk</label>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_shinken_path" id="plugin-shinken-path" value="{$plugin->settings->thruk_path}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-shinken-username">Nom d'utilisateur</label>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_shinken_username" id="plugin-shinken-username" value="{$plugin->settings->thruk_username}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-shinken-password">Mot de passe</label>
				</dt>
				<dd>
					<input class="form-control" size="40" type="password" name="plugin_shinken_password" id="plugin-shinken-password" value="{if empty($plugin->settings->thruk_password) === false}* * * * *{/if}" />
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

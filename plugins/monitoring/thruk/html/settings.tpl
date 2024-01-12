<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-thruk-enable">Activer</dt>
				<dd>
					{html_radios class="isou-radios" aria-labelledby="plugin-thruk-enable" name="plugin_thruk_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>

			<div class="form-group">
				<dt>
					<label for="plugin-thruk-path">URL de Thruk</label>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_thruk_path" id="plugin-thruk-path" value="{$plugin->settings->thruk_path}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-thruk-username">Nom d'utilisateur</label>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_thruk_username" id="plugin-thruk-username" value="{$plugin->settings->thruk_username}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-thruk-password">Mot de passe</label>
				</dt>
				<dd>
					<input class="form-control" size="40" type="password" name="plugin_thruk_password" id="plugin-thruk-password" value="{if empty($plugin->settings->thruk_password) === false}* * * * *{/if}" />
				</dd>
			</div>
		</dl>
	</fieldset>

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

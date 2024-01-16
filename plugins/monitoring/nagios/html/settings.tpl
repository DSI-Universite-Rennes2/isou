<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">

	{include file="common/messages_form.tpl"}
	{include file="common/messages_session.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-nagios-enable">Activer</dt>
				<dd>
					{html_radios class="isou-radios" aria-labelledby="plugin-nagios-enable" name="plugin_nagios_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-nagios-path">Chemin du fichier status.dat</label>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_nagios_path" id="plugin-nagios-path" value="{$plugin->settings->statusdat_path}" />
					<span class="d-block small" id="localauthenticationpath-aria-describedby">exemple : /var/share/nagios/status.dat</span>
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

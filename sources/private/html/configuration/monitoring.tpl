<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>Backend Nagios (status.dat)</legend>

		<dl>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="nagios-statusdat-enable">Activer</label>
				</dt>
				<dd class="form-values-dd">
					{html_radios id="nagios-statusdat-enable" name="nagios_statusdat_enable" options="{$options_yes_no}" selected="{$CFG.nagios_statusdat_enable}"}
				</dd>
			</div>

			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="nagios-statusdat-path">Chemin du fichier status.dat</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-extra-large" type="text" name="nagios_statusdat_path" id="nagios-statusdat-path" value="{$CFG.nagios_statusdat_path}" />
					<p id="localauthentificationpath-aria-describedby"><abbr>ex</abbr>: /var/share/nagios/status.dat</p>
				</dd>
			</div>
		</dl>

	</fieldset>

	<fieldset>
		<legend>Backend Shinken (Thruk)</legend>

		<dl>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="shinken-thruk-enable">Activer</label>
				</dt>
				<dd class="form-values-dd">
					{html_radios id="shinken-thruk-enable" name="shinken_thruk_enable" options="{$options_yes_no}" selected="{$CFG.shinken_thruk_enable}"}
				</dd>
			</div>

			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="shinken-thruk-path">URL de Thruk</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-extra-large" type="text" name="shinken_thruk_path" id="shinken-thruk-path" value="{$CFG.shinken_thruk_path}" />
				</dd>
			</div>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="shinken-thruk-username">Nom d'utilisateur</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-extra-large" type="text" name="shinken_thruk_username" id="shinken-thruk-username" value="{$CFG.shinken_thruk_username}" />
				</dd>
			</div>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="shinken-thruk-password">Mot de passe</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-extra-large" size="40" type="password" name="shinken_thruk_password" id="shinken-thruk-password" value="{$CFG.shinken_thruk_password}" />
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


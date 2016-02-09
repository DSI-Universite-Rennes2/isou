<form method="post" action="{$smarty.const.URL}/index.php/configuration/authentification">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>Authentification CAS</legend>
		<dl>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="authentification-cas-enabled">Activer l'authentification CAS</label>
				</dt>
				<dd class="form-values-dd">
					{html_radios id="authentification-cas-enabled" name="authentification_cas_enabled" options="{$options_yes_no}" selected="{$CFG.authentification_cas_enabled}"}
				</dd>
			</div>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="authentification-cas-admin-usernames">Liste des identifiants CAS ayant les droits administrateurs</label>
				</dt>
				<dd class="form-values-dd">
					<textarea class="textarea-large" name="authentification_cas_admin_usernames" id="authentification_cas_admin_usernames">{$cas_admin_usernames}</textarea>
				</dd>
			</div>
		</dl>
	</fieldset>

	<fieldset disabled="disabled">
		<legend>Authentification manuelle</legend>
		<dl>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="authentification-manual-enabled">Activer l'authentification manuelle</label>
				</dt>
				<dd class="form-values-dd">
					{html_radios id="authentification-manual-enabled" name="authentification_manual_enabled" options="{$options_yes_no}" selected="{$CFG.authentification_manual_enabled}"}
				</dd>
			</div>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="authentification-manual-path">URL permettant d'accéder à la page d'authentificaiton locale</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-large" type="text" id="authentification-manual-path" name="authentification_manual_path" value="{$CFG.authentification_manual_path}" />
				</dd>
			</div>
			<div class="form-information-dl-div">
				<dt class="form-topics-dt">
					<label for="authentification-manual-password">Mot de passer permettant l'authentification en local, sans passer par CAS.</label>
				</dt>
				<dd class="form-values-dd">
					<input class="input-large" type="password" id="authentification-manual-password" name="authentification_manual_password" value="{$CFG.authentification_manual_password}" />
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



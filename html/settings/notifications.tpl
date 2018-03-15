<form method="post" action="{$smarty.const.URL}/index.php/configuration/notifications">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="notification-enabled">Activer les notifications</label>
			</dt>
			<dd class="form-values-dd">
				{html_radios id="notification-enabled" name="notification_enabled" options=$options_yes_no selected=$CFG.notification_enabled}
			</dd>
		</div>

		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="notification-hour">Heure d'exécution du cron quotidien</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="notification_hour" id="notification-hour" value="{$CFG.notification_hour}" />
			</dd>
		</div>

		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="notification-receivers">Destinataires des notifications</label>
			</dt>
			<dd class="form-values-dd">
				<textarea class="textarea-large" name="notification_receivers" id="notification-receivers">{$receivers}</textarea>
			</dd>
		</div>

		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="notification-sender">Expéditeur utilisé pour l'envoi des notifications</label>
			</dt>
			<dd class="form-values-dd">
				<input class="input-large" type="text" name="notification_sender" id="notification-sender" value="{$CFG.notification_sender}" />
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>


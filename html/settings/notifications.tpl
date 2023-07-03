<form action="{$smarty.const.URL}/index.php/configuration/notifications" method="post">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt id="notification-enabled">Activer les notifications web</dt>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="notifications_enabled" options=$options_yes_no selected=$CFG.notifications_enabled}
			</dd>
			<dd>
				<span class="help-block small">Autorise les utilisateurs connectés à recevoir une notification web dès qu'un service est perturbé.</span>
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

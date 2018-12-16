<form action="{$smarty.const.URL}/index.php/configuration/notifications" class="form" method="post">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt id="notification-enabled">Activer les notifications web</dt>
			<dd>
				<p class="help-block small">Autorise les utilisateurs connectés à s'abonner aux notifications web.</p>
			</dd>
			<dd>
				{html_radios aria-labelledby="notification-enabled" name="notifications_enabled" options=$options_yes_no selected=$CFG.notifications_enabled}
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

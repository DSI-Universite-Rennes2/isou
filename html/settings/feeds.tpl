<form action="{$smarty.const.URL}/index.php/configuration/flux" method="post">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt id="notification-enabled">Activer le flux RSS</dt>
			<dd>
				<span class="help-block small">Permet de s'abonner et de suivre les interruptions de service prévues et non prévues par flux RSS.{if empty($CFG.rss_enabled) === false} Adresse du flux : <a href="{$smarty.const.URL}/rss.php">{$smarty.const.URL}/rss.php</a>.{/if}</span>
			</dd>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="rss_enabled" options=$options_yes_no selected=$CFG.rss_enabled}
			</dd>
		</div>
		<div class="form-group">
			<dt id="notification-enabled">Activer le flux iCal</dt>
			<dd>
				<span class="help-block small">Permet de s'abonner et de suivre les interruptions de service prévues au format iCal.{if empty($CFG.ical_enabled) === false} Adresse du flux : <a href="{$smarty.const.URL}/isou.ics">{$smarty.const.URL}/isou.ics</a>.{/if}</span>
			</dd>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="ical_enabled" options=$options_yes_no selected=$CFG.ical_enabled}
			</dd>
		</div>
		<div class="form-group">
			<dt id="notification-enabled">Activer les notifications web</dt>
			<dd>
				<span class="help-block small">Permet, pour les utilisateurs authentifiés, de s'abonner et de suivre les interruptions de service prévues et non prévues par notifications web.</span>
			</dd>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="notifications_enabled" options=$options_yes_no selected=$CFG.notifications_enabled}
			</dd>
		</div>
		<div class="form-group">
			<dt id="notification-enabled">Activer le flux JSON</dt>
			<dd>
				<span class="help-block small">Permet de suivre les interruptions de service prévues et non prévues au format JSON.{if empty($CFG.json_enabled) === false} Adresse du flux : <a href="{$smarty.const.URL}/isou.json">{$smarty.const.URL}/isou.json</a>.{/if}</span>
			</dd>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="json_enabled" options=$options_yes_no selected=$CFG.json_enabled}
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

<form action="{$smarty.const.URL}/index.php/configuration/notifications" method="post">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt id="notification-enabled">Activer les notifications web</dt>
			<dd>
				<span class="help-block small">Autorise les utilisateurs connectés à s'abonner aux notifications web.</span>
			</dd>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="notification-enabled" name="notifications_enabled" options=$options_yes_no selected=$CFG.notifications_enabled}
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="http-proxy">Adresse de votre proxy HTTP</label>
			</dt>
			<dd>
				<span class="help-block small">Exemple: http://localhost:8125</span>
			</dd>

			<dd>
				<input class="form-control" id="http-proxy" name="http_proxy" type="text" value="{$CFG.http_proxy}" />
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="https-proxy">Adresse de votre proxy HTTPS</label>
			</dt>
			<dd>
				<span class="help-block small">Exemple: http://localhost:9124</span>
			</dd>
			<dd>
				<input class="form-control" id="https-proxy" name="https_proxy" type="text" value="{$CFG.https_proxy}" />
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="no-proxy">Domaines ne devant pas utiliser le proxy</label>
			</dt>
			<dd>
				<span class="help-block small">Exemple: .mit.edu, foo.com</span>
			</dd>
			<dd>
				<input class="form-control" id="no-proxy" name="no_proxy" type="text" value="{$no_proxy}" />
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

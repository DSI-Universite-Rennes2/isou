<form method="post" action="{$smarty.const.URL}/index.php/configuration/proxy">

	<div class="alert alert-info"><p>Si les requêtes HTTP de votre serveur passe par un proxy mandataire, vous pouvez renseigner sa configuration sur cette page.</p></div>

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt>
				<label for="http-proxy">Adresse de votre proxy HTTP</label>
			</dt>
			<dd>
				<input class="form-control" id="http-proxy" name="http_proxy" type="text" value="{$CFG.http_proxy}" />
			</dd>
			<dd>
				<span class="help-block small">Exemple: http://localhost:8125</span>
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="https-proxy">Adresse de votre proxy HTTPS</label>
			</dt>
			<dd>
				<input class="form-control" id="https-proxy" name="https_proxy" type="text" value="{$CFG.https_proxy}" />
			</dd>
			<dd>
				<span class="help-block small">Exemple: http://localhost:9124</span>
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="no-proxy">Domaines ne devant pas utiliser le proxy</label>
			</dt>
			<dd>
				<input class="form-control" id="no-proxy" name="no_proxy" type="text" value="{$no_proxy}" />
			</dd>
			<dd>
				<span class="help-block small">Exemple: .mit.edu, foo.com</span>
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration/authentification/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-cas-enable">Activer</dt>
				<dd>
					{html_radios aria-labelledby="plugin-cas-enable" name="plugin_cas_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-host">Hôte</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-host" name="plugin_cas_host" type="text" value="{$plugin->settings->cas_host}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-port">Port</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-port" name="plugin_cas_port" type="text" value="{$plugin->settings->cas_port}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-path">Chemin</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-path" name="plugin_cas_path" type="text" value="{$plugin->settings->cas_path}" />
					<p class="help-block small">Généralement vide. Mettre 'mon-cas' par exemple, si votre serveur CAS est accessible à l'adresse auth.example.com/mon-cas.</p>
				</dd>
			</div>
			<div class="form-group">
				<dt id="plugin-cas-protocol">Protocole</dt>
				<dd>
					{html_options aria-labelledby="plugin-cas-protocol" class="form-control" name="plugin_cas_protocol" options=$options_cas_protocols selected=$plugin->settings->cas_protocol}
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-logout-redirection">Adresse de redirection</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-logout-redirection" name="plugin_cas_logout_redirection" type="url" value="{$plugin->settings->cas_logout_redirection}" />
					<p class="help-block small">Adresse vers laquelle l'utilisateur sera redirigé après déconnexion. Si vide, l'utilisateur restera sur la page de déconnexion du serveur CAS.</p>
				</dd>
			</div>
			<div class="form-group">
				<dt id="plugin-cas-verbose">Activer le mode verbeux</dt>
				<dd>
					{html_radios aria-labelledby="plugin-cas-verbose" name="plugin_cas_verbose" options=$options_yes_no selected=$plugin->settings->cas_verbose}
				</dd>
			</div>
		</dl>
	</fieldset>

  <fieldset>
    <legend>Autorisations via LDAP</legend>

    <dl>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-uri">URI LDAP</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-uri" name="plugin_cas_ldap_uri" type="text" value="{$plugin->settings->cas_ldap_uri}" />
					<div class="help-block small">
						<p>Exemple d'URI LDAP : <code>ldap://ldap.example.com:port</code> ou <code>ldaps://ldap.example.com:port</code>.</p>
						<p>Vous pouvez également fournir plusieurs URI LDAP séparés par un espace comme une chaîne.</p>
					</div>
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-username">Nom utilisateur LDAP</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-username" name="plugin_cas_ldap_username" type="text" value="{$plugin->settings->cas_ldap_username}" />
					<p class="help-block small">Laisser vide pour une authentification anonyme.</p>
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-password">Mot de passe LDAP</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-password" name="plugin_cas_ldap_password" type="password" value="{if empty($plugin->settings->cas_ldap_password) === false}* * * * *{/if}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-dn">DN LDAP</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-dn" name="plugin_cas_ldap_dn" type="text" value="{$plugin->settings->cas_ldap_dn}" />
					<div class="help-block small">
						<p>Exemple de DN LDAP : <code>ou=users,dc=ldap,dc=example,dc=com</code></p>
					</div>
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-filter">Filtre LDAP</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-filter" name="plugin_cas_ldap_filter" type="text" value="{$plugin->settings->cas_ldap_filter}" />
					<div class="help-block small">
						<p>Exemple de filtre LDAP : <code>(&amp;(uid=:phpcas_username)(memberof=cn=isou,ou=groups,dc=ldap,dc=example,dc=com))</code>.</p>
						<p>Si un enregistrement est retourné par le filtre, l'utilisateur aura des droits administrateur sur ISOU.</p>
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-attribute-firstname">Nom de l'attribut LDAP contenant le prénom de l'utilisateur</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-attribute-firstname" name="plugin_cas_ldap_attribute_firstname" type="text" value="{$plugin->settings->cas_ldap_attribute_firstname}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-attribute-lastname">Filtre LDAP</label>
					<label for="plugin-cas-ldap-attribute-firstname">Nom de l'attribut LDAP contenant le nom de l'utilisateur</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-attribute-lastname" name="plugin_cas_ldap_attribute_lastname" type="text" value="{$plugin->settings->cas_ldap_attribute_lastname}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-cas-ldap-attribute-firstname">Nom de l'attribut LDAP contenant l'email de l'utilisateur</label>
				</dt>
				<dd>
					<input class="form-control" id="plugin-cas-ldap-attribute-email" name="plugin_cas_ldap_attribute_email" type="text" value="{$plugin->settings->cas_ldap_attribute_email}" />
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

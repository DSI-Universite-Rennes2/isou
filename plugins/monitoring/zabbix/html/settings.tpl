<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt id="plugin-zabbix-enable">Activer</dt>
				<dd>
					{html_radios class="isou-radios" aria-labelledby="plugin-zabbix-enable" name="plugin_zabbix_enable" options=$options_yes_no selected=$plugin->active}
				</dd>
			</div>

			<div class="form-group">
				<dt>
					<label for="plugin-zabbix-url">URL de Zabbix</label>
					<p class="help-block small">Exemple : <em>https://zabbix.example.com/api_jsonrpc.php</em></p>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_zabbix_url" id="plugin-zabbix-url" value="{$plugin->settings->zabbix_url}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-zabbix-api-key">Jeton d'API Zabbix</label>
				</dt>
				<dd>
					<input class="form-control" type="password" name="plugin_zabbix_api_token" id="plugin-zabbix-api-key" value="{if empty($plugin->settings->zabbix_api_token) === false}* * * * *{/if}" />
				</dd>
			</div>
			<div class="form-group">
				<dt>
					<label for="plugin-zabbix-tags">Tags Zabbix</label>
					<p class="help-block small">Permet de filtrer par tags et de limiter le nombre de services remontés par l'API. Le format attendu est <em>tag1=value1,tag2=value2</em>. Exemple : <em>class=os,target=linux</em></p>
				</dt>
				<dd>
					<input class="form-control" type="text" name="plugin_zabbix_tags" id="plugin-zabbix-tags" value="{$plugin->settings->zabbix_tags}" />
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

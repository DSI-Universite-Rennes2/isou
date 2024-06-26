<form action="{$smarty.const.URL}/index.php/services/zabbix/edit/{$service->id}" class="form" method="post">
	{if empty($service->id) === true}
		<h2>Ajouter un service Zabbix</h2>
	{else}
		<h2>Remplacer le service {$service->name}</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<dt>
			<label for="pattern">Service Zabbix</label>
		</dt>
		<dd class="form-group">
			<input class="form-control" id="pattern" list="suggestions" name="pattern" placeholder="Rechercher un service Zabbix. Les expressions régulières sont acceptées. Exemple : https.*myhostname" value="{$smarty.post.pattern|default:""}" type="search" />
			<datalist id="suggestions">
			{foreach $services as $service}
				<option value="{$service}">
			{/foreach}
			</datalist>
		</dd>
	</dl>

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" name="search" type="submit" value="rechercher" />
		</li>

		<li class="list-inline-item">
			<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/services/zabbix">annuler</a>
		</li>
	</ul>

	{if isset($results[0]) === true}
	<div class="border mb-4 px-4 py-2 rounded">
		<p>{$results|count} services trouvés :</p>
		{if empty($service->id) === true}
    {html_checkboxes id="services" name="services" output=$results separator='<br />' style="margin-right:.5em;" values=$results}
		{else}
    {html_radios id="services" name="services" output=$results separator='<br />' style="margin-right:.5em;" values=$results}
		{/if}

		<ul class="list-inline">
			<li class="list-inline-item">
				<input class="btn btn-primary" name="submit" type="submit" value="{if empty($service->id) === true}ajouter{else}modifier{/if}" />
			</li>

			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/services/zabbix">annuler</a>
			</li>
		</ul>
	</div>
	{/if}
</form>

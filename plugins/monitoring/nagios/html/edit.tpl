<form action="{$smarty.const.URL}/index.php/services/nagios/edit/{$service->id}" class="form-horizontal" method="post">
	{if empty($service->id) === true}
		<h2>Ajouter un service Nagios</h2>
	{else}
		<h2>Remplacer le service {$service->name}</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<dt>
			<label for="service">Service Nagios</label>
		</dt>
		<dd class="form-group">
			<input class="form-control" list="services" id="service" name="service" value="{$service->name}" />
			<datalist id="services">
			{foreach $services as $service}
				<option value="{$service}">
			{/foreach}
			</datalist>
		</dd>
	</dl>

	<ul class="list-inline">
		<li class="list-inline-items">
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li class="list-inline-items">
			<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/services/nagios">annuler</a>
		</li>
	</ul>
</form>

{if empty($plugin->active) === true}
	<p class="alert alert-info">Le plugin {$plugin->name} n'est pas activé dans la <a href="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">page de configuration</a>.</p>
{else}
	{include file="common/messages_session.tpl"}

	<p class="text-end">
		<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/edit/0">Ajouter un service {$plugin->name}</a>
	</p>

	{if $services|count === 0}
		<p class="alert alert-info">Aucun service {$plugin->name} utilisé.</p>
	{else}
	<table class="caption-top table table-condensed" summary="liste des services {$plugin->name}">
		<caption>{$services|count} services {$plugin->name} gérés dans Isou</caption>
		<thead>
		<tr>
			<th class="col-md-5" id="head-name">Nom du service</th>
			<th class="col-md-2" id="head-action">Actions</th>
			<th class="col-md-5" id="head-note">Notes</th>
		</tr>
		</thead>
		<tbody>
		{foreach $services as $service}
		<tr>
			<td headers="head-name">{$service->name}</td>
			<td headers="head-action">
				<ul class="list-inline">
					<li class="list-inline-item"><a class="btn btn-sm btn-primary" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/edit/{$service->id}">modifier</a></li>
					<li class="list-inline-item"><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/delete/{$service->id}">supprimer</a></li>
				</ul>
			</td>
			<td headers="head-note">
				{* <!-- ex: service Zabbix non utilisé dans ISOU, service retiré de Zabbix --> *}
				{if isset($service->notes[0])}
				<ul class="list-unstyled">
				{foreach $service->notes as $note}
					<li class="alert alert-{$note['style']}">{$note['label']}</li>
				{/foreach}
				</ul>
				{/if}
			</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
	{/if}
{/if}

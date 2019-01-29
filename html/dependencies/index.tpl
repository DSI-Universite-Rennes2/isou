{include file="common/messages_session.tpl"}

{if count($categories) === 0}
	<p class="alert alert-info">Aucun service ISOU trouvé (<a href="{$smarty.const.URL}/index.php/services">créer un service ISOU</a>).</p>
{else}
	<h1 class="sr-only">Dépendances</h1>

	{foreach $categories as $i => $category}
	<details open>
		<summary>{$category->name}</summary>
		<table class="table table-condensed table-striped">
		<thead>
			<tr>
				<th class="col-md-5" id="head-names-{$i}">Services</th>
				<th class="col-md-3" id="head-warning-groups-{$i}">Groupes instables</th>
				<th class="col-md-3" id="head-critical-groups-{$i}">Groupes indisponibles</th>
				<th class="col-md-1" id="head-actions-{$i}">Actions</th>
			</tr>
		</thead>
		<tbody>
			{foreach $category->services as $service}
			<tr>
				<td headers="head-names-{$i}">{$service->name}</td>
				<td headers="head-warning-groups-{$i}"{if $service->count_warning_groups === 0} class="danger"{/if}>{$service->count_warning_groups} groupe(s) configuré(s)</td>
				<td headers="head-critical-groups-{$i}"{if $service->count_critical_groups === 0} class="danger"{/if}>{$service->count_critical_groups} groupe(s) configuré(s)</td>
				<td headers="head-actions-{$i}"><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">modifier</a></td>
			</tr>
			{/foreach}
		</tbody>
		</table>
	</details>
	{/foreach}
{/if}

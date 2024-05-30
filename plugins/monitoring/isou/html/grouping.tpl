<ul class="flex-column flex-lg-row nav nav-tabs">
	<li class="nav-item"><a class="nav-link" href="{$smarty.const.URL}/index.php/services/isou">Liste des services</a></li>
	<li class="nav-item"><a class="active nav-link" href="{$smarty.const.URL}/index.php/services/isou/grouping">Liste des groupements de services</a></li>
</ul>

{if isset($services[0]) === false}
<p class="alert alert-info">Aucun groupement généré.</p>
{else}
<table class="caption-top table table-bordered">
	<thead>
		<tr>
			<th>Domaine</th>
			<th>Dépendances</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		{foreach $services as $service}
		<tr>
			<td>{$service->name}</td>
			{if $service->dependencies_content|count === 0}
			<td class="table-danger text-danger">Aucun service.</td>
			{else}
			<td>
					<ul>
					{foreach $service->dependencies_content as $content}
						<li>{$content->name} ({$plugins[$content->idplugin]})</li>
					{/foreach}
					</ul>
			</td>
			{/if}
			<td><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}">Supprimer</a></td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}

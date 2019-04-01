<ul class="nav nav-tabs">
	<li><a href="{$smarty.const.URL}/index.php/services/isou">Liste des services</a></li>
	<li class="active"><a class="btn btn-default" href="{$smarty.const.URL}/index.php/services/isou/grouping">Liste des groupements de services</a></li>
</ul>

{if isset($services[0]) === false}
<p class="alert alert-info">Aucun groupement généré.</p>
{else}
<table class="table table-bordered">
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
			{if count($service->dependencies_content) === 0}
			<td class="danger">Aucun service.</td>
			{else}
			<td>
					<ul>
					{foreach $service->dependencies_content as $content}
						<li>{$content->name} ({$plugins[$content->idplugin]})</li>
					{/foreach}
					</ul>
			</td>
			{/if}
			<td><a class="btn btn-danger" href="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}">Supprimer</a></td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}

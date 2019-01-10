{if empty($plugin->active) === true}
	<p class="alert alert-info">Le plugin {$plugin->name} n'est pas activé dans la <a href="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">page de configuration</a>.</p>
{else}
	{include file="common/messages_session.tpl"}

	<p class="text-right">
		<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/edit/0">Ajouter un service {$plugin->name}</a>
	</p>

	{if count($services) === 0}
		<p class="alert alert-info">Aucun service {$plugin->name} utilisé.</p>
	{else}
	<table class="table table-condensed" summary="liste des services {$plugin->name}">
		<caption>{count($services)} services {$plugin->name} gérés dans Isou</caption>
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
					<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/edit/{$service->id}">modifier</a></li>
					<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/services/{$plugin->codename}/delete/{$service->id}">supprimer</a></li>
				</ul>
			</td>
			<td headers="head-note">
				{* <!-- ex: service SHINKEN non utilisé dans ISOU, service retiré de SHINKEN --> *}
				{if isset($service->notes[0])}
				<ul class="alert alert-warning list-unstyled">
				{foreach $service->notes as $note}
					<li>{$note}</li>
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

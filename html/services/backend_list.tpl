{if $backend->enabled === false}
	<p class="alert alert-info">Le backend {$backend->fullname} n'est pas activé dans la <a href="{$smarty.const.URL}/index.php/configuration/monitoring">page de configuration</a>.</p>
{else}
	{include file="common/messages_session.tpl"}

	<p class="text-right">
		<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/{$backend->url}/edit/0">Ajouter un service {$backend->name}</a>
	</p>

	{if count($services) === 0}
		<p>Aucun service {$backend->name} utilisé</p>
	{else}
	<table class="table" summary="liste des services {$backend->name}">
		<caption>{count($services)} services {$backend->name} gérés dans Isou</caption>
		<thead>
		<tr>
			<th class="col-md-2" id="head-state">État actuel du service</th>
			<th class="col-md-3" id="head-name">Nom informatique du service</th>
			<th class="col-md-1" id="head-action">Actions</th>
			<th class="col-md-5" id="head-note">Note</th>
		</tr>
		</thead>
		<tbody>
		{foreach $services as $service}
		<tr>
			<td headers="head-state">{$STATES[{$service->state}]->get_flag_html_renderer()}</td>
			<td headers="head-name">{$service->name}</td>
			<td headers="head-action">
				<ul class="list-inline">
					<li><a href="{$smarty.const.URL}/index.php/services/{$backend->url}/edit/{$service->id}"><img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" /></a></li>
					<li><a href="{$smarty.const.URL}/index.php/services/{$backend->url}/delete/{$service->id}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a></li>
				</ul>
			</td>
			<td headers="head-note">
				{* <!-- ex: service NAGIOS non utilisé dans ISOU, service retiré de NAGIOS --> *}
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

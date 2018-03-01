{if count($categories) === 0}
<p class="alert alert-danger">Vous n'avez pas encore défini de catégorie. Avant d'ajouter un service Isou, vous devez créer une catégorie.</p>
<p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/0">Ajouter une catégorie</a></p>
{else}
<p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/isou/edit/0">Ajouter un service ISOU</a></p>

{include file="common/messages_session.tpl"}

{foreach $categories as $i => $category}

<h2>{$category->name}</h2>

{if !isset($category->services[0])}
<p class="alert alert-info">Cette catégorie ne contient aucun service Isou</p>
{else}
<table class="table table-condensed" summary="liste des services ISOU de la catégorie {$category->name}">
{* <!-- <caption class="services-caption"></caption> --> *}
<thead>
	<tr>
		<th class="col-md-2" id="head-state-{$i}">État service du service</th>
		<th class="col-md-4" id="head-name-{$i}">Nom du service pour les usagers</th>
		<th class="col-md-2" id="head-action-{$i}">Actions</th>
		<th class="col-md-4" id="head-note-{$i}">Notes</th>
	</tr>
</thead>
<tbody>
	{foreach $category->services as $service}
	<tr{if !empty($service->css)} class="{$service->css}"{/if}>
		<td headers="head-state-{$i}">{$STATES[{$service->state}]->get_flag_html_renderer()}</td>
		<td id="service-{$service->id}" headers="head-name-{$i}">
			{$service->name}
			{if $service->url !== NULL}
				<br /><span>URL :</span> {$service->url}
			{/if}
		</td>
		<td headers="head-action-{$i}">
		<ul class="list-inline">
			<li><a href="{$smarty.const.URL}/index.php/services/isou/inspect/{$service->id}" title="inspecter"><img src="{$smarty.const.URL}/styles/{$CFG.theme}/images/inspect.gif" alt="inspecter" width="16px" height="16px" /></a></li>
			<li><a href="{$smarty.const.URL}/index.php/services/isou/edit/{$service->id}" title="modifier"><img src="{$smarty.const.URL}/styles/{$CFG.theme}/images/edit.gif" alt="modifier" width="16px" height="16px" /></a></li>
			<li><a href="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}" title="supprimer"><img src="{$smarty.const.URL}/styles/{$CFG.theme}/images/delete.gif" alt="supprimer" width="16px" height="16px" /></a></li>
		</ul>
		</td>
		<td headers="head-note-{$i}">
			{* <!-- ex: service final ISOU sans dépendance, service final dont l'état est bloqué --> *}
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
{/foreach}
{/if}

{if $isou->settings->grouping === true}
<ul class="nav nav-tabs">
	<li class="active"><a href="{$smarty.const.URL}/index.php/services/isou">Liste des services</a></li>
	<li><a class="btn btn-default" href="{$smarty.const.URL}/index.php/services/isou/grouping">Liste des groupements de services</a></li>
</ul>
{/if}

<p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/isou/edit/0">Ajouter un service ISOU</a></p>

{include file="common/messages_session.tpl"}

{foreach $categories as $i => $category}
	{if isset($category->services[0]) === false}
		{continue}
	{/if}

	<details open>
		<summary>{$category->name}</summary>
		<table class="table table-condensed table-striped" summary="liste des services ISOU de la catégorie {$category->name}">
		{* <!-- <caption class="services-caption"></caption> --> *}
		<thead>
			<tr>
				<th class="col-md-5" id="head-name-{$i}">Nom du service</th>
				<th class="col-md-2" id="head-action-{$i}">Actions</th>
				<th class="col-md-5" id="head-note-{$i}">Notes</th>
			</tr>
		</thead>
		<tbody>
			{foreach $category->services as $service}
			<tr{if !empty($service->css)} class="{$service->css}"{/if}>
				<td id="service-{$service->id}" headers="head-name-{$i}">
					{$service->name}
					{if $service->url !== null}
						<br /><span>URL :</span> {$service->url}
					{/if}
				</td>
				<td headers="head-action-{$i}">
				<ul class="list-inline">
					<!-- <li><a class="btn btn-xs btn-default" href="{$smarty.const.URL}/index.php/services/isou/inspect/{$service->id}">inspecter</a></li> -->
					<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/services/isou/edit/{$service->id}">modifier</a></li>
					<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}">supprimer</a></li>
				</ul>
				</td>
				<td headers="head-note-{$i}">
					{* <!-- ex: service final ISOU sans dépendance, service final dont l'état est bloqué --> *}
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
	</details>
{/foreach}

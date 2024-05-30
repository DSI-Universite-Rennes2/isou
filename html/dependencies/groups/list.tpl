<ul class="breadcrumb ps-4 py-2 rounded">
	<li class="breadcrumb-item"><a href="{$smarty.const.URL}/index.php/dependances">dépendances</a></li>
	<li class="active breadcrumb-item">{$service->name}</li>
</ul>

<ul class="list-inline text-end">
	<li class="list-inline-item"><a class="btn btn-info" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/simulate">Simuler une panne</a></li>
	<li class="list-inline-item"><a class="btn btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/0">Créer un groupe de dépendances</a></li>
</ul>

{include file="common/messages_session.tpl"}

<div class="row">
{if $groups|count === 0}
	<p class="alert alert-info text-center">Aucun groupe de dépendances défini.</p>
{else}
	<div class="row">
	{foreach $groups as $state => $grps}
	<div class="col-md-6">
		<h2 class="isou-dependencies-group-h1 alert alert-{if (string) $state === UniversiteRennes2\Isou\State::WARNING}warning{else}danger{/if} text-center">
			<span aria-hidden="true">{$STATES[$state]}</span> Groupes {UniversiteRennes2\Isou\State::$STATES[$state]|lower}s
		</h2>
		<ul class="list-unstyled">
		{foreach $grps as $group}
		<li class="border mb-4 px-4 py-2 rounded">
			<h3 class="isou-dependencies-group-h3">{$STATES[$state]} {$group->name}</h2>
			{if $group->redundant === "0"}
			<p class="small isou-non-redundant-groups">Groupe de services non-redondés<br />Une seule anomalie dans ce groupe suffit à modifier l'état du service.</p>
			{else}
			<p class="small isou-redundant-groups">Groupe de services redondés<br />Toutes les dépendances de ce groupe doivent être en anomalie pour modifier l'état du service.</p>
			{/if}

			<h4 class="isou-dependencies-group-h4">Contenu du groupe</h4>
			<ul class="list-inline text-end">
				<li class="list-inline-item"><a class="btn btn-sm btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/edit/0">ajouter du contenu</a></li>
			</ul>

			<div class="isou-dependencies-content-div">
				{if $group->contents|count === 0}
				<p class="alert alert-danger">Groupe vide.</p>
				{else}
				<ul class="alert list-unstyled isou-dependencies-content-ul">
				{foreach $group->contents as $content}
					<li class="isou-dependencies-content-ul-li clearfix">
					{$STATES[$content->servicestate]} {$content->name} <span class="small">(plugin {$content->pluginname})</span>
					<ul class="float-end list-inline">
						<li class="list-inline-item"><a class="btn btn-sm btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/edit/{$content->id}">modifier</a></li>
						<li class="list-inline-item"><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/delete/{$content->id}">supprimer</a></li>
					</ul>

					</li>
				{/foreach}
				</ul>
				{/if}
			</div>

			<h4 class="isou-dependencies-group-h4">Message affiché en cas d'erreur</h4>
			{if empty($group->message) === true}
				<p class="alert alert-warning">Aucun message défini.</p>
			{else}
				<div class="alert alert-info isou-dependencies-group-message">{$group->message}</div>
			{/if}

			<h4 class="isou-dependencies-group-h4">Actions sur le groupe</h4>
			<ul class="list-inline">
				<li class="list-inline-item"><a class="btn btn-sm btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/{$group->id}">modifier</a></li>
				<li class="list-inline-item"><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/delete/{$group->id}">supprimer</a></li>
				<li class="list-inline-item float-end"><a class="btn btn-sm btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/duplicate/{$group->id}">dupliquer le groupe</a></li>
			</ul>
		</li>
		{/foreach}
		</ul>
	</div>
	{/foreach}
	</div>
{/if}
</div>

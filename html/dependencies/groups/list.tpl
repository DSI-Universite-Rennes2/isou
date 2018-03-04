<main role="main">
<article id="content">

<ul class="list-inline text-right">
	<li><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/0">Créer un groupe de dépendances</a></li>
	{if count($groups) !== 0}
	<!-- <li><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/content/0">Ajouter un service à un groupe</a></li> -->
	{/if}
</ul>

{include file="common/messages_session.tpl"}

{if count($groups) === 0}
	<p class="alert alert-info text-center">Aucun groupe de dépendances défini.</p>
{else}
	<div class="clearfix">
	{foreach $groups as $state => $grps}
	<div class="pull-left col-md-6">
	<ul class="list-unstyled">
	{foreach $grps as $group}
	<li class="well">
		<h2 class="isou-dependencies-group-h2">{$STATES[$state]} {$group->name}</h2>
		{if $group->redundant === "0"}
		<p class="small isou-non-redundant-groups">groupe de services non-redondés</p>
		{else}
		<p class="small isou-redundant-groups">groupe de services redondés</p>
		{/if}

		<h3 class="isou-dependencies-group-h3">Contenu du groupe</h3>
		{if count($group->contents) === 0}
		<p class="alert alert-info">Groupe vide.</p>
		{else}
		<ul class="alert list-unstyled isou-dependencies-content-ul">
		{foreach $group->contents as $content}
			<li>
			{$STATES[$content->servicestate]} {$content->name}
			<ul class="pull-right list-inline">
				<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$group->id}/content/{$content->idservice}">modifier</a></li>
				<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/delete/group/{$group->id}/content/{$content->idservice}">supprimer</a></li>
			</ul>
			</li>
		{/foreach}
		</ul>
		{/if}

		<h3 class="isou-dependencies-group-h3">Message affiché en cas d'erreur</h3>
		{if $group->message === ''}
			<p class="alert alert-info">Aucun message défini.</p>
		{else}
			<div class="alert isou-dependencies-group-message">{$group->message}</div>
		{/if}

		<h3 class="isou-dependencies-group-h3">Actions sur le groupe</h3>
		<ul class="list-inline">
			<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/add/group/{$group->id}/content/0">ajouter du contenu</a></li>
			<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/duplicate/group/{$group->id}">dupliquer le groupe</a></li>
			<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$group->id}">modifier</a></li>
			<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/delete/group/{$group->id}">supprimer</a></li>
		</ul>
	</li>
	{/foreach}
	</ul>
	</div>
	{/foreach}
	</div>
{/if}

</article>
</main>

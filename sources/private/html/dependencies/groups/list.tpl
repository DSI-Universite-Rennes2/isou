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
	<h1 class="isou-dependencies-h1">{$STATES[$state]->get_flag_html_renderer()} {$STATES[$state]->alternate_text}</h1>
	<ul class="list-unstyled">
	{foreach $grps as $group}
	<li class="well">
		<div class="clearfix isou-dependencies-group-header-div"><!-- {if $group->redundant === "1"}bg-success isou-redounded{else}bg-danger isou-not-redounded{/if}"> -->
			<div class="pull-left">
				<h2 class="isou-dependencies-group-h2">{$group->name}</h2>
				<p class="small">groupe de services {if $group->redundant === "0"}non-{/if}redondé</p>
			</div>

			<ul class="pull-right list-inline">
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/add/group/{$group->id}/content/0"><img src="{$smarty.const.URL}/images/add.png" alt="ajouter du contenu" /></a></li>
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$group->id}"><img src="{$smarty.const.URL}/images/edit.png" alt="éditer" /></a></li>
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/delete/group/{$group->id}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" /></a></li>
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/duplicate/group/{$group->id}"><img src="{$smarty.const.URL}/images/duplicate.png" alt="dupliquer" /></a></li>
			</p>
		</div>

		{if count($group->contents) === 0}
		<p class="alert alert-info">Groupe vide</p>
		{else}
		<ul class="list-unstyled isou-dependencies-content-ul">
		{foreach $group->contents as $content}
			<li>
			{$STATES[$content->servicestate]->get_flag_html_renderer()} {$content->name}
			<ul class="pull-right list-inline">
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$group->id}/content/{$content->idservice}"><img src="{$smarty.const.URL}/images/edit.png" alt="éditer" /></a></li>
				<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/delete/group/{$group->id}/content/{$content->idservice}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" /></a></li>
			</ul>
			</li>
		{/foreach}
		</ul>
		{/if}

		<!--
		<p class="text-right">
			<a class="btn btn-primary" title="ajouter un service au groupe '{$group->name}'" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$group->id}/content/0">ajouter un service</a>
		</p>
		-->

		{if $group->message !== ''}
		<dl class="isou-dependencies-message-dl">
			<dt>Message associé en cas d'erreur</dt>
			<dd>{$group->message}</dd>
		</dl>
		{/if}
	</li>
	{/foreach}
	</ul>
	</div>
	{/foreach}
	</div>
{/if}

<ul class="list-inline well text-center">
	<li><img src="{$smarty.const.URL}/images/flag_orange.gif" alt="Drapeau orange" aria-describedby="orange_flag"> <span id="orange_flag">Etat 1 est équivalent à état perturbé</span></li>
	<li><img src="{$smarty.const.URL}/images/flag_red.gif" alt="Drapeau rouge" aria-describedby="red_flag"> <span id="red_flag">Etat 2 est équivalent à état critique</span></li>
</ul>

</article>
</main>

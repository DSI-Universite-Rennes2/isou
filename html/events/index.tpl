<main role="main">
<article id="content">

<h1 class="sr-only">Évènements</h1>

<ul class="nav nav-tabs">
{foreach $submenu as $menu}
	{if $menu->selected === true}
	<li class="active"><a href="{$menu->url}">{$menu->label}</a></li>
	{else}
	<li><a href="{$menu->url}">{$menu->label}</a></li>
	{/if}
{/foreach}
</ul>

{if isset($event) === true}
	{* Formulaire d'édition et de suppression d'évènements. *}
	{include file=$subtemplate}
{else}
	{* Liste des évènements. *}
	<p class="text-right">
		<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/0">Ajouter un évènement</a>
	</p>

	{include file="common/messages_session.tpl"}

	{if isset($events[0]) === false}
		<p class="alert alert-info">Aucune interruption enregistrée.</p>
	{else}
		{include file=$subtemplate}
	{/if}
{/if}

</article>
</main>

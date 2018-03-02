<main role="main">
<article id="content">

<h1 class="sr-only">Évènements</h1>

<ul class="nav nav-tabs">
{foreach $submenu as $menu}
	<li{if $menu->selected === TRUE} class="active"{/if}><a href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>

<p class="text-right">
	<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/evenements/edit/0">Ajouter un évènement</a>
</p>

{include file="common/messages_session.tpl"}

{if !isset($events[0])}

<p class="alert alert-info">Aucune interruption enregistrée.</p>

{else}

{include file=$subtemplate}

{/if}

</article>
</main>

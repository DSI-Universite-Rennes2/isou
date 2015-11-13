<main role="main">
<article id="content">

{include file="common/messages_session.tpl"}

{if count($services) === 0}
	<p class="alert alert-info">Aucun service ISOU trouvé (<a href="{$smarty.const.URL}/index.php/services">créer un service ISOU</a>).</p>
{else}
	<h1 class="sr-only">Dépendances</h1>

	<ul>
	{foreach $services as $service}
		<li><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">{$service->name}</a></li>
	{/foreach}
	</ul>
{/if}

</article>
</main>


<main role="main">
<article id="content">

<h1 class="sr-only">Flux RSS</h1>

{if count($categories) === 0}
	<p class="alert alert-info">Aucun service disponible pour le moment.</p>
{else}
	<p class="well">Sélectionnez les services qui vous intéressent afin de ne recevoir, par flux RSS, que les informations à propos de ces services ou cliquez directement sur le bouton "Générer le flux RSS" en bas de page pour surveiller tous les services.</p>

	<form method="post" action="{$smarty.const.URL}/index.php/rss/config#rss-url">

	<ul class="list-unstyled">
	{foreach $categories as $category}
	<li>
		<h2 class="isou-rss-categories">{$category->name}</h2>
		<ul>
		{foreach $category->services as $service}
		<li>
			<input type="checkbox" name="keys[{$service->id}]" id="key-{$service->id}" value="{$service->id}"{if isset($smarty.post.keys[$service->id])} checked="1"{/if} />
			<label for="key-{$service->id}">{$service->name}</label>
		</li>
		{/foreach}
		</ul>
	</li>
	{/foreach}
	</ul>

	<p><input class="btn btn-primary" type="submit" name="generer" value="Générer le flux RSS" id="generer" /></p>

	</form>

	{if $rss_url !== NULL}
	<p id="rss-url">Vous pouvez consulter les actualités des services sélectionnés précédemment en utilisant ce lien RSS : <a href="{$rss_url}" title="lien vers le flux RSS">{$rss_url}</a>.</p>
	{/if}
{/if}

</article>
</main>

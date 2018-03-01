<main role="main">
<article id="content">

<h1 class="sr-only">Catégories</h1>

<p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/0">Ajouter une catégorie</a></p>

{include file="common/messages_session.tpl"}

{if $count_categories === 0}
	<p>Aucune catégorie.</p>
{else}

<table class="table table-bordered table-condensed" summary="liste des categories">
<caption class="text-center">Liste des catégories</caption>
<thead>
	<tr class="categories-tr">
		<th class="col-md-1" id="head-order">Positionnement</th>
		<th class="col-md-5" id="head-name">Nom de la catégorie</th>
		<th class="col-md-3" id="head-action">Actions</th>
		<th class="col-md-3" id="head-note">Nombre de services</th>
	</tr>
</thead>
<tbody>
{foreach $categories as $category}
    <tr>
		<td headers="head-order">
			<span class="upndown">
			{if $category->position > 1}
				<a href="{$smarty.const.URL}/index.php/categories/up/{$category->id}"><img src="{$smarty.const.URL}/images/arrow_up.gif" alt="monter la catégorie" width="24px" height="24px" /></a>
			{/if}

			{if $category->position < $count_categories}
				<a href="{$smarty.const.URL}/index.php/categories/down/{$category->id}"><img src="{$smarty.const.URL}/images/arrow_down.gif" alt="descendre la catégorie" width="24px" height="24px" /></a>
			{/if}

			{if $count_categories === 1}-{/if}
			</span>
		</td>
		<td headers="head-name">{$category->name}</td>
		<td headers="head-action">
		<ul class="list-inline">
			<li><a href="{$smarty.const.URL}/index.php/categories/edit/{$category->id}"><img src="{$smarty.const.URL}/images/edit.png" alt="éditer" width="16px" height="16px" /></a></li>
			<li><a href="{$smarty.const.URL}/index.php/categories/delete/{$category->id}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a></li>
		</ul>
		</td>
		<td{if $category->count_services === 0} class="alert alert-danger"{/if} headers="head-note">{$category->count_services} service{if $category->count_services > 1}s{/if}</td>
{/foreach}
</tbody>
</table>
{/if}

</article>
</main>

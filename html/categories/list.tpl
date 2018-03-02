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
		<th class="col-md-2" id="head-order">Positionnement</th>
		<th class="col-md-4" id="head-name">Nom de la catégorie</th>
		<th class="col-md-3" id="head-action">Actions</th>
		<th class="col-md-3" id="head-note">Nombre de services</th>
	</tr>
</thead>
<tbody>
{foreach $categories as $category}
    <tr>
		<td headers="head-order">
			{if $category->position > 1}
				<p><a class="btn btn-xs btn-success" href="{$smarty.const.URL}/index.php/categories/up/{$category->id}">monter la catégorie</a></p>
			{/if}

			{if $category->position < $count_categories}
				<p><a class="btn btn-xs btn-success" href="{$smarty.const.URL}/index.php/categories/down/{$category->id}">descendre la catégorie</a></p>
			{/if}
		</td>
		<td headers="head-name">{$category->name}</td>
		<td headers="head-action">
		<ul class="list-inline">
			<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/{$category->id}">modifier</a></li>
			<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/categories/delete/{$category->id}">supprimer</a></li>
		</ul>
		</td>
		<td{if $category->count_services === 0} class="alert alert-danger"{/if} headers="head-note">{$category->count_services} service{if $category->count_services > 1}s{/if}</td>
{/foreach}
</tbody>
</table>
{/if}

</article>
</main>

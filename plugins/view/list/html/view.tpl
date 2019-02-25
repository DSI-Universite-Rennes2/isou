{if count($categories) === 0}
	<p class="alert alert-info">Aucun service disponible pour le moment.</p>
{else}
	<div class="sr-only">
		<h1>Liste des catégories</h1>
		<ul class="list-inline">
		{foreach $categories as $category}
			<li><a href="#{$category->name}">{$category->name}</a></li>
		{/foreach}
		</ul>
	</div>

	{foreach $categories as $category}
		<details class="isou-list-events-details">
			<summary>{$STATES[$category->state]} {$category->name}

				{if isset($category->unstable_services[0]) === true}
					{* Affiche seulement les services perturbés. *}
					<div class="isou-list-hideable-table-div">
						{include file="subview.tpl" services=$category->unstable_services}
					</div>
				{/if}
			</summary>

			{include file="subview.tpl" services=$category->services}
		</details>
		{/foreach}
{/if}

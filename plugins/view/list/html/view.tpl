{if count($categories) === 0}
	<p class="alert alert-info">Aucun service disponible pour le moment.</p>
{else}
	<div class="visually-hidden">
		<h1>Liste des catégories</h1>
		<ul class="list-inline">
		{foreach $categories as $category}
			<li class="list-inline-item"><a href="#{$category->name}">{$category->name}</a></li>
		{/foreach}
		</ul>
	</div>

	{foreach $categories as $category}
		<details class="isou-list-events-details">
			<summary><span>{$STATES[$category->state]} {$category->name}</span>
				<ul class="list-inline text-end">
					{if $category->scheduled_events_count > 0}
						{if $category->scheduled_events_count === 1}
						<li class="list-inline-item"><span class="isou-list-event-scheduled">{$category->scheduled_events_count} évènement prévu</span></li>
						{else}
						<li class="list-inline-item"><span class="isou-list-event-scheduled">{$category->scheduled_events_count} évènements prévus</span></li>
						{/if}
					{/if}
					{if $category->past_events_count > 0}
						{if $category->past_events_count === 1}
						<li class="list-inline-item"><span class="isou-list-event-unscheduled">{$category->past_events_count} évènement passé</span></li>
						{else}
						<li class="list-inline-item"><span class="isou-list-event-unscheduled">{$category->past_events_count} évènements passés</span></li>
						{/if}
					{/if}
				</ul>
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

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
		<details>
			<summary>{$STATES[$category->state]} {$category->name}{if $category->count_events !== 0} <span class="small">({$category->count_events} perturbation{if $category->count_events !== 1}s{/if} en cours)</span>{/if}</summary>

			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th class="col-md-5" id="column-1">Services</th>
						<th class="col-md-1 text-center" id="column-2">États</th>
						<th class="col-md-3 text-center" id="column-3">Interruptions passées ou à venir</th>
						<th class="col-md-3 text-center" id="column-4">Interruptions régulières</th>
					</tr>
				</thead>
				<tbody>

				{foreach $category->services as $service}
				<tr>
					{* Services column. *}
					{if $service->url === null}
						<td headers="column-1" class="text-left">{$service->name}</td>
					{else}
						<td headers="column-1" class="text-left"><a href="{$service->url}" title="Accéder à la page du service {$service->name|escape:'html':'UTF-8'}">{$service->name}</a></td>
					{/if}

					{* States column. *}
					<td headers="column-2">{$STATES[$service->state]}</td>

					{* Events column. *}
					<td headers="column-3">
						{if isset($service->events[0]) === true}
							<ul class="list-unstyled">
							{foreach $service->events as $event}
								{if $event->type === UniversiteRennes2\Isou\Event::TYPE_SCHEDULED}
									<li class="isou-list-event-scheduled">{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
								{else if $event->type === UniversiteRennes2\Isou\Event::TYPE_UNSCHEDULED}
									<li class="isou-list-event-unscheduled">{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
								{/if}
							{/foreach}
							</ul>

							{if isset($service->more) === true}
								{* Liste des interruptions en plus *}
								<details class="isou-list-events-details">
									<summary class="isou-list-hideable-summary text-right small">{count($service->more)} interruption{if count($service->more) !== 1}s{/if} en plus</summary>
									<ul class="list-unstyled">
									{foreach $service->more as $event}
										{if $event->type === UniversiteRennes2\Isou\Event::TYPE_SCHEDULED}
										<li class="isou-list-event-scheduled">{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
										{else if $event->type === UniversiteRennes2\Isou\Event::TYPE_UNSCHEDULED}
										<li class="isou-list-event-unscheduled">{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
										{/if}
									{/foreach}
									</ul>
								</details>
							{/if}
						{/if}
					</td>

					{* Regular events column. *}
					<td headers="column-4">
					{if isset($service->regular_events[0])}
						<ul class="list-unstyled">
						{foreach $service->regular_events as $event}
							<li class="isou-list-event-regular">{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
						{/foreach}
						</ul>
					{/if}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
		</details>
		{/foreach}
{/if}

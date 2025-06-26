<table class="caption-top table table-bordered table-condensed">
	<thead>
		<tr>
			<th class="col-md-5" id="column-1">Service</th>
			<th class="col-md-1 text-center" id="column-2">État</th>
			<th class="col-md-3 text-center" id="column-3">Interruptions passées ou à venir</th>
			<th class="col-md-3 text-center" id="column-4">Interruptions régulières</th>
		</tr>
	</thead>
	<tbody>
		{foreach $services as $service}
		<tr>
			{* Services column. *}
			{if $service->url === null}
				<td headers="column-1" class="text-start">{$service->name}</td>
			{else}
				<td headers="column-1" class="text-start"><a href="{$service->url}" title="Accéder à la page du service {$service->name|escape:'html':'UTF-8'}">{$service->name}</a></td>
			{/if}

			{* States column. *}
			<td headers="column-2" class="text-center">{$STATES[$service->state]}</td>

			{* Events column. *}
			<td headers="column-3">
				{if isset($service->events[0]) === true}
					<ul class="list-unstyled">
					{foreach $service->events as $event}
						{if $event->type === Event::TYPE_SCHEDULED}
							<li><i aria-hidden="true" class="bi bi-calendar4 me-2"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
						{else if $event->type === Event::TYPE_UNSCHEDULED}
							<li><i aria-hidden="true" class="bi bi-patch-exclamation-fill me-2 text-danger"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
						{else if $event->type === Event::TYPE_CLOSED}
							<li><i aria-hidden="true" class="bi bi-stopwatch me-2"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
						{/if}
					{/foreach}
					</ul>

					{if isset($service->more) === true}
						{* Liste des interruptions en plus *}
						<details class="isou-list-events-details isou-list-events-more-details">
							<summary class="isou-list-hideable-summary text-end small">{$service->more|count} interruption{if $service->more|count !== 1}s{/if} en plus</summary>
							<ul class="list-unstyled">
							{foreach $service->more as $event}
								{if $event->type === Event::TYPE_SCHEDULED}
								<li><i aria-hidden="true" class="bi bi-calendar4 me-2"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
								{else if $event->type === Event::TYPE_UNSCHEDULED}
								<li><i aria-hidden="true" class="bi bi-patch-exclamation-fill me-2 text-danger"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
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
					<li><i aria-hidden="true" class="bi bi-clock-history me-2"></i>{$event}{if empty($event->description) === false}<div class="isou-list-event-description">{$event->description|nl2br}</div>{/if}</li>
				{/foreach}
				</ul>
			{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

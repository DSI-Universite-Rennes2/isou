<h1 class="visually-hidden">Calendrier</h1>

<p class="alert alert-info text-center">Liste des opérations de maintenance prévues.</p>

<table id="calendar" class="caption-top table table-bordered" summary="Calendrier répertoriant toutes les intervertions prévues">
	{* Titre du calendrier. *}
	<caption class="text-center">
		<div class="visually-hidden">Calendrier des interventions</div>
		<ul class="list-unstyled row">
			{if $smarty.get.page > 1}
			<li class="col-md-6 text-start">
				<a class="btn" href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page - 1}"><i aria-hidden="true" class="bi bi-arrow-left-square-fill" title="Page précédente"></i><span class="visually-hidden">Page précédente</span></a>
			</li>
			{/if}
			{if $smarty.get.page < 5}
			<li class="col-md-6 ms-auto text-end">
				<a class="btn" href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page + 1}"><i aria-hidden="true" class="bi bi-arrow-right-square-fill" title="Page suivante"></i><span class="visually-hidden">Page suivante</span></a>
			</li>
			{/if}
		</ul>
	</caption>

	{* Entêtes du calendrier. *}
	<thead>
		<tr>
			<th class="col-md-1">Lundi</th>
			<th class="col-md-1">Mardi</th>
			<th class="col-md-1">Mercredi</th>
			<th class="col-md-1">Jeudi</th>
			<th class="col-md-1">Vendredi</th>
			<th class="col-md-1">Samedi</th>
			<th class="col-md-1">Dimanche</th>
		</tr>
	</thead>

	{* Corps du calendrier. *}
	<tbody>
	{foreach $calendar as $week}
		<tr>
		{foreach $week as $day}
			<td class="col">
			<div class="card h-100">
					<div class="{if $day->datetime < $now}bg-body-secondary {else if $day->datetime === $now}bg-info-subtle {/if}card-header">
						<span class="small" id="date-{$day->datetime|date_format:'%d-%B-%Y'}">{$day->datetime|date_format:$day->strftime}</span>
					</div>
					<div class="card-body">
						{if isset($day->services[0]) === true}
						<ul>
							{foreach $day->services as $service}
							<li>
								{if $service->event->enddate === null}
								<div>{$service->name} - à partir de {$service->event->startdate->format('H\hi')}.</div>
								{else}
								<div>{$service->name} - de {$service->event->startdate->format('H\hi')} à {$service->event->enddate->format('H\hi')}.</div>
								{/if}
								{if empty($service->event->description) === false}
								<div class="isou-calendar-description">{$service->event->description}</div>
								{/if}
							</li>
							{/foreach}
						</ul>
						{else}
							{if $day->datetime === $now}
								<p>Aucune intervention prévue</p>
							{/if}
						{/if}
					</div>
					</div>
			</td>
		{/foreach}
		</tr>
	{/foreach}
	</tbody>
</table>

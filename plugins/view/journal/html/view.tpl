<table id="list" class="caption-top table table-condensed" summary="Journal des perturbations et interruptions de services">
	<caption class="text-center">Journal des services monitorés</caption>
	<thead>
		<tr class="header">
			<th class="text-center" id="lth2">État</th>
			<th id="lth1">Service</th>
			<th id="lth4">Début</th>
			<th id="lth5">Fin</th>
			<th id="lth3">Raison</th>
		</tr>
	</thead>
	{foreach $days as $day}
	<tbody>
		<tr class="header">
			{if isset($day->today) === true}
			<th colspan="5" class="thdate active">Aujourd'hui</span></th>
			{else if isset($day->yesterday) === true}
			<th colspan="5" class="thdate active">Hier</th>
			{else}
			<th colspan="5" class="thdate active">{$day->date|date_format:"%A %e %B"}</th>
			{/if}
		</tr>
		{if isset($day->events[0]) === false}
		<tr><td colspan="5" class="table-info text-info">Aucun évènement</td></tr>
		{else}
		{foreach $day->events as $event}
			<tr>
				<td class="text-center" headers="lth2">{$STATES[$event->state]}</td>
				<td headers="lth1">{$event->service}</td>
				<td headers="lth4">{$event->startdate|date_format:"%a %e %b %Hh%M"}</td>
				{if $event->enddate === null}
				<td class="table-danger text-danger" headers="lth5">en cours</td>
				{else}
				<td headers="lth5">{$event->enddate|date_format:"%a %e %b %Hh%M"}</td>
				{/if}
				<td headers="lth3">{$event->description|nl2br}</td>
			</tr>
		{/foreach}
		{/if}
	</tbody>
	{/foreach}
</table>

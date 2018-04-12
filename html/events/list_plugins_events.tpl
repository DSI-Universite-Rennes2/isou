<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th id="lth1">État</th>
			<th id="lth2">Service</th>
			<th id="lth3">Date de début</th>
			<th id="lth4">Date de fin</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td headers="lth1">{$STATES[$event->state]}</td>
			<td headers="lth2">{$event->service_name}</td>
			<td headers="lth3">{$event->startdate|date_format:"%a %d %B %Y %H:%M"}</td>
			{if $event->enddate === null}
			<td headers="lth4" class="danger">en cours</td>
			{else}
			<td headers="lth4">{$event->enddate|date_format:"%a %d %B %Y %H:%M"}</td>
			{/if}
		{/foreach}
	</tbody>
</table>

<table class="caption-top table table-bordered table-condensed">
	<caption class="text-center">Liste des interruptions des services non isou</caption>
	<thead>
		<tr>
			<th id="lth1">État</th>
			<th id="lth2">Source</th>
			<th id="lth3">Service</th>
			<th id="lth4">Date de début</th>
			<th id="lth5">Date de fin</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td headers="lth1">{$STATES[$event->state]}</td>
			<td headers="lth2">{$plugins[$event->idplugin]->name}</td>
			<td headers="lth3">{$event->service_name}</td>
			<td headers="lth4">{$event->startdate|date_format:"%a %d %B %Y %H:%M"}</td>
			{if $event->enddate === null}
			<td headers="lth5" class="table-danger text-danger">en cours</td>
			{else}
			<td headers="lth5">{$event->enddate|date_format:"%a %d %B %Y %H:%M"}</td>
			{/if}
		{/foreach}
	</tbody>
</table>

<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th id="lth1">État</th>
			<th id="lth2">Source</th>
			<th id="lth3">Service</th>
			<th id="lth4">Date de début</th>
			<th id="lth5">Date de fin</th>
			<th id="lth6">Description</th>
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
			<td class="danger" headers="lth5">en cours</td>
			{else}
			<td headers="lth5">{$event->enddate|date_format:"%a %d %B %Y %H:%M"}</td>
			{/if}
			<td headers="lth6">{if empty($event->description) === false}{$event->description|nl2br}{/if}</td>
		{/foreach}
	</tbody>
</table>

<ul class="list-inline">
	<li><a class="btn btn-default" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">retour</a></li>
</ul>

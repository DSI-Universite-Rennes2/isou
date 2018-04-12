<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th>État</th>
			<th>Service</th>
			<th>Date de début</th>
			<th>Date de fin</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td>{$STATES[$event->state]}</td>
			<td>{$event->service_name}</td>
			<td>{$event->startdate|date_format:"%a %d %B %Y %H:%M"}</td>
			{if $event->enddate === null}
			<td class="danger">en cours</td>
			{else}
			<td>{$event->enddate|date_format:"%a %d %B %Y %H:%M"}</td>
			{/if}
			<td>{if empty($event->description) === false}{$event->description|nl2br}{/if}</td>
		{/foreach}
	</tbody>
</table>

<ul class="list-inline">
	<li><a class="btn btn-default" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">retour</a></li>
</ul>

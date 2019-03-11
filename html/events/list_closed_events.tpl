<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th id="lth1">État</th>
			<th id="lth2">Service</th>
			<th id="lth3">Date de début</th>
			<th id="lth4">Date de fin</th>
			<th id="lth5">Description</th>
			<th id="lth6" class="col-sm-3">Actions</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td headers="lth1">{$STATES[$event->state]}</td>
			<td headers="lth2">{$event->service_name}</td>
			<td headers="lth3">{$event->startdate|date_format:"%a %d %B %Y %H:%M"}</td>
			{if $event->enddate === null}
			<td headers="lth4" class="info">indéterminé</td>
			{else}
			<td headers="lth4">{$event->enddate|date_format:"%a %d %B %Y %H:%M"}</td>
			{/if}
			<td headers="lth5">{if empty($event->description) === false}{$event->description|nl2br}{/if}</td>
			<td headers="lth6">
				<ul class="list-inline">
					<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/{$event->id}">modifier</a></li>
					<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/delete/{$event->id}">supprimer</a></li>
				</ul>
			</td>
		{/foreach}
	</tbody>
</table>

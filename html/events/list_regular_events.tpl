<table class="caption-top table table-bordered table-condensed">
	<thead>
		<tr>
			<th id="lth1">État</th>
			<th id="lth2">Service</th>
			<th id="lth3">Périodicité</th>
			<th id="lth4">Début</th>
			<th id="lth5">Fin</th>
			<th id="lth6">Prochaine interruption</th>
			<th id="lth7">Description</th>
			<th id="lth8" class="col-sm-2">Actions</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td headers="lth1">{$STATES[$event->state]}</td>
			<td headers="lth2">{$event->service_name}</td>
			<td headers="lth3">{if $event->period === Event::PERIOD_DAILY}Quotidienne{else if $event->period === Event::PERIOD_WEEKLY}Hebdomadaire{/if}</td>
			<td headers="lth4">{$event->startdate|date_format:"%H:%M"}</td>
			<td headers="lth5">{$event->enddate|date_format:"%H:%M"}</td>
			<td headers="lth6">{$event->startdate|date_format:"%a %d %B %Y %H:%M"}</td>
			<td headers="lth7">{if empty($event->description) === false}{$event->description|nl2br}{/if}</td>
			<td headers="lth8">
				<ul class="list-inline">
					<li class="list-inline-item"><a class="btn btn-sm btn-primary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/{$event->id}">modifier</a></li>
					<li class="list-inline-item"><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/delete/{$event->id}">supprimer</a></li>
				</ul>
			</td>
		{/foreach}
	</tbody>
</table>

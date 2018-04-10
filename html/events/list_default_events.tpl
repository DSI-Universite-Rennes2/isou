<table class="table table-bordered table-condensed">
	<thead>
		<tr>
			<th>État</th>
			<th>Service</th>
			<th>Date de début</th>
			<th>Date de fin</th>
			<th>Description</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td>{$STATES[$event->state]}</td>
			<td>{$services[$event->idservice]}</td>
			<td>{$event->startdate|date_format:"%d %B %Y %H:%M"}</td>
			{if $event->enddate === null}
			<td class="danger">en cours</td>
			{else}
			<td>{$event->enddate|date_format:"%d %B %Y %H:%M"}</td>
			{/if}
			<td>{if !empty($event->description)}{$event->description|nl2br}{/if}</td>
			<td>
				<ul class="list-inline">
					<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/{$event->id}">modifier</a></li>
					<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/delete/{$event->id}">supprimer</a></li>
				</ul>
			</td>
		{/foreach}
	</tbody>
</table>

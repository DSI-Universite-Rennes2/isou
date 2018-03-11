<table class="table table-bordered table-condensed">
<thead>
<tr>
	<th>Service</th>
	<th>Date de début</th>
	<th>Date de fin</th>
	<th>État</th>
	<th>Description</th>
	<th>Actions</th>
</tr>
</thead>

<tbody>
{foreach $events as $event}
<tr>
	<td>{$services[$event->idservice]}</td>
	<td>{$event->startdate|date_format:"%d %B %Y %H:%M"}</td>
	<td{if $event->enddate === NULL} class="danger">en cours{else}>{$event->enddate|date_format:"%d %B %Y %H:%M"}{/if}</td>
	<td>{$STATES[$event->state]}</td>
	<td>{if !empty($event->description)}{$event->description|nl2br}{/if}</td>
	<td>
		<ul class="list-inline">
			<li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/evenements/edit/{$event->id}">modifier</a></li>
			<li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/evenements/delete/{$event->id}">supprimer</a></li>
		</ul>
	</td>
{/foreach}
</tbody>
</table>

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
	<td>{$event->begindate|date_format:"%d %B %Y %H:%M"}</td>
	<td{if $event->enddate === NULL} class="danger">en cours{else}>{$event->enddate|date_format:"%d %B %Y %H:%M"}{/if}</td>
	<td>{$STATES[$event->state]->get_flag_html_renderer()}</td>
	<td>{if !empty($event->description)}{$event->description|nl2br}{/if}</td>
	<td>
		<ul class="list-inline">
			<li><a href="{$smarty.const.URL}/index.php/evenements/edit/{$event->id}"><img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" /></a></li>
			<li><a href="{$smarty.const.URL}/index.php/evenements/delete/{$event->id}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a></li>
		</ul>
	</td>
{/foreach}
</tbody>
</table>

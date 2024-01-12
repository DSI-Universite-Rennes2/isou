<h1 class="visually-hidden">Historique</h1>

<form action="{$smarty.const.URL}/index.php/statistiques#resultat" class="form-horizontal" method="post">

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="event-services">Services</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id="event-services" name="services[]" multiple="true" options=$options_services selected=$smarty.post.services|default:array()}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="event-type">Type d'interruptions</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id="event-type" name="event_type" options=$options_event_types selected=$smarty.post.event_type|default:-1}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="startdate">Date de début</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" id="startdate" name="startdate" type="date" value="{$smarty.post.startdate}" required="1" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="enddate">Date de fin</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" id="enddate" name="enddate" type="date" value="{$smarty.post.enddate}" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="sort">Trier par date</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id=sort name=sort options=$options_sorts selected=$smarty.post.sort|default:1}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="paging">Nombre de résultat par page</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id=paging name=paging options=$options_paging selected=$smarty.post.paging|default:20}
			</dd>
		</div>
	</dl>

	<ul class="list-inline form-submit-buttons-ul">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="afficher" />
		</li>
		<li class="list-inline-item">
			<input class="btn btn-warning" type="submit" name="export" value="exporter au format csv" />
		</li>
	</ul>

</form>

{if isset($events)}
	{if isset($events[0]) === false}
	<p id="resultat" class="alert alert-info">Aucun résultat</p>
	{else}

	<table class="caption-top table table-bordered table-condensed" id="resultat" summary="Historique des interruptions">
	<caption class="text-center">Historique des interruptions : {$count_events} {if $count_events > 1}évènements trouvés{else}évènement trouvé{/if}.</caption>
	<thead>
		<tr>
			<th>Service</th>
			<th>État</th>
			<th>Date de début</th>
			<th>Date de fin</th>
			<th>Durée</th>
			<th>Durée (en minutes)</th>
			<th>Description</th>
			<th>Type d'interruption</th>
		</tr>
	</thead>
	<tbody>
		{foreach $events as $event}
		<tr>
			<td>{$event->name}</td>
			<td>{$STATES[$event->state]}</td>
			<td>{$event->startdate|date_format:'%A %e %B %Y %H:%M'}</td>
			<td>{$event->enddate|date_format:'%A %e %B %Y %H:%M'}</td>
			<td>{$event->total}</td>
			<td>{$event->total_minutes}</td>
			<td width="40%">{$event->description|default:''}</td>
			<td>
				{* TODO: update smarty and remove this hook *}
				{if $event->type === {"UniversiteRennes2\Isou\Event::TYPE_SCHEDULED"|constant}}
					Prévues
				{else}
					Non prévues
				{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
	</table>

	{if isset($pagination[1])}
	<nav>
	<ul class="pagination">
		{foreach $pagination as $page}
			<li{if $page->selected === true} class="active"{/if}><a href="{$page->url}" title="{$page->title}">{$page->label}</a></li>
		{/foreach}
	</ul>
	</nav>
	{/if}

	{/if}
{/if}

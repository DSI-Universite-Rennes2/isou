<main role="main">
<article id="content">

<h1 class="sr-only">Calendrier</h1>

<table id="calendrier" class="table table-bordered" summary="Calendrier répertoriant toutes les intervertions prévues">
	{* <!-- titre du calendrier --> *}
	<caption class="text-center">
	{if $smarty.get.page > 1}
		<a title="Semaine précédente" href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page-1}"><img src="{$smarty.const.URL}/images/action_back.gif" alt="précédent" width="16px" height="16px" /></a>
	{/if}
		&nbsp;Calendrier des interventions &nbsp;
	{if $smarty.get.page < 5}
		<a href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page+1}" title="Semaine suivante"><img src="{$smarty.const.URL}/images/action_forward.gif" alt="suivant" width="16px" height="16px" /></a>
	{/if}
	</caption>

	{* <!-- entêtes du calendrier --> *}
	<thead>
	<tr>
		<th>Lundi</th>
		<th>Mardi</th>
		<th>Mercredi</th>
		<th>Jeudi</th>
		<th>Vendredi</th>
		<th>Samedi</th>
		<th>Dimanche</th>
	</tr>
	</thead>

	{* <!-- corps du calendrier --> *}
	<tbody>
	{foreach $calendar as $week}
	<tr>
		{foreach $week as $day}
			<td {if $day->datetime < $now}class="active"{else if $day->datetime === $now}class="info"{/if}">
				<span id="date-{$day->datetime|date_format:'%d-%B-%Y'}">{$day->datetime|date_format:$day->strftime}</span>
				{if isset($day->services[0])}
				<ul>
					{foreach $day->services as $service}
					<li>
						<a href="#event-{$service->idevent}">{$service->name}</a>
					</li>
					{/foreach}
				</ul>
				{else}
					{if $day->datetime === $now}
						<p>Aucune intervention prévue</p>
					{/if}
				{/if}
			</td>
		{/foreach}
	</tr>
	{/foreach}
	</tbody>
</table>

{if count($events) > 0}
<ul id="events-ul">
{foreach $events as $event}
	<li id="event-{$event->id}" class="events-li">{$event->service} - <span>{$event}{if !empty($event->description)} (<span>{$event->description}</span>){/if}</li>
{/foreach}
</ul>
<p class="sr-only"><a href="#top">retourner en haut de la page</a></p>
{/if}

</article>
</main>

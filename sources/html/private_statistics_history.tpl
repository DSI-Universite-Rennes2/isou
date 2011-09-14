<form action="{$smarty.const.URL}/index.php/statistiques" method="get">
<fieldset>
<legend>Options</legend>

<p>
	<label for="serviceSelect">Trier par service</label>
	{html_options id=serviceSelect name=serviceSelect options=$serviceSelect selected=$smarty.get.serviceSelect|default:''}
</p>

<p>
<label for="beginSort">Trier par date de début</label>
{html_options id=beginSort name=beginSort options=$beginSort selected=$smarty.get.beginSort|default:''}

<label for="endSort">Trier par date de fin</label>
{html_options id=endSort name=endSort options=$endSort selected=$smarty.get.endSort|default:''}

<label for="yearSelect">Année</label>
{html_options id=yearSelect name="yearSelect" values=$yearSelect output=$yearSelect selected=$smarty.get.yearSelect|default:''}

<label for="monthSelect">Mois</label>
{html_options id=monthSelect name=monthSelect options=$monthSelect selected=$smarty.get.monthSelect|default:''}
</p>

<p>
	<label for="typeSelect">Type d'interruptions</label>
	{html_options id=typeSelect name=typeSelect options=$typeSelect selected=$smarty.get.typeSelect|default:''}

	<label for="maxResult">Nombre de résultat par page</label>
	{html_options id=maxResultSelect name=maxResultSelect options=$maxResultSelect selected=$smarty.get.maxResultSelect|default:''}
</p>
</p>

<p>
	<input type="hidden" class="hidden" name="history" value="1">
	<input type="hidden" class="hidden" name="graph" value="none">
	<input type="submit" name="submit" value="Envoyer" />
</p>

</fieldset>
</form>


<table id="historique" summary="Historique des interruptions">
	<caption>Historique des interruptions{if isset($total)} (total : {$total} interruptions){/if}</caption>
	<tr>
	<th>Service</th>
		<th>Date de début</th>
		<th>Date de fin</th>
		<th>Durée</th>
		<th>Description</th>
		<th>Type d'interruption</th>
	</tr>
	{if count($events) > 0}
	{foreach from=$events item=event}
	<tr>
		<td>{$event->nameForUsers}</td>
		<td>{$event->beginDate|date_format:'%A %e %B %Y %H:%M'}</td>
		<td>{$event->endDate|date_format:'%A %e %B %Y %H:%M'}</td>
		<td>{$event->total} minutes</td>
		<td width="40%">{$event->description|default:''}</td>
		<td>
			{if $event->isScheduled == 1}
				Prévues
			{else}
				Non prévues
			{/if}
		</td>
	</tr>
	{/foreach}
	{else}
	<tr><td class="center" colspan="6">Aucun résultat</td></tr>
	{/if}
</table>

{if isset($pageRange)}
	<p id="page">Page : {$pageRange}</p>
{/if}

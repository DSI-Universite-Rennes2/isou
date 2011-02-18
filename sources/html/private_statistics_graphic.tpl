<form action="{$smarty.const.URL}/index.php/statistiques" method="get">

<h1>Options </h1>

<fieldset id="service-fieldset">
<legend>Afficher par service</legend>
{html_checkboxes name="serviceSelect" options=$serviceSelect selected=$smarty.get.serviceSelect|default:'all'  separator='<br />'}
</fieldset>

<p>
	<label class="label" for="yearSelect">Année</label>
	{html_options multiple=multiple id=yearSelect name="yearSelect[]" values=$yearSelect output=$yearSelect selected=$smarty.get.yearSelect|default:{$smarty.now|date_format:"%Y"}}
</p>

<p>
	<label class="label" for="groupbySelect">Grouper</label>
	{html_options id=groupbySelect name=groupbySelect options=$groupbySelect selected=$smarty.get.groupbySelect|default:''}
</p>

<p>
	<label class="label" for="typeSelect">Type d'interruptions</label>
	{html_options id=typeSelect name=typeSelect options=$typeSelect selected=$smarty.get.typeSelect|default:''}
</p>

<p>
	<input type="hidden" class="hidden" name="graph" value="none">
	<input type="submit" name="submit" value="Envoyer" />
</p>


</form>

{if isset($services)}

{if count($services) > 0}
{if $smarty.get.groupbySelect === 'm'}

<table id="historique" summary="Historique des interruptions">
	<caption>Statistique des interruptions de service en minutes</caption>
	<thead>
	<tr>
		<td></td>
		{foreach from=$calendar item=month}
		<th>{$month|date_format:'%b %y'}</th>
		{/foreach}
		<th>Total</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$services item=service}
	<tr>
		<th>{$service->name}</th>
		{foreach from=$calendar key=ym item=month}
		<td>{round({$service->month.$ym}/60)}</td>
		{/foreach}
		<td>{round($service->count/60)}</td>
	</tr>
	</tbody>
	{/foreach}
</table>

{else}

<table id="historique" summary="Historique des interruptions">
	<caption>Statistique des interruptions de service en minutes</caption>
	<thead>
	<tr>
		<td></td>
		<th>Total</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$services item=service}
	<tr>
		<th>{$service->name}</th>
		<td>{round($service->count/60)}</td>
	</tr>
	{/foreach}
	</tbody>
</table>

{/if}

{else}
<p>Aucune statistique disponible avec ces critères.</p>
{/if}

{/if}

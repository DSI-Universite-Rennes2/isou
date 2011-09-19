<form id="form-stat-graphic" action="{$smarty.const.URL}/index.php/statistiques" method="get">

<h1>Options </h1>

<fieldset id="service-fieldset">
<legend>Afficher par service</legend>
{html_checkboxes name="serviceSelect" options=$serviceSelect selected=$smarty.get.serviceSelect|default:'all'  separator='<br />'}
</fieldset>

<!-- <p>
	<label class="label" for="yearSelect">Année</label>
	{html_options multiple=multiple id=yearSelect name="yearSelect[]" values=$yearSelect output=$yearSelect selected=$smarty.get.yearSelect|default:{$smarty.now|date_format:"%Y"}}
</p> -->
<p>
	<label class="label" for="beginDate">Date de début
		<span class="required" title="champs obligatoire">*</span>
		<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de début">?</a>
	</label>
	<input type="text" id="beginDate" name="beginDate" value="{$smarty.get.beginDate|default:'01/01/2011 00:00'}" maxlength="16">
</p>

<p>
	<label class="label" for="endDate">Date de fin
		<span class="required" title="champs obligatoire">*</span>
		<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de fin">?</a>
	</label>
	<input type="text" id="endDate" name="endDate" value="{$smarty.get.endDate|default:'01/04/2011 00:00'}" maxlength="16">
</p>

<p>
	<label class="label" for="groupbySelect">Grouper</label>
	{html_options id=groupbySelect name=groupbySelect options=$groupbySelect selected=$smarty.get.groupbySelect|default:'m'}
</p>

<p>
	<label class="label" for="typeSelect">Type d'interruptions</label>
	{html_options id=typeSelect name=typeSelect options=$typeSelect selected=$smarty.get.typeSelect|default:''}
</p>

<p>
	<input type="hidden" class="hidden" name="graph" value="none">
	<input type="submit" name="submit" value="Envoyer" />
</p>

	<p class="info-date-format">
		<a name="formatDate1"></a>
		Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
		Exemple :<br />
		Pour le {$smarty.now|date_format:'%A %d %B %Y à %H heures et %M minutes'}, la valeur attendue est {$smarty.now|date_format:'%d/%m/%y %H:%M'}.<br /><br />
		<a class="quickaccess-form" href="#form-stat-graphic" title="revenir au formulaire">Revenir au formulaire.</a>
	</p>

</form>

{if isset($services)}

{if count($services) > 0}

<table id="historique" summary="Historique des interruptions">
	<caption>Statistique des interruptions de service en minutes</caption>
	<thead>
	<tr>
		<td></td>
		{foreach from=$calendar key=date item=total}
		{if $smarty.get.groupbySelect == 'a'}
		<th>{$date|date_format:'%Y'}</th>
		{elseif $smarty.get.groupbySelect == 'm'}
		<th>{$date|date_format:'%b-%y'}</th>
		{else}
		<th> {$date|date_format:'%e/%m/%y'} </th>
		{/if}
		{/foreach}
		<th>Total</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$services item=service}
	<tr>
		<th>{$service->name}</th>
		{foreach from=$service->count key=ym item=month}
		<td>{round({$service->count.$ym}/60)}</td>
		{/foreach}
		<td>{round($service->total/60)}</td>
	</tr>
	{/foreach}
	</tbody>
</table>

{else}
<p>Aucune statistique disponible avec ces critères.</p>
{/if}

{/if}

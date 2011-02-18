<div id="content">
<a name="content"></a>
{if count($categories) > 0}
<table id="table" summary="Tableau des perturbations et interruptions de services">
	<caption>Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1" rowspan="2">Service</th>
			<th id="lth2" rowspan="2">&Eacute;tat</th>
			<th id="lth4" colspan="3">Dernière perturbation ou interruption depuis 48h</th>
			<th id="lth5" rowspan="2">Prochaine interruption exceptionnelle&nbsp; programm&eacute;e</th>
			<th id="lth6" rowspan="2">Interruptions r&eacute;guli&egrave;res</th>
		</tr>
		<tr class="header">
			<th class="th-date">D&eacute;but</th>
			<th class="th-date">Fin</th>
			<th class="th-extand">Raison</th>
		</tr>
	</thead>
	<tbody>
	{section name=i loop=$categories}
		<tr class="tr-category">
			<th colspan="10">{$categories[i]->name}</th>
		</tr>
		{section name=j loop=$categories[i]->services}
		<tr class="tr-status-{$flags.{$categories[i]->services[j]->state}->name}">
			{if empty($categories[i]->services[j]->url)}
			<td headers="lth1" class="left">{$categories[i]->services[j]->name}</td>
			{else}
			<td headers="lth1" class="left"><a href="{$categories[i]->services[j]->url}" title="Accéder à la page du service {$categories[i]->services[j]->name}">{$categories[i]->services[j]->name}</a></td>
			{/if}
			<td headers="lth2"><img src="{$smarty.const.URL}/images/{$flags.{$categories[i]->services[j]->state}->src}" alt="{$flags.{$categories[i]->services[j]->state}->alt}" /></td>
			{if $categories[i]->services[j]->closed === TRUE}
			<td headers="lth4" colspan="5">
				{if is_null($categories[i]->services[j]->endDate)}
					Service fermé depuis le {$categories[i]->services[j]->beginDate|date_format:"%A %e %B %Y"}.
				{else}
					Service fermé depuis le {$categories[i]->services[j]->beginDate|date_format:"%A %e %B %Y"}.
					Réouverture le {$categories[i]->services[j]->endDate|date_format:"%A %e %B %Y"}.
				{/if}
				{if !empty($categories[i]->services[j]->reason)}
					({$categories[i]->services[j]->reason|nl2br})
				{/if}
			</td>
			{else}
			{if $categories[i]->services[j]->beginDateLastEvent === NULL}
			<td headers="lth4" class="bold">&nbsp;</td>
			<td headers="lth4" class="bold">&nbsp;</td>
			<td headers="lth4" class="center">&nbsp;</td>
			{else}
			<td headers="lth4" class="bold">{$categories[i]->services[j]->beginDateLastEvent|date_format:"%A %d %B %Y %H:%M"}</td>
			{if $categories[i]->services[j]->endDateLastEvent === NULL}
			<td headers="lth4" class="bold">NC</td>
			{else}
			<td headers="lth4" class="bold">{$categories[i]->services[j]->endDateLastEvent|date_format:"%A %d %B %Y %H:%M"}</td>
			{/if}
			<td headers="lth4" class="center">{$categories[i]->services[j]->reasonLastEvent|nl2br}</td>

			{/if}
			<td headers="lth5" class="bold justify">{$categories[i]->services[j]->nextEvent}</td>
			<td headers="lth6" class="bold">{$categories[i]->services[j]->regularInterruption}</td>
			{/if}
		</tr>
		{/section}
	{/section}
	</tbody>
</table>
{else}
<p id="no-event">Aucun service disponible pour le moment.</p>
{/if}

</div>

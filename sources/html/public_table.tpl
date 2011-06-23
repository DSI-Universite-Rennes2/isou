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
			<th id="lth41">D&eacute;but</th>
			<th id="lth42">Fin</th>
			<th id="lth43">Raison</th>
		</tr>
	</thead>
	<tbody>
	{section name=i loop=$categories}
		<tr class="tr-category">
			<th colspan="10">{$categories[i]->name}</th>
		</tr>
		{section name=j loop=$categories[i]->services}
		<tr class="tr-status-{$flags.{$categories[i]->services[j]->getState()}->name}">
			{if $categories[i]->services[j]->getUrl() === NULL}
			<td headers="lth1" class="left">{$categories[i]->services[j]->getNameForUsers()}</td>
			{else}
			<td headers="lth1" class="left"><a href="{$categories[i]->services[j]->getUrl()}" title="Accéder à la page du service {$categories[i]->services[j]->getNameForUsers()}">{$categories[i]->services[j]->getNameForUsers()}</a></td>
			{/if}
			<td headers="lth2"><img src="{$smarty.const.URL}/images/{$flags.{$categories[i]->services[j]->getState()}->src}" alt="{$flags.{$categories[i]->services[j]->getState()}->alt}" /></td>
			{if $categories[i]->services[j]->isClosed() === TRUE}
			<td headers="lth4" colspan="5">
				{if $categories[i]->services[j]->closedEvent->getDescription() !== NULL}
					{$categories[i]->services[j]->closedEvent->getDescription()|nl2br}
				{else}
					{if $categories[i]->services[j]->closedEvent->getEndDate() === NULL}
						Service fermé depuis le {$categories[i]->services[j]->closedEvent->getBeginDate()|date_format:"%A %e %B %Y"}.
					{else}
						Service fermé depuis le {$categories[i]->services[j]->closedEvent->getBeginDate()|date_format:"%A %e %B %Y"}.
						Réouverture le {$categories[i]->services[j]->closedEvent->getEndDate()|date_format:"%A %e %B %Y"}.
					{/if}
				{/if}
			</td>
			{else}
			{if count($categories[i]->services[j]->lastEvent) === 0}
			<td headers="lth4 lth41" class="bold">&nbsp;</td>
			<td headers="lth4 lth42" class="bold">&nbsp;</td>
			<td headers="lth4 lth43" class="center">&nbsp;</td>
			{else}
			<td headers="lth4 lth41" class="bold">{$categories[i]->services[j]->lastEvent[0]->getBeginDate()|date_format:"%A %d %B %Y %H:%M"}</td>
			{if $categories[i]->services[j]->lastEvent[0]->getEndDate() === NULL}
			<td headers="lth4 lth42" class="bold">NC</td>
			{else}
			<td headers="lth4 lth42" class="bold">{$categories[i]->services[j]->lastEvent[0]->getEndDate()|date_format:"%A %d %B %Y %H:%M"}</td>
			{/if}
			<td headers="lth4 lth43" class="center">{$categories[i]->services[j]->lastEvent[0]->getDescription()|nl2br}</td>

			{/if}
			<td headers="lth5" class="bold justify">
				{if count($categories[i]->services[j]->nextEvent) === 1}
					Interruption du {$categories[i]->services[j]->nextEvent[0]->getBeginDate()|date_format:"%A %d %B %Y %H:%M"} au {$categories[i]->services[j]->nextEvent[0]->getEndDate()|date_format:"%A %d %B %Y %H:%M"}
					{if $categories[i]->services[j]->nextEvent[0]->getDescription() !== NULL}
						<br />
						( {$categories[i]->services[j]->nextEvent[0]->getDescription()|nl2br} )
					{/if}
				{/if}
			</td>
			<td headers="lth6" class="bold">
			{if count($categories[i]->services[j]->regularInterruption) > 0}
				<ul class="regular">
				{foreach from=$categories[i]->services[j]->regularInterruption item=ri}
					<li>
					{if $ri->getPeriod() === 7*24*60*60}
						{* Tous les mois *}
						Tous les {$ri->getBeginDate()|date_format:"%d"} du mois de {$ri->getBeginDate()|date_format:"%H:%M"} à {$ri->getEndDate()|date_format:"%H:%M"}
					{else if $ri->getPeriod() === 7*24*60*60}
						{* Toutes les semaines *}
						Tous les {$ri->getBeginDate()|date_format:"%A"} de {$ri->getBeginDate()|date_format:"%H:%M"} à {$ri->getEndDate()|date_format:"%H:%M"}
					{else}
						{* Tous les jours *}
						Tous les jours de {$ri->getBeginDate()|date_format:"%H:%M"} à {$ri->getEndDate()|date_format:"%H:%M"}
					{/if}

					{if $ri->getDescription() !== NULL}
						<br />
						{$ri->getDescription()|nl2br}
					{/if}
					</li>
				{/foreach}
				</ul>
			{/if}
			</td>
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

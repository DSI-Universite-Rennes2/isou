	{if count($regular) === 0}
	<p class="pform1">Aucune interruption régulière définie</p>
	{else}
	<ul class="form">
	{foreach name=i item=event from=$regular}
	{if isset($event->edit)}
		<li id="selected">{include file="private_events_edit.tpl"}</li>
	{else}
		<li{if isset($event->group)} class="group"{/if}>
			<p class="pform1">Service <span class="bold">{$event->nameForUsers}</span>
				interrompu
			{if $event->endDate === 604800}
				{* <!-- 7*24*60*60 = 604800 // tous les mois --> *}
				tous les {$event->beginDate|date_format:"%d"} du mois de {$event->beginDate|date_format:"%H:%M"} à {$scheduled[i]->endDate|date_format:"%H:%M"}
			{elseif $event->endDate === 604800}
				{* <!-- 7*24*60*60 = 604800 // toutes les semaines --> *}
				tous les {$event->beginDate|date_format:"%A"} de {$event->beginDate|date_format:"%H:%M"} à {$scheduled[i]->endDate|date_format:"%H:%M"}
			{else}
				{* <!-- tous les jours --> *}
				tous les jours de {$event->beginDate|date_format:"%H:%M"} à {$event->endDate|date_format:"%H:%M"}
			{/if}
				<a href="{$smarty.const.URL}/index.php/evenements/reguliers?modify={$event->idEvent}&p=1#R{$smarty.foreach.i.index}" name="R{$smarty.foreach.i.index+1}" title="modifier"><img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" /></a>
				<a href="{$smarty.const.URL}/index.php/evenements/reguliers?delete={$event->idEvent}" title="supprimer"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a>
			</p>
			{* <!-- description --> *}
			{if !empty($event->description)}
				<p class="pform2">Raison : {$event->description|nl2br}</p>
			{/if}
		</li>
	{/if}
	{/foreach}
	</ul>
	<p>
		<a href="#menu" title="Retourner au menu de la page">
			<img src="{$smarty.const.URL}/images/page_up.gif" alt="remonter" />
		</a>
	</p>
	{/if}

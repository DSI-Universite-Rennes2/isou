	{if count($scheduled) === 0}
	<p class="pform1">Aucune interruption prévue</p>
	{else}
	<ul class="form">
	{foreach name=i item=event from=$scheduled}
	{if isset($event->edit)}
		<li id="selected">{include file="private_events_edit.tpl"}</li>
	{else}
		<li{if isset($event->group)} class="group"{/if}>
			<p class="pform1">Service <span class="bold">{$event->nameForUsers}</span>
				interrompu
			{if $event->endDate === NULL}
				à partir du {$event->beginDate|date_format:"%d %B %Y %H:%M"}
			{else}
				du {$event->beginDate|date_format:"%d %B %Y %H:%M"} au {$event->endDate|date_format:"%d %B %Y %H:%M"}
			{/if}
				<a href="{$smarty.const.URL}/index.php/evenements/prevus?modify={$event->idEvent}#S{$smarty.foreach.i.index}" name="S{$smarty.foreach.i.index+1}" title="modifier"><img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" /></a>
				<a href="{$smarty.const.URL}/index.php/evenements/prevus?delete={$event->idEvent}" title="supprimer"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a>
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


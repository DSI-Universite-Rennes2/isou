	{if count($messages) === 0}
	<p class="pform1">Aucun message</p>
	{else}
	<ul class="form">
	{foreach name=i item=event from=$messages}
	{if isset($event->edit)}
		<li id="selected">{include file="private_events_edit_messages.tpl"}</li>
	{else}
		<li class="{$event->classCss}">
			<p class="pform1">
				<span class="top">Message d'information : </span>
				<span class="shortTexts">{$event->shortText|nl2br}</span>
				<span class="top">
				<a href="{$smarty.const.URL}/index.php/evenements?modify={$event->idEvent}#M{$smarty.foreach.i.index}" name="M{$smarty.foreach.i.index+1}" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" />
				</a>
				<a href="{$smarty.const.URL}/index.php/evenements?delete={$event->idEvent}&amp;message" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" />
				</a>
				</span>
			</p>
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


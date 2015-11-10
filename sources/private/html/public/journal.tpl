<div id="content">
<p><a name="content"></a></p>
<table id="list" summary="Journal des perturbations et interruptions de services Rennes 2">
	<caption>Journal des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth2"><!-- &Eacute;tat --> &nbsp;</th>
			<th id="lth1">Service</th>
			<th id="lth4">Début</th>
			<th id="lth5">Fin</th>
			<th id="lth3">Raison</th>
		</tr>
	</thead>
	{foreach name=i item=ievents key=day from=$events}
	<thead>
		<tr class="header">
			<th colspan="5" class="thdate">{$day|date_format:"%A %e %B"}</th>
		</tr>
	</thead>
	<tbody>
	{foreach name=k item=event from=$ievents}
		{if isset($event->first)}

		{/if}
		<tr class="{$event->class}">
			{if $event->typeEvent === 2}
			<td></td>
			<td colspan="4">
				{$event->shortText}
				{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
					<a href="{$smarty.const.URL}/index.php/journal?modify={$event->idEvent}#edit">
						<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" />
					</a>
					<a href="{$smarty.const.URL}/index.php/journal?delete={$event->idEvent}#delete">
						<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" />
					</a>
				{/if}
			</td>
			{else}
			<td>{if isset($event->state)}<img src="{$smarty.const.URL}/images/{$STATES.{$event->state}->src}" alt="{$smarty.const.URL}/images/{$STATES.{$event->state}->alt}" />{/if}</td>
			<td>
				{$event->nameForUsers}
				{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
					<a href="{$smarty.const.URL}/index.php/journal?modify={$event->idEvent}#edit">
						<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" />
					</a>
					<a href="{$smarty.const.URL}/index.php/journal?delete={$event->idEvent}#delete">
						<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" />
					</a>
				{/if}
			</td>
			<td>{$event->beginDate|date_format:"%A %e %B %Y %H:%M"}</td>
			<td>{$event->endDate|date_format:"%A %e %B %Y %H:%M"}</td>
			<td>{$event->description|nl2br}</td>
			{/if}
		</tr>
	{/foreach}
	{if $smarty.foreach.k.iteration === 0}
		<tr><td colspan="5">Aucun évènement</td></tr>
	{/if}
	</tbody>
	{/foreach}
</table>

{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
	{if isset($smarty.get.modify)}
		<a name="edit"></a>
		{if $currentEdit->class === "messages"}
		{include file="private_events_edit_messages.tpl"}
		{else}
		{include file="private_events_edit.tpl"}
		{/if}
	{/if}

	{if isset($smarty.get.delete)}
	<div id="update">
		{if isset($smarty.get.message)}
			<p>Voulez-vous vraiment effacer le message informatif #{$smarty.get.delete} ?</p>
		{else}
			<p>Voulez-vous vraiment effacer l'évènement #{$smarty.get.delete} ?</p>
		{/if}
		<form action="{$smarty.const.URL}/index.php/evenements#delete" method="post">
			<p>
				<a name="delete"></a>
				<input type="submit" name="delete" value="Oui"> <input type="submit" value="Non">
				<input class="hidden" type="hidden" name="idDelEvent" value="{$smarty.get.delete}">
				{if isset($smarty.get.message)}
				<input class="hidden" type="hidden" name="message" value="1">
				{/if}
			</p>
		</form>
	</div>
	{/if}
{/if}
</div>

<div id="content">
	<a name="content"></a>

	<h2>Liste des interruptions qui ont eu lieu depuis lundi dernier et qui auront lieu au plus tard dans une trentaine de jours.</h2>

	{if !isset($smarty.get.modify) || !isset($smarty.get.delete)}
		{include file="private_events_add.tpl"}
		{include file="private_events_add_messages.tpl"}
	{/if}

	{if isset($smarty.get.delete)}
	<div id="update">
		{if isset($smarty.get.message)}
			<p>Voulez-vous vraiment effacer le message informatif #{$smarty.get.delete} ?</p>
		{else}
			<p>Voulez-vous vraiment effacer l'évènement #{$smarty.get.delete} ?</p>
		{/if}
		<form action="{$smarty.const.URL}/index.php/evenements" method="post">
			<p>
				<input type="submit" name="delete" value="Oui"> <input type="submit" value="Non">
				<input class="hidden" type="hidden" name="idDelEvent" value="{$smarty.get.delete}">
				{if isset($smarty.get.message)}
				<input class="hidden" type="hidden" name="message" value="1">
				{/if}
			</p>
		</form>
	</div>
	{/if}

	<div class="events">
	<h3><a name="U0">Interruptions non prévues</a></h3>
	{include file="private_events_view_unscheduled.tpl"}
	</div>

	<div class="events">
	<h3><a name="S0">Interruptions prévues</a></h3>
	{include file="private_events_view_scheduled.tpl"}
	</div>

	<div class="events">
	<h3><a name="R0">Interruptions régulières</a></h3>
	{include file="private_events_view_regular.tpl"}
	</div>

	<div class="events">
	<h3><a name="C0">Services fermés</a></h3>
	{include file="private_events_view_closed.tpl"}
	</div>

	<div class="events">
	<h3><a name="M0">Messages informatifs</a></h3>
	{include file="private_events_view_messages.tpl"}
	</div>

</div>

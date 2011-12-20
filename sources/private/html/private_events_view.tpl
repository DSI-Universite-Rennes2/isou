<div id="content">
	<a name="content"></a>

	<h2>Liste des interruptions qui ont eu lieu depuis lundi dernier et qui auront lieu au plus tard dans une trentaine de jours.</h2>

	<!-- <div id="events-quick-access">
	<p>Accès rapide :</p>
	<ul>
		<li><a href="#form-add-event">ajouter un évènement</a></li>
		<li><a href="#form-add-info">ajouter un message informatif</a></li>
		<li><a href="#U0">accéder aux interruptions non prévues</a></li>
		<li><a href="#S0">accéder aux interruptions prévues</a></li>
		<li><a href="#R0">accéder aux interruptions régulières</a></li>
		<li><a href="#C0">accéder aux services fermés</a></li>
		<li><a href="#M0">accéder aux messages informatifs</a></li>
	</ul>
	</div> -->
	<ul id="events-menu">
		<li{if $smarty.get.type == 0} id="events-menu-selected"{/if}><a href="{$smarty.const.URL}/index.php/evenements/nonprevus">interruptions non prévues</a></li>
		<li{if $smarty.get.type == 1} id="events-menu-selected"{/if}><a href="{$smarty.const.URL}/index.php/evenements/prevus">interruptions prévues</a></li>
		<li{if $smarty.get.type == 2} id="events-menu-selected"{/if}><a href="{$smarty.const.URL}/index.php/evenements/reguliers">interruptions régulières</a></li>
		<li{if $smarty.get.type == 3} id="events-menu-selected"{/if}><a href="{$smarty.const.URL}/index.php/evenements/fermes">services fermés</a></li>
		<li{if $smarty.get.type == 4} id="events-menu-selected"{/if}><a href="{$smarty.const.URL}/index.php/evenements/messages">messages informatifs</a></li>
	</ul>

	{if isset($smarty.get.delete)}
	<div id="update">
		{if isset($smarty.get.message)}
			<p>Voulez-vous vraiment effacer le message informatif #{$smarty.get.delete} ?</p>
		{else}
			<p>Voulez-vous vraiment effacer l'évènement #{$smarty.get.delete} ?</p>
		{/if}
		<form action="{$smarty.const.URL}/index.php/evenements/{if $smarty.get.type == 0}nonprevus{elseif $smarty.get.type == 2}reguliers{elseif $smarty.get.type == 3}fermes{elseif $smarty.get.type == 4}messages{else}prevus{/if}" method="post">
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

	{if count($forcedservices) > 0}
		<h3>Liste des services actuellement forcés</h3>
		<ul>
		{foreach $forcedservices as $forcedservice}
			<li>
				<img src="{$smarty.const.URL}/images/{$flags.{$forcedservice->state}->src}" alt="{$flags.{$forcedservice->state}->alt}" />
				{$forcedservice->nameForUsers}
			</li>
		{/foreach}
		</ul>
	{/if}

	{if $smarty.get.type == 0}
		<h3>Interruptions non prévues</h3>
		<p id="add-form"><a id="button-add-event" href="#form-add-event">Ajouter une interruption non prévue</a></p>
		{include file="private_events_view_unscheduled.tpl"}
		{include file="private_events_add.tpl"}
	{else if $smarty.get.type == 2}
		<h3>Interruptions régulières</h3>
		<p id="add-form"><a id="button-add-event" href="#form-add-event">Ajouter une interruption régulière</a></p>
		{include file="private_events_view_regular.tpl"}
		{include file="private_events_add.tpl"}
	{else if $smarty.get.type == 3}
		<h3>Services fermés</h3>
		<p id="add-form"><a id="button-add-event" href="#form-add-event">Ajouter une fermeture de service</a></p>
		{include file="private_events_view_closed.tpl"}
		{include file="private_events_add.tpl"}
	{else if $smarty.get.type == 4}
		<h3>Messages informatifs</h3>
		<p id="add-form"><a id="button-add-info" href="#form-add-info">Ajouter un message informatif</a></p>
		{include file="private_events_view_messages.tpl"}
		{include file="private_events_add_messages.tpl"}
	{else}
		<h3>Interruptions prévues</h3>
		<p id="add-form"><a id="button-add-event" href="#form-add-event">Ajouter une interruption prévue</a></p>
		{include file="private_events_view_scheduled.tpl"}
		{include file="private_events_add.tpl"}
	{/if}
</div>

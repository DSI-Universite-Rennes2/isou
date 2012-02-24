<ul class="submenu" id="typelist">
	<li{if !isset($smarty.get.visits) && !isset($smarty.get.history)} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques" title="Afficher les statistiques d'interruptions de service">statistiques des interruptions de service</a></li>
	<li{if isset($smarty.get.history)} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques?history=1" title="Afficher les historiques d'interruptions de service">historique des interruptions de service</a></li>
	<li{if isset($smarty.get.visits)} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques?visits=1&year={$smarty.const.TIME|date_format:'%Y'}" title="Afficher les statistiques de visite">statistiques des visites</a></li>
</ul>

<div id="content">
<a name="content"></a>
	{if isset($smarty.get.visits)}
		{include file="private_statistics_visits.tpl"}
	{else if isset($smarty.get.history)}
		{include file="private_statistics_history.tpl"}
	{else}
		{include file="private_statistics_graphic.tpl"}
	{/if}
</div>


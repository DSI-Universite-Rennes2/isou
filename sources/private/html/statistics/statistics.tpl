<ul class="submenu" id="typelist">
	<li{if !isset($smarty.get.history)} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques" title="Afficher les statistiques d'interruptions de service">statistiques des interruptions de service</a></li>
	<li{if isset($smarty.get.history)} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques?history=1" title="Afficher les historiques d'interruptions de service">historique des interruptions de service</a></li>
</ul>

<div id="content">
<a name="content"></a>
	{if isset($smarty.get.history)}
		{include file="statistics/history.tpl"}
	{else}
		{include file="statistics/graphic.tpl"}
	{/if}
</div>


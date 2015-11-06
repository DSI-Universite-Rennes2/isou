<div class="submenu-div">
<ul class="submenu-ul" id="typelist">
	<li class="menu-ul-items{if !isset($smarty.get.history)} active{/if}">
		<a class="menu-entries" href="{$smarty.const.URL}/index.php/statistiques" title="Afficher les statistiques d'interruptions de service">statistiques des interruptions de service</a>
	</li>
	<li class="menu-ul-items{if isset($smarty.get.history)} active{/if}">
		<a class="menu-entries" href="{$smarty.const.URL}/index.php/statistiques?history=1" title="Afficher les historiques d'interruptions de service">historique des interruptions de service</a>
	</li>
</ul>
</div>

<div id="content">
<a name="content"></a>
	{if isset($smarty.get.history)}
		{include file="statistics/history.tpl"}
	{else}
		{include file="statistics/graphic.tpl"}
	{/if}
</div>


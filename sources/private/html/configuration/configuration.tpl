<div class="submenu-div">
	<h3 class="menu-title">Afficher :</h3>
	<ul class="submenu-ul">
		{if isset($smarty.get.type) && $smarty.get.type === 'advanced'}
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li class="menu-ul-items active">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
			</li>
		{elseif isset($smarty.get.type) && $smarty.get.type === 'changelog'}
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li class="menu-ul-items active">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
			</li>
		{else}
			<li class="menu-ul-items active">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
			</li>
		{/if}
	</ul>
</div>

<div id="content">
	{if isset($smarty.get.type) && $smarty.get.type === 'advanced'}
		{include file="configuration/advanced.tpl"}
	{elseif isset($smarty.get.type) && $smarty.get.type === 'changelog'}
		{include file="configuration/changelog.tpl"}
	{else}
		{include file="configuration/general.tpl"}
	{/if}
</div>


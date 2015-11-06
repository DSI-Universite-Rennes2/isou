<div class="submenu">
	<h3>Afficher :</h3>
	<ul>
		{if isset($smarty.get.type) && $smarty.get.type === 'advanced'}
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li class="selectedPage">
				<a href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
			</li>
		{elseif isset($smarty.get.type) && $smarty.get.type === 'changelog'}
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li class="selectedPage">
				<a href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
			</li>
		{else}
			<li class="selectedPage">
				<a href="{$smarty.const.URL}/index.php/configuration" title="Afficher la configuration générale">générale</a>
			</li>
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration?type=advanced" title="Afficher la configuration avancée">avancée</a>
			</li>
			<li>
				<a href="{$smarty.const.URL}/index.php/configuration?type=changelog" title="Afficher la changelog">changelog</a>
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


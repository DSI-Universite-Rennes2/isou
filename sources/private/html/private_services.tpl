<div class="submenu">
	<h3>Afficher :</h3>
	<ul>
		{if isset($smarty.get.service) && $smarty.get.service === 'nagios'}
			<li><a href="{$smarty.const.URL}/index.php/services?service=isou" title="Afficher la liste des services Isou">Services ISOU</a></li>
			<li class="selectedPage"><a href="{$smarty.const.URL}/index.php/services?service=nagios" title="Afficher la liste des services Nagios">Services NAGIOS</a></li>
		{else}
		<li class="selectedPage"><a href="{$smarty.const.URL}/index.php/services?service=isou" title="Afficher la liste des services Isou">Services ISOU</a></li>
		<li><a href="{$smarty.const.URL}/index.php/services?service=nagios" title="Afficher la liste des services Nagios">Services NAGIOS</a></li>
		{/if}
	</ul>
</div>

<div id="content">
	{if isset($smarty.get.service) && $smarty.get.service === 'nagios'}
		{include file="private_services_nagios.tpl"}
	{else}
		{include file="private_services_isou.tpl"}
	{/if}
</div>



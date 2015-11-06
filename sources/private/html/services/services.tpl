<div class="submenu-div">
	<h3 class="menu-title">Afficher :</h3>
	<ul class="submenu-ul">
		{if isset($smarty.get.service) && $smarty.get.service === 'nagios'}
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/services?service=isou" title="Afficher la liste des services Isou">Services ISOU</a>
			</li>
			<li class="menu-ul-items active">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/services?service=nagios" title="Afficher la liste des services Nagios">Services NAGIOS</a>
			</li>
		{else}
			<li class="menu-ul-items active">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/services?service=isou" title="Afficher la liste des services Isou">Services ISOU</a>
			</li>
			<li class="menu-ul-items">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/services?service=nagios" title="Afficher la liste des services Nagios">Services NAGIOS</a>
			</li>
		{/if}
	</ul>
</div>

<div id="content">
	{if isset($smarty.get.service) && $smarty.get.service === 'nagios'}
		{include file="services/nagios.tpl"}
	{else}
		{include file="services/isou.tpl"}
	{/if}
</div>



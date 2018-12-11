<h1 class="sr-only">Services</h1>

<ul class="nav nav-tabs">
{foreach $submenus as $menu}
	<li{if $menu->selected === true} class="active"{/if}><a href="{$menu->url}" title="{$menu->title}">{$menu->label}</a></li>
{/foreach}
</ul>

{include file=$MONITORING_TEMPLATE}

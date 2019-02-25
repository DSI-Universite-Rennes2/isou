<h2 class="sr-only">Apparence</h2>

<ul class="nav nav-tabs">
{foreach $submenus as $menu}
	<li{if $menu->selected === true} class="active"{/if}><a href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>

{include file=$VIEW_TEMPLATE}

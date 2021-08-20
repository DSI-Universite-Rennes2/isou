<h1 class="sr-only">Annonce</h1>

{if isset($submenu['notification']) === true}
<ul class="nav nav-tabs">
{foreach $submenu as $menu}
	{if $menu->selected === true}
	<li class="active"><a href="{$menu->url}">{$menu->label}</a></li>
	{else}
	<li><a href="{$menu->url}">{$menu->label}</a></li>
	{/if}
{/foreach}
</ul>
{/if}

{include file=$subtemplate}

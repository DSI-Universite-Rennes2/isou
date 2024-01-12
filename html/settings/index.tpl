<h1 class="visually-hidden">Configuration</h1>

<ul class="flex-column flex-lg-row nav nav-tabs">
{foreach $services_menu as $menu}
	<li class="nav-item"><a class="{if $menu->selected === true}active {/if}nav-link" href="{$menu->url}" title="{$menu->title}">{$menu->label}</a></li>
{/foreach}
</ul>

{include file=$SUBTEMPLATE}

<h1 class="visually-hidden">Annonce</h1>

{if isset($submenu['notification']) === true}
<ul class="flex-column flex-lg-row nav nav-tabs">
{foreach $submenu as $menu}
	<li class="nav-item"><a class="{if $menu->selected === true}active {/if}nav-link" href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>
{/if}

{include file=$subtemplate}

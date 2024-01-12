<h2 class="visually-hidden">Apparence</h2>

<ul class="flex-column flex-lg-row nav nav-tabs">
{foreach $submenus as $menu}
	<li class="nav-item"><a class="{if $menu->selected === true}active {/if}nav-link" href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>

{include file=$VIEW_TEMPLATE}

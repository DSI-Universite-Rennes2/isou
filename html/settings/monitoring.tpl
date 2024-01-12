<h2 class="visually-hidden">Monitoring</h2>

<ul class="flex-column flex-lg-row nav nav-tabs">
{foreach $submenus as $menu}
	<li class="nav-item"><a class="{if $menu->selected === true}active {/if}nav-link" href="{$menu->url}" title="{$menu->title}">{$menu->label}</a></li>
{/foreach}
</ul>

{include file=$MONITORING_TEMPLATE}

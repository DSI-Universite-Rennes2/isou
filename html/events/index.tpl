<h1 class="visually-hidden">Évènements</h1>

<ul class="flex-column flex-lg-row nav nav-tabs">
{foreach $submenu as $menu}
	<li class="nav-item"><a class="{if $menu->selected === true}active {/if}nav-link" href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>

{if isset($event) === true}
	{* Formulaire d'édition et de suppression d'évènements. *}
	{include file=$subtemplate}
{else}
	{* Liste des évènements. *}
	{if $show_add_button === true}
		<p class="text-end">
			<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/0">Ajouter un évènement</a>
		</p>
	{/if}

	{include file="common/messages_session.tpl"}

	{if isset($events[0]) === false}
		<p class="alert alert-info">Aucune interruption enregistrée.</p>
	{else}
		{include file=$subtemplate}
	{/if}
{/if}

<main role="main">
<article id="content">

<h1 class="sr-only">Évènements</h1>

<ul class="nav nav-tabs">
{foreach $submenu as $menu}
	<li{if $menu->selected === TRUE} class="active"{/if}><a href="{$menu->url}">{$menu->label}</a></li>
{/foreach}
</ul>

<p class="text-right">
	<a class="btn btn-primary" href="{$smarty.const.URL}/index.php/evenements/edit/0">Ajouter un évènement</a>
</p>

{include file="common/messages_session.tpl"}

{if !isset($events[0])}

<p class="alert alert-info">Aucune interruption rencontrée</p>

{else}

<table class="table table-bordered">
<thead>
<tr>
	<th>Service</th>
	<th>Date de début</th>
	<th>Date de fin</th>
	<th>État</th>
	<th>Description</th>
	<th>Actions</th>
</tr>
</thead>

<tbody>
{foreach $events as $event}
<tr>
	<td>{$event->name}</td>
	<td>{$event->begindate|date_format:"%d %B %Y %H:%M"}</td>
	<td{if $event->enddate === NULL} class="danger">en cours{else}>{$event->enddate|date_format:"%d %B %Y %H:%M"}{/if}</td>
	<td>{$STATES[$event->state]->get_flag_html_renderer()}</td>
	<td>{if !empty($event->description)}{$event->description|nl2br}{/if}</td>
	<td>
		<ul class="list-inline">
			<li><a href="{$smarty.const.URL}/index.php/evenements/edit/{$event->id}"><img src="{$smarty.const.URL}/images/edit.png" alt="modifier" width="16px" height="16px" /></a></li>
			<li><a href="{$smarty.const.URL}/index.php/evenements/delete/{$event->id}"><img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" width="16px" height="16px" /></a></li>
		</ul>
	</td>
{/foreach}
</tbody>
</table>

{/if}

</article>
</main>

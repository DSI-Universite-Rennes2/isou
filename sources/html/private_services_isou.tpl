<ul class="legend">
	<li><span class="legend unassign">&nbsp;</span>&nbsp;Service final ISOU sans dépendance avec NAGIOS&nbsp;</li>
	<li><span class="legend forced">&nbsp;</span>&nbsp;Service final dont l'état est bloqué par ISOU&nbsp;</li>
</ul>

{if !isset($smarty.get.modify) && !isset($smarty.get.delete)}
	{include file="private_services_isou_add.tpl"}
{/if}

{if isset($smarty.get.delete)}
<div id="update">
	{if empty($nameService)}
		<p>Le service que vous souhaitez effacer n'existe pas.</p>
	{else}
		<form action="{$smarty.const.URL}/index.php/services?service={$smarty.get.service}" method="post">
			<p>
				Voulez-vous vraiment effacer le service {$nameService} ?<br />
				<input type="submit" name="delete" value="Oui"> <input type="submit" value="Non">
				<input class="hidden" type="hidden" name="idDelService" value="{$smarty.get.delete}">
			</p>
		</form>
	{/if}
</div>
{/if}

{if isset($currentEdit)}
	{include file="private_services_isou_edit.tpl"}
{/if}

<div id="list">
{foreach item=category from=$categories name=cat}

	<h2>{$category->name}</h2>

	<table class="table-services">

		<summary></summary>

		<thead>
		<tr>
			<th id="head-name-{$smarty.foreach.cat.index}" class="head-name">
				<label>Nom du service pour les usagers</label>
			</th>
			<th id="head-state-{$smarty.foreach.cat.index}" class="head-state">
				<label>Etat du service</label>
			</th>
			<th id="head-action-{$smarty.foreach.cat.index}" class="head-action">
				<label>Actions</label>
			</th>
		</tr>
		</thead>

		<tbody>
		{foreach item=service from=$category->services name=serv}

		<tr{if !empty($service->css)} class="{$service->css}"{/if}>
			<td id="service-{$service->idService}" headers="head-name-{$smarty.foreach.cat.index}">
				{if count($service->parents) > 0}
				{$service->nameForUsers}
				<div class="parentsList">
					{include file="public_news_recursive_parents.tpl" parents=$service->parents}
				</div>
				{else}
					{$service->nameForUsers}
				{/if}
				{if !empty($service->url)}
					<br />URL : {$service->url}
				{/if}
			</td>
			<td headers="head-state-{$smarty.foreach.cat.index}">{$flags.{$service->state}->alt}</td>
			<td class="actionbox" headers="head-action-{$smarty.foreach.cat.index}">
				{if $service->visible === "1"}
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;mask={$service->idService}#service-{$service->idService}" title="masquer">
					<img src="{$smarty.const.URL}/images/page_user_dark.gif" alt="visible" />
				</a>
				{else}
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;show={$service->idService}#service-{$service->idService}" title="afficher">
					<img src="{$smarty.const.URL}/images/page_user_light.gif" alt="caché" />
				</a>
				{/if}
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;modify={$service->idService}#edit" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" />
				</a>
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;delete={$service->idService}#S{$smarty.foreach.cat.index+$smarty.foreach.serv.index+1}" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" />
				</a>
			</td>
		</tr>

		{/foreach}
		</tbody>

	</table>
{/foreach}
</div>

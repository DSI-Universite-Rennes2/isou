{if isset($update)}
	{$update}
{/if}

<ul class="legend">
	<li><span class="legend hide">&nbsp;</span>&nbsp;Service NAGIOS utilisé par un service final ISOU&nbsp;</li>
	<li><span class="legend unassign">&nbsp;</span>&nbsp;Service NAGIOS non utilisé dans ISOU&nbsp;</li>
	<li><span class="legend nomorein">&nbsp;</span>&nbsp;Service retiré de NAGIOS&nbsp;</li>
</ul>

{* <!-- FORMULAIRE D'AJOUT DES DONNEES --> *}
{if !isset($smarty.get.modify) && !isset($smarty.get.delete)}
	{include file="private_services_nagios_add.tpl"}
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
	{include file="private_services_nagios_edit.tpl"}
{/if}


<div class="list">

{if count($nagiosServices)>0}

<h3>{$countNagiosServices} Services Nagios gérés dans Isou</h3>
<p class="header">
	<label class="longbox">Nom informatique du service</label>
	<label class="action actionbox">Actions</label>
</p>

<ul class="listing">
{foreach item=service from=$nagiosServices name=serv}
	<li>
		<p>
			<span class="longbox {$service->css}">{$service->name}</span>
			<span class="action actionbox">
				<a href="{$smarty.const.URL}/index.php/services?service=nagios&amp;modify={$service->idService}#edit" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" />
				</a>
				<a href="{$smarty.const.URL}/index.php/services?service=nagios&amp;delete={$service->idService}" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" />
				</a>
			</span>
		</p>
	</li>
{/foreach}
</ul>
{/if}

</div>




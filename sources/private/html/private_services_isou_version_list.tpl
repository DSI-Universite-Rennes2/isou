<ul class="legend">
	<li><span class="legend unassign">&nbsp;</span>&nbsp;Service final ISOU sans dépendance avec NAGIOS&nbsp;</li>
	<li><span class="legend nagios">&nbsp;</span>&nbsp;Service NAGIOS utilisé directement dans ISOU&nbsp;</li>
	<li><span class="legend forced">&nbsp;</span>&nbsp;Service final dont l'état est bloqué par ISOU&nbsp;</li>
</ul>

<form id="addform" action="{$smarty.const.URL}/index.php/services?service=isou" method="post">
<fieldset>
<legend>Ajouter un service</legend>
<p class="header">
	<label class="addbox" for="nameForUsers">Nom du service pour les usagers</label>
	<label class="longbox" for="category">Nom de la catégorie du service</label>
</p>
<p>
	<input class="addbox" type="text" name="nameForUsers" id="nameForUsers" maxlength="64" />
	{html_options id=category name=category options=$optionCategories selected=$smarty.post.category}
	<input class="hidden" type="hidden" name="name" value="Service final" />
	<input type="submit" name="insert" value="Enregistrer" />
</p>
<p>
	<label for="comment">Remarque</label>
	<input type="text" name="comment" id="comment" maxlength="64" size="64" />
</p>
</fieldset>
</form>

<div id="list">
{foreach item=category from=$categories name=cat}
<h2>{$category->name}</h2>

<ul class="listing">
	<li>
		<p class="header">
			<label class="longbox">Nom du service pour les usagers</label>
			<label class="longbox">Etat du service</label>
			<label class="forcedbox">Etat forcé</label>
			<label class="actionbox">Actions</label>
		</p>
	</li>

	{foreach item=service from=$category->services name=serv}
	<li>

	{if (isset($smarty.get.modify) && $smarty.get.modify === $service->idService) ||
		(isset($smarty.post.idService) && $smarty.post.idService === $service->idService)}

		<form method="post" action="{$smarty.const.URL}/index.php/services?service=isou">
		<p>
			<label for="">Nom du service</label>
			<input class="longbox" type="text" name="nameForUsers" maxlength="64" value="{$service->nameForUsers}" />

		{if $service->state != 4}
			<label for="state">Etat : </label>
			{html_options id=state name=state options=$optionState selected=$service->state}

			<label for="readonly">Forcer : </label>
			{html_checkboxes labels=0 id=readonly name=readonly options=$checkboxForced selected=$service->readonly}
		<p/>
		<p>
		{/if}
			<label for="category">Catégorie</label>
			{html_options id=category name=category options=$optionCategories selected=$service->idCategory}
			<input type="submit" name="modify" value="Enregistrer" />
			<input class="hidden" type="hidden" name="idService" value="{$service->idService}" />
		</p>

		<p>
			<label for="comment">Remarque</label>
			<input type="text" name="comment" maxlength="64" size="64" value="{$service->comment}" />
		</p>

		{if $service->state === '4'}
		<p>
			<span class="italic">Note : Le service est fermé. Merci de passer par le menu "<a href="{$smarty.const.URL}/index.php/evenements" title="aller sur la page des évènements">évènement</a>" pour le réouvrir.</span>
		</p>
		{/if}

		</form>
	{else}
		<p>
			<span class="longbox {$service->css}"><a {if !empty($service->js)}class="{$js}"{/if}>{$service->nameForUsers}</a></span>
			<span class="longbox">{$service->strState}</span>
			<span class="forcedbox">{$service->forced}</span>
			<span class="actionbox">
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;modify={$service->idService}#S{$smarty.foreach.cat.index+$smarty.foreach.serv.index+1}" name="S{$smarty.foreach.cat.index+$smarty.foreach.serv.index+2}" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier" />
				</a>
				<a href="{$smarty.const.URL}/index.php/services?service=isou&amp;delete={$service->idService}#S{$smarty.foreach.cat.index+$smarty.foreach.serv.index+1}" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer" />
				</a>
			</span>
		</p>

		{if !empty($service->comment)}
		<p class="remarque">
			<span class="longbox">Remarque: {$service->comment}</span>
			<span class="longbox">&nbsp;</span>
			<span class="forcedbox">&nbsp;</span>
			<span class="actionbox">&nbsp;</span>
		</p>
		{/if}
		{$service->parents}
	{/if}

	</li>
	{/foreach}
</ul>
{/foreach}
</div>

<div id="content">
<a name="content"></a>
	<ul class="legend">
		<li><img src="{$smarty.const.URL}/images/flag_orange.gif" title="drapeau_orange"> Etat 1 est équivalent à état perturbé</li>
		<li> <img src="{$smarty.const.URL}/images/flag_red.gif" title="drapeau_rouge"> Etat 2 est équivalent à état critique</li>
	</ul>

	{$update}

	<div id="services">
	<h2>Liste des services ISOU avec leurs dépendances</h2>

	<form id="addform" action="{$smarty.const.URL}/index.php/dependances" method="post">
		<fieldset>
		<legend>Ajouter une dépendance</legend>
			<p class="pform1">
				Le service ISOU {html_options class=childService id=childService name=childService options=$optionChildService selected=$smarty.post.childService|default:''}
				passe à l'état {html_options id=newStateForChild name=newStateForChild options=$optionState selected=$smarty.post.newStateForChild|default:''}
				si le service {html_options class=parentService id=parentService name=parentService options=$optionParentService selected=$smarty.post.parentService|default:''}
				est à l'état {html_options id=stateOfParent name=stateOfParent options=$optionState selected=$smarty.post.stateOfParent|default:''}
				<input type="submit" name="insert" value="Enregistrer"></p>
			<p class="pform2">
				<label for="description">Description : </label>
				<textarea name="description" cols="100" rows="3"></textarea>
			</p>
			<p class="pform2">
				<label for="both">Appliquer les états paire à paire </label>
				<input type="checkbox" name="both" value="1" checked="1">
			</p>
		</fieldset>
	</form>


	{foreach item=service from=$services name=serv}

	<div class="service">
		{* <!-- AFFICHAGE DES SERVICES "ORANGE" --> *}
		{if count($service->dependency1) > 0}
		<dl>
		<dt>
			<img name="S{$smarty.foreach.serv.index}" class="flag" alt="Service instable ou indisponible" title="Service instable ou indisponible" src="{$smarty.const.URL}/images/flag_orange.gif" />
			{$service->nameForUsers}
		</dt>
		{foreach item=dependency from=$service->dependency1}
			{include file="private_dependencies_dd.tpl" i=$smarty.foreach.serv.index}
		{/foreach}
		</dl>
		{else}
		<p class="no-dependencies">Aucune dépendance pour l'état 1</p>
		{/if}

		{* <!-- AFFICHAGE DES SERVICES "ROUGE" --> *}
		{if count($service->dependency2) > 0}
		<dl>
		<dt>
			<img name="S{$smarty.foreach.serv.index}" class="flag" alt="Service indisponible" title="Service indisponible" src="{$smarty.const.URL}/images/flag_red.gif" />
			{$service->nameForUsers}
		</dt>
		{foreach item=dependency from=$service->dependency2}
			{include file="private_dependencies_dd.tpl" i=$smarty.foreach.serv.index}
		{/foreach}
		</dl>
		{else}
		<p class="no-dependencies">Aucune dépendance pour l'état 2</p>
		{/if}
	</div>

	{/foreach}

	<div class="spacer">&nbsp;</div>
	</div>

</div>

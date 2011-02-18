	<form id="form-edit-service" method="post" action="{$smarty.const.URL}/index.php/services?service=isou">
		<a name="edit"></a>
		<fieldset>
			<legend>Edition d'un service</legend>
			<p>
				<label class="label-form" for="category">Catégorie : </label>
				{html_options id=category name=category options=$optionCategories selected=$smarty.post.idCategory|default:$currentEdit->idCategory}
			</p>
			<p>
				<label class="label-form" for="nameForUsers">Nom du service : </label>
				<input type="text" id="nameForUsers" name="nameForUsers" size="64" maxlength="64" value="{$smarty.post.nameForUsers|default:$currentEdit->nameForUsers}" />
			</p>

			{if $currentEdit->state != 4}
			<p>
				<label class="label-form" for="state">Etat : </label>
				{html_options id=state name=state options=$optionState selected=$smarty.post.state|default:$currentEdit->state}
			</p>
			<p>
				<label class="label-form" for="readonly">Forcer : </label>
				{html_options id=readonly name=readonly options=$checkboxForced selected=$smarty.post.readonly|default:$currentEdit->readonly}
			</p>
			{else}
			<p>
				<span class="italic">Note : Le service est fermé. Merci de passer par le menu "<a href="{$smarty.const.URL}/index.php/evenements" title="aller sur la page des évènements">évènement</a>" pour le réouvrir.</span>
			</p>
			{/if}
			<p>
				<label class="label-form" for="url">URL du service</label>
				<input type="text" name="url" id="url" size="64" maxlength="64" value="{$smarty.post.url|default:$currentEdit->url}" />
			</p>
			<p>
				<input type="submit" name="modify" value="Enregistrer" />
				<input type="submit" name="cancel" value="Annuler" />
				<input class="hidden" type="hidden" name="idService" value="{$currentEdit->idService}" />
			</p>
		</fieldset>
	</form>

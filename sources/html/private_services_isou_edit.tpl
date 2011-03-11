	<form id="form-edit-service" method="post" action="{$smarty.const.URL}/index.php/services?service=isou">
		<a name="edit"></a>
		<fieldset>
			<legend>Edition d'un service</legend>
			<p>
				<label class="label-form" for="category">Nom de la catégorie du service</label>
				{html_options id=category name=category options=$optionCategories selected=$smarty.post.idCategory|default:$currentEdit->idCategory}
			</p>
			<p>
				<label class="label-form" for="nameForUsers">Nom du service pour les usagers</label>
				<input type="text" id="nameForUsers" name="nameForUsers" size="64" maxlength="64" value="{$smarty.post.nameForUsers|default:$currentEdit->nameForUsers}" />
			</p>
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

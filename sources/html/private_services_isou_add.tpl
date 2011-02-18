	{if !empty($error)}
		<p id="update">{$error}</p>
	{/if}
	<form id="form-add-service" action="{$smarty.const.URL}/index.php/services?service=isou" method="post">
		<fieldset>
			<legend>Ajouter un service</legend>
			<p>
				<label class="label-form" for="category">Nom de la catégorie du service</label>
				{html_options id=category name=category options=$optionCategories selected=$smarty.post.category|default:''}
			</p>
			<p>
				<label class="label-form" for="nameForUsers">Nom du service pour les usagers</label>
				<input type="text" name="nameForUsers" id="nameForUsers" maxlength="64" value="{$smarty.post.nameForUsers|default:''}" />
			</p>
			<p>
				<label class="label-form" for="url">URL du service</label>
				<input type="text" name="url" id="url" size="64" maxlength="64" value="{$smarty.post.url|default:''}" />
			</p>
			<p>
				<input class="hidden" type="hidden" name="name" value="Service final" />
				<input type="submit" name="insert" value="Enregistrer" />
				<input type="submit" name="cancel" value="Annuler" />
			</p>
		</fieldset>
	</form>

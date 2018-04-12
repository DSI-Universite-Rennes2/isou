<form action="{$smarty.const.URL}/index.php/categories/edit/{$category->id}" method="post">
	{if $category->id == 0}
	<h2>Ajouter une catégorie</h2>
	{else}
	<h2>Mettre à jour une catégorie</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="name">Nom de la catégorie</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="name" id="name" maxlength="32" value="{$category->name}" />
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/categories">annuler</a>
		</li>
	</ul>
</form>

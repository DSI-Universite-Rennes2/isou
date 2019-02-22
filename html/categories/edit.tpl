<form action="{$smarty.const.URL}/index.php/categories/edit/{$category->id}" class="form-horizontal" method="post">
	{if $category->id == 0}
	<h2>Ajouter une catégorie</h2>
	{else}
	<h2>Mettre à jour une catégorie</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="name">Nom de la catégorie</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="text" name="name" id="name" value="{$category->name}" />
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

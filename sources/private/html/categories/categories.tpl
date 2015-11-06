<div id="content">
<a name="content"></a>
{if isset($error)}
	<p id="update">{$error}</p>
{/if}

{* <!-- Formulaire d'insertion --> *}
	<form action="{$smarty.const.URL}/index.php/categories" method="post">
	<fieldset>
	<legend>Ajouter une catégorie</legend>
	<p>
		<label for="name" class="label">Nom de la catégorie</label>
		<input type="text" name="name" id="name" maxlength="32" />
		<input type="submit" name="insert" value="Enregistrer" />
	</p>
	</fieldset>
	</form>

{if count($categories) > 0}
<ul class="form">
{foreach item=category from=$categories}
	{if (isset($smarty.get.modify) && $smarty.get.modify == $category->idCategory) ||
			(isset($smarty.post.idCategory) && $smarty.post.idCategory == $category->idCategory)}
	<li id="selected">
		<form id="onFly" action="{$smarty.const.URL}/index.php/categories" method="post">
			<p>
				<label class="label invisible" for="changename">Nom de la catégorie</label></p>
			<p>
				<input type="text" name="name" id="changename" maxlength="32" value="{$category->name}" />
				<input type="submit" name="modify" value="Enregistrer" />
				<input type="hidden" class="hidden" name="idCategory" value="{$category->idCategory}" />
			</p>
		</form>
	</li>
	{else}
	<li>
		<span class="upndown">
		{if $category->position > 1}
			<a href="{$smarty.const.URL}/index.php/categories?id={$category->idCategory}&amp;action=up" title="Monter la catégorie"><img src="{$smarty.const.URL}/images/arrow_up.gif" alt="monter" /></a>
		{/if}

		{if $category->position < $count}
			<a href="{$smarty.const.URL}/index.php/categories?id={$category->idCategory}&amp;action=down" title="Descendre la catégorie"><img src="{$smarty.const.URL}/images/arrow_down.gif" alt="descendre" /></a>
		{/if}
		</span>

		{$category->name}

		<a href="{$smarty.const.URL}/index.php/categories?modify={$category->idCategory}" title="Modifier"><img src="{$smarty.const.URL}/images/edit.png" alt="éditer" /></a>
	</li>
	{/if}
{/foreach}


{/if}

</div>


{if $categories|count === 0}
	<p class="alert alert-danger">Vous n'avez pas encore défini de catégorie. Avant d'ajouter un service isou, vous devez <a class="text-danger" href="{$smarty.const.URL}/index.php/categories/edit/0"><strong>créer une catégorie</strong></a>.</p>
{else}
<form action="{$smarty.const.URL}/index.php/services/isou/edit/{$service->id}" class="form-horizontal" id="isou-plugin-isou-services-edit-form" method="post">
	{if empty($service->id) === true}
		<h2>Ajouter un service</h2>
	{else}
		<h2>Mettre à jour un service</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="category">Nom de la catégorie du service</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id=category name=category options=$categories selected=$service->idcategory}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="name">Nom du service</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="text" name="name" id="name" value="{$service->name}" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="url">URL du service</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="url" name="url" id="url" value="{$service->url}" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2" id="visible">Visibilité du service</dt>
			<dd class="col-sm-10">
				{html_radios class="isou-radios" aria-labelledby="visible" name="visible" options=$options_visible selected=$service->visible}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2" id="locked">Verrouillage</dt>
			<dd class="col-sm-10">
				{html_radios class="isou-radios" aria-labelledby="locked" name="locked" options=$options_locked selected=$service->locked}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2" id="state">État</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" aria-labelledby="state" name="state" options=$options_state selected=$service->state}
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li class="list-inline-item">
			<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/services/isou">annuler</a>
		</li>
	</ul>
</form>
{/if}

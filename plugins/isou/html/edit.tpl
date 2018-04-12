<form action="{$smarty.const.URL}/index.php/services/isou/edit/{$service->id}" method="post">
	{if $service->id === 0}
		<h2>Ajouter un service</h2>
	{else}
		<h2>Mettre à jour un service</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="category">Nom de la catégorie du service</label>
			</dt>
			<dd class="form-values-dd">
				{html_options id=category name=category options=$categories selected=$service->idcategory}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="name">Nom du service</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="name" id="name" maxlength="32" value="{$service->name}" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="url">URL du service</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="url" id="url" size="64" maxlength="64" value="{$service->url}" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt" id="visible">Visibilité du service</dt>
			<dd class="form-values-dd">
				{html_radios aria-labelledby="visible" name="visible" options=$options_visible selected=$service->visible}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt" id="locked">Verrouillage</dt>
			<dd class="form-values-dd">
				{html_radios aria-labelledby="locked" name="locked" options=$options_locked selected=$service->locked}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt" id="state">État</dt>
			<dd class="form-values-dd">
				{html_options aria-labelledby="state" name="state" options=$options_state selected=$service->state}
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/services/isou">annuler</a>
		</li>
	</ul>
</form>

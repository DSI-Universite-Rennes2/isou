<main role="main">
<article id="content">

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/edit/group/{$dependency_group_content->idgroup}/content/{$dependency_group_content->idservice}" method="post">
	<h2>Mettre à jour l'état d'une dépendance</h2>

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="service">Nom du service ISOU</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="service" id="service" maxlength="32" value="{$service->name}" disabled="1" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="group">Nom du groupe</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="group" id="group" maxlength="32" value="{$group->name}" disabled="1" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="backend">Nom du service</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="backend" id="backend" maxlength="32" value="{$backend->name}" disabled="1" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="servicestate">État du groupe</label>
			</dt>
			<dd class="form-values-dd">
				{html_options name="servicestate" id="servicestate" options=$options_states selected=$dependency_group_content->servicestate}
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
		</li>
	</ul>
</form>

</article>
</main>

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/{$dependency_group->id}" class="form-horizontal" method="post">
	{if $dependency_group->id == 0}
	<h2>Ajouter un groupe</h2>
	{else}
	<h2>Mettre à jour un groupe</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="service">Nom du service ISOU</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" name="service" id="service" disabled="1" readonly="1" options=$options_services selected=$service->id}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="name">Nom du groupe</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="text" name="name" id="name" value="{$dependency_group->name}" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="redundant">Groupe redondé</label>
			</dt>
			<dd class="col-sm-10">
				{html_radios class="isou-radios" name="redundant" id="redundant" options=$options_redundants selected=$dependency_group->redundant}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="groupstate">État du groupe</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" name="groupstate" id="groupstate" options=$options_states selected=$dependency_group->groupstate}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="message">Message automatique</label>
			</dt>
			<dd class="col-sm-10">
				<textarea class="form-control" name="message" id="message" cols="75" rows="10">{$dependency_group->message}</textarea>
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

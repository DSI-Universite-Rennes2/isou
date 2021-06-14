<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$dependency_group->id}/content/edit/{$dependency_group_content->id}" class="form-horizontal" method="post">
	{if empty($dependency_group_content->id) === true}
	<h2>Ajouter des dépendances</h2>
	{else}
	<h2>Mettre à jour la dépendance</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="servicename">Nom du service ISOU</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="text" name="servicename" id="servicename" value="{$service->name}" disabled="1" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="groupname">Nom du groupe</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="text" name="groupname" id="groupname" value="{$dependency_group->name}" disabled="1" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="groupstate">État du groupe lié</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" name="groupstate" id="groupstate" options=$options_states selected=$dependency_group->groupstate disabled="1"}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="services">Nom du service lié</label>
			</dt>
			<dd class="col-sm-10" id="isou-dependencies-contents-backends-dd">
				{if empty($dependency_group_content->id) === true}
				<ul class="nav nav-tabs" role="tablist">
					{foreach $options_services as $plugin => $services}
						<li role="presentation"><a href="#tab-{$plugin|lower|escape:'url'}" aria-controls="tab-{$plugin|lower|escape:'url'}" role="tab" data-toggle="tab">{$plugin}</a></li>
					{/foreach}
				</ul>
				<div class="tab-content">
					{foreach $options_services as $plugin => $services}
						<div role="tabpanel" class="tab-pane" id="tab-{$plugin|lower|escape:'url'}">
							{html_options class="form-control isou-dependencies-contents-add-services" id="services" name="services[]" multiple="1" options=$services}
						</div>
					{/foreach}
				</div>
				{else}
				{* TODO: faire la même interface pour ajouter/supprimer une dépendance *}
				{html_options class="form-control" id="services" name="services" options=$options_services selected=$dependency_group_content->idservice}
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="servicestate">État du service lié</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" name="servicestate" id="servicestate" options=$options_states selected=$dependency_group_content->servicestate}
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

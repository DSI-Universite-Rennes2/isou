<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/duplicate/{$dependency_group->id}" method="post">
	<div class="text-center alert alert-info">
		<p>Voulez-vous vraiment dupliquer le groupe "{$dependency_group->name}" ?</p>
		<div class="text-start well">
			<h2 class="isou-dependencies-group-duplicate-preview-h2">Prévisualisation</h2>
			<p>{$preview->name}</p>

			{if isset($preview->services[0]) === true}
			<ul>
			{foreach $preview->services as $service}
				<li>{$service}</li>
			{/foreach}
			</ul>
			{/if}
		</div>
		<ul class="list-inline form-submit-buttons-ul">
			<li class="list-inline-item">
				<input class="btn btn-info" type="submit" name="duplicate" value="dupliquer" />
			</li>
			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
			</li>
		</ul>
	</div>
</form>

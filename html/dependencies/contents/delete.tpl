<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$dependency_group_content->idgroup}/content/delete/{$dependency_group_content->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment retirer le service <strong>{$content->name}</strong> du groupe de dépendance de <strong>{$service->name}</strong> ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li class="list-inline-item">
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
			</li>
		</ul>
	</div>
</form>

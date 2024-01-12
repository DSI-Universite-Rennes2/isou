<form action="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment supprimer le service "{$service->name}", ses évènements et ses dépendances ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li class="list-inline-item">
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/services/isou">annuler</a>
			</li>
		</ul>
	</div>
</form>

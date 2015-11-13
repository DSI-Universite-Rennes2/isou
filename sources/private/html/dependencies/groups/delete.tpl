<main role="main">
<article id="content">

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/delete/group/{$dependency_group->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment supprimer le groupe "{$dependency_group->name}" ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li>
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li>
				<a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
			</li>
		</ul>
	</div>
</form>

</article>
</main>

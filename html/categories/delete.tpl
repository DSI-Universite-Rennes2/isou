<form action="{$smarty.const.URL}/index.php/categories/delete/{$category->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment supprimer la catégorie "{$category->name}" et ses services associés ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li class="list-inline-item">
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/categories">annuler</a>
			</li>
		</ul>
	</div>
</form>

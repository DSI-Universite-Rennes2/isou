<main role="main">
<article id="content">

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/duplicate/group/{$dependency_group->id}" method="post">
	<div class="text-center alert alert-info">
		<p>Voulez-vous vraiment dupliquer le groupe "{$dependency_group->name}" ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li>
				<input class="btn btn-info" type="submit" name="duplicate" value="dupliquer" />
			</li>
			<li>
				<a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
			</li>
		</ul>
	</div>
</form>

</article>
</main>

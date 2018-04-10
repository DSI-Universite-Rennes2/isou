<form action="{$smarty.const.URL}/index.php/evenements/{$eventtype}/delete/{$event->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment supprimer cet évènement ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li>
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li>
				<a class="btn btn-default" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">annuler</a>
			</li>
		</ul>
	</div>
</form>

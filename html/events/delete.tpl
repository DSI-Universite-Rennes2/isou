<form action="{$smarty.const.URL}/index.php/evenements/{$eventtype}/delete/{$event->id}" method="post">
	<div class="text-center alert alert-danger">
		<p>Voulez-vous vraiment supprimer l'évènement <strong>{$event->service_name}</strong> du {$event->startdate|date_format:"%a %d %B %Y %H:%M"}{if $event->enddate !== null} au {$event->enddate|date_format:"%a %d %B %Y %H:%M"}{/if} ?</p>
		<ul class="list-inline form-submit-buttons-ul">
			<li class="list-inline-item">
				<input class="btn btn-danger" type="submit" name="delete" value="supprimer" />
			</li>
			<li class="list-inline-item">
				<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">annuler</a>
			</li>
		</ul>
	</div>
</form>

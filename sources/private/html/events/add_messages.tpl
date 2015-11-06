	<form action="{$smarty.const.URL}/index.php/evenements/messages" method="post" id="form-add-info">
	<fieldset>
	<legend>Ajouter un message informatif</legend>

	<p>
		<label for="beginDateMessage" class="label">
			Date de début de l'évènement
			<span class="required" title="champs obligatoire">*</span>
			<a href="#formatDate2" class="help" title="lire l'aide pour le champs date de début">?</a>
		</label>
		<input type="text" id="beginDateMessage" name="beginDate" value="{$smarty.post.beginDate|default:''}" maxlength="16">
	</p>

	<p>
		<label for="endDateMessage" class="label">
			Date de fin de l'évènement
			<a href="#formatDate2" class="help" title="lire l'aide pour le champs date de fin">?</a>
		</label>
		<input type="text" id="endDateMessage" name="endDate" value="{$smarty.post.endDate|default:''}" maxlength="16">
	</p>

	<p>
		<label for="message" class="label">
			Message d'information (html autorisé)
			<span class="required" title="champs obligatoire">*</span>
		</label>
		<textarea id="message" name="message" cols="40" rows="6">{$smarty.post.message|default:''}</textarea>
	</p>
	<p class="info-date-format">
		<a name="formatDate2"></a>
		Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
		Exemple :<br />
		Pour le {$smarty.now|date_format:'%A %d %B %Y à %H heures et %M minutes'}, la valeur attendue est {$smarty.now|date_format:'%d/%m/%y %H:%M'}.<br /><br />
		<a class="quickaccess-form" href="#form-add-info" title="revenir au formulaire">Revenir au formulaire.</a>
	</p>
	<p>
		<input type="hidden" name="scheduled" value="4" />
		<input type="submit" name="insert" value="Enregistrer" />
		<input type="submit" name="cancel" value="Annuler">
	</p>
	</fieldset>
	</form>

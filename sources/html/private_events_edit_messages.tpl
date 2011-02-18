			<form id="form-edit" action="{$smarty.const.URL}/index.php/evenements" method="post">
			<fieldset>
			<legend>Modifier un message informatif</legend>
			<p>
				<label for="beginDateUpd" class="label">
					Date de début
					<span class="required" title="champs obligatoire">*</span>
					<a href="#formatDate4" class="help" title="lire l'aide pour le champs date de début">?</a>
				</label>
				<input type="text" name="beginDate" id="beginDateUpd" title="Format : Jour/Mois/Annee H:M" maxlength="16" value="{$currentEdit->beginDate|date_format:'%d/%m/%Y %H:%M'}" />
			</p>
			<p>
				<label for="endDateUpd" class="label">
					Date de fin
					<span class="required" title="champs obligatoire">*</span>
					<a href="#formatDate4" class="help" title="lire l'aide pour le champs date de début">?</a>
				</label>
				<input type="text" name="endDate" id="endDateUpd" title="Format : Jour Mois Annee H:M" maxlength="16" value="{$currentEdit->endDate|date_format:'%d/%m/%Y %H:%M'|default:''}" />
			</p>
			<p>
				<label for="message" class="label">Message :</label>
				<!-- <input type="text" id="message" name="message" value="{$event->shortText}" /> -->
				<textarea id="message" name="message" cols="40" rows="6">{$currentEdit->shortText}</textarea>
			</p>
			<p class="info-date-format">
				<a name="formatDate4"></a>
				Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
				Exemple :<br />
				13/09/2010 14:30 pour le lundi 13 septembre 2010 à 14 heures et 30 minutes.<br /><br />
				<a class="quickaccess-form" href="#form-edit" title="revenir au formulaire">Revenir au formulaire.</a>
			</p>
			<p>
				<input class="hidden" type="hidden" name="idEvent" value="{$currentEdit->idEvent}">
				<input type="submit" name="modify" value="Enregistrer">
				<input type="submit" name="cancel" value="Annuler">
			</p>
			</fieldset>
			</form>

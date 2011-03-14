	<form action="{$smarty.const.URL}/index.php/evenements" method="post" id="form-add-event">
	<fieldset>
	<legend>Ajouter un évènement</legend>
	<p>
		<label for="scheduled" class="label">
			Type d'opération
			<span class="required" title="champs obligatoire">*</span>
		</label>
		{html_options id=scheduled name=scheduled options=$optionScheduled selected=$smarty.post.scheduled|default:'1'}
	</p>

	<p id="p-forced">
		<label for="forced" class="label">
			Forcer à l'état du service
			<span class="info">(seulement si c'est une opération non-prévue)</span>
		</label>
		{html_options id=forced name=forced options=$optionForced selected=$smarty.post.forced|default:'0'}
	</p>

	<p>
		<label for="name" class="label">
			Service mis en maintenance
			<span class="required" title="champs obligatoire">*</span>
		</label>
		{html_options id=name name=name options=$optionNameForUsers}
	</p>

	<p>
		<label for="beginDate" class="label">
			Date de début de l'évènement
			<span class="required" title="champs obligatoire">*</span>
			<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de début">?</a>
		</label>
		<input type="text" id="beginDate" name="beginDate" value="{$smarty.post.beginDate|default:''}" maxlength="16">
	</p>

	<p>
		<label for="endDate" class="label">
			Date de fin de l'évènement
			<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de fin">?</a>
		</label>
		<input type="text" id="endDate" name="endDate" value="{$smarty.post.endDate|default:''}" maxlength="16">
	</p>

	<p id="p-period">
	<span class="label">Périodicité <span class="info">(seulement si c'est une opération régulière)</span></span>
	{html_radios  id='period' name='period' options=$period selected={$smarty.post.period|default:'daily'}}
	</p>

	<p>
	<label for="description" class="label">Raison de la maintenance</label>
	<textarea id="description" name="description" cols="40" rows="6">{$smarty.post.description|default:''}</textarea>
	</p>
	<p class="info-date-format">
		<a name="formatDate1"></a>
		Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
		Exemple :<br />
		13/09/2010 14:30 pour le lundi 13 septembre 2010 à 14 heures et 30 minutes.<br /><br />
		<a class="quickaccess-form" href="#form-add-event" title="revenir au formulaire">Revenir au formulaire.</a>
	</p>
	<p>
		<input type="submit" name="insert" value="Enregistrer" />
		<input type="submit" name="cancel" value="Annuler">
	</p>
	</fieldset>
	</form>

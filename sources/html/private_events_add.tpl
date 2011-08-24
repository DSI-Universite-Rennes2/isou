	<form action="{$smarty.const.URL}/index.php/evenements/{if $smarty.get.type == 0}nonprevus{elseif $smarty.get.type == 2}reguliers{elseif $smarty.get.type == 3}fermes{else}prevus{/if}" method="post" id="form-add-event">
	<fieldset>
	<legend>Ajouter un évènement</legend>
	<p>
		<label for="scheduled" class="label">
			Type d'opération
			<span class="required" title="champs obligatoire">*</span>
		</label>
		{html_options id=scheduled name=scheduled options=$optionScheduled selected=$smarty.post.scheduled|default:$smarty.get.type}
	</p>

	{if $smarty.get.type == 0}
	{* <!-- seulement pour les interruptions non prevues --> *}
	<p id="p-forced">
		<label for="forced" class="label">
			Forcer l'état du service
			<span class="info">(seulement si c'est une opération non-prévue)</span>
		</label>
		{html_options id=forced name=forced options=$optionForced selected=$smarty.post.forced|default:'-1'}
	</p>
	{/if}

	<p>
		<label for="name" class="label">
			Service mis en maintenance
			<span class="required" title="champs obligatoire">*</span>
		</label>
		{html_options id=name name=name options=$optionNameForUsers selected=$smarty.post.name|default:'0'}
	</p>

	<p>
		<label for="beginDate" class="label">
			{if $smarty.get.type == 0}
				Date de début
			{elseif $smarty.get.type == 2}
				Date de la prochaine opération régulière
			{elseif $smarty.get.type == 3}
				Date de la prochaine fermeture
			{else}
				Date de la prochaine maintenance
			{/if}
			<span class="required" title="champs obligatoire">*</span>
			<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de début">?</a>
		</label>
		<input type="text" id="beginDate" name="beginDate" value="{$smarty.post.beginDate|default:''}" maxlength="16">
	</p>

	<p>
		<label for="endDate" class="label">
			{if $smarty.get.type == 0}
				Date de fin de l'interruption (optionnel)
			{elseif $smarty.get.type == 2}
				Date de fin de la prochaine opération régulière
				<span class="required" title="champs obligatoire">*</span>
			{elseif $smarty.get.type == 3}
				Date de réouverture (optionnel)
			{else}
				Date de fin de la maintenance
			{/if}
			<a href="#formatDate1" class="help" title="lire l'aide pour le champs date de fin">?</a>
		</label>
		<input type="text" id="endDate" name="endDate" value="{$smarty.post.endDate|default:''}" maxlength="16">
	</p>

	{if $smarty.get.type == 2}
	{* <!-- seulement pour les interruptions régulières --> *}
	<p id="p-period">
	<span class="label">Périodicité <span class="info">(seulement si c'est une opération régulière)</span></span>
	{html_radios name='period' options=$period selected={$smarty.post.period|default:'daily'}}
	</p>
	{/if}

	<p>
	<label for="description" class="label">Raison de la maintenance</label>
	<textarea id="description" name="description" cols="40" rows="6">{$smarty.post.description|default:''}</textarea>
	</p>
	<p class="info-date-format">
		<a name="formatDate1"></a>
		Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
		Exemple :<br />
		Pour le {$smarty.now|date_format:'%A %d %B %Y à %H heures et %M minutes'}, la valeur attendue est {$smarty.now|date_format:'%d/%m/%y %H:%M'}.<br /><br />
		<a class="quickaccess-form" href="#form-add-event" title="revenir au formulaire">Revenir au formulaire.</a>
	</p>
	<p>
		<input type="submit" name="insert" value="Enregistrer" />
		<input type="submit" name="cancel" value="Annuler">
	</p>
	</fieldset>
	</form>

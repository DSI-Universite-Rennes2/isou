			<form id="form-edit" action="{$smarty.const.URL}/index.php/evenements/{if $smarty.get.type == 0}nonprevus{elseif $smarty.get.type == 2}reguliers{elseif $smarty.get.type == 3}fermes{else}prevus{/if}{if isset($smarty.get.f)}?f=1{elseif isset($smarty.get.p)}?p=1{/if}#form-edit" method="post">
			<fieldset>
			<legend>Modifier un évènement</legend>
				<p>
					<label for="name" class="label">Service :</label>
					{html_options id=name name=name options=$optionNameForUsers selected=$smarty.post.name|default:$currentEdit->idService}
				</p>
				{if isset($smarty.get.f)}
				<p id="pedit-forced">
					<label for="forced" class="label">
						Forcer l'état du service
						<span class="info">(seulement si c'est une opération non-prévue)</span>
						<span id="warning-forced"><br />Attention ! Sélectionner l'option 'État par défaut' pour un retour au <span lang="en">monitoring</span> automatique</span>
					</label>
					{if $currentEdit->readonly == 1}
					{html_options id=forced name=forced options=$optionForced selected=$smarty.post.forced|default:$currentEdit->state}
					{else}
					{html_options id=forced name=forced options=$optionForced selected=$smarty.post.forced|default:-1}
					{/if}
				</p>
				{/if}

				<p>
					<label for="beginDateUpd" class="label">
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
						<a href="#formatDate3" class="help" title="lire l'aide pour le champs date de début">?</a>
					</label>
					<input type="text" name="beginDate" id="beginDateUpd" title="Format : Jour/Mois/Annee H:M" maxlength="16" value="{$currentEdit->beginDate|date_format:'%d/%m/%Y %H:%M'}" />
				</p>

				<p>
					<label for="endDateUpd" class="label">
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
						<a href="#formatDate3" class="help" title="lire l'aide pour le champs date de début">?</a>
					</label>
					<input type="text" name="endDate" id="endDateUpd" title="Format : Jour Mois Annee H:M" maxlength="16" value="{$currentEdit->endDate|date_format:'%d/%m/%Y %H:%M'}" />
				</p>

				{if isset($smarty.get.p)}
				<p>
					<span class="label">Périodicité <span class="info">(seulement si c'est une opération régulière)</span></span>
					{html_radios name='period' options=$period selected={$smarty.post.period|default:$currentEdit->strperiod}}
				</p>
				{/if}

				<p>
					<label for="descriptionUpd" class="label">Raison de l'interruption</label>
					<textarea id="description" name="descriptionUpd" cols="40" rows="6">{$smarty.post.descriptionUpd|default:$currentEdit->description}</textarea>
				</p>
				<p class="info-date-format">
					<a name="formatDate3"></a>
					Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
					Exemple :<br />
					Pour le {$smarty.now|date_format:'%A %d %B %Y à %H heures et %M minutes'}, la valeur attendue est {$smarty.now|date_format:'%d/%m/%y %H:%M'}.<br /><br />
					<a class="quickaccess-form" href="#form-edit" title="revenir au formulaire">Revenir au formulaire.</a>
				</p>
				<p>
					<input class="hidden" type="hidden" name="idEvent" value="{$currentEdit->idEvent}" />
					<input class="hidden" type="hidden" name="idEventDescription" value="{$currentEdit->idEventDescription}" />
					<input class="hidden" type="hidden" name="scheduled" value="{$currentEdit->isScheduled}" />
					<input type="submit" name="modify" value="Enregistrer" />
					<input type="submit" name="cancel" value="Annuler" />
				</p>
			</fieldset>
			</form>


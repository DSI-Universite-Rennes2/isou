<form action="{$smarty.const.URL}/index.php/evenements/edit/{$event->id}" method="post">

	{if $event->id == 0}
	<h2>Ajouter un évènement</h2>
	{else}
	<h2>Mettre à jour un évènement</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="type">Type d'évènement</label>
			</dt>
			<dd class="form-values-dd">
				{html_options id="type" name="type" options=$options_types selected=$event->type}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="service">Nom du service</label>
			</dt>
			<dd class="form-values-dd">
				{html_options id="service" name="service" options=$options_services selected=$event->idservice}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="state">État du service</label>
			</dt>
			<dd class="form-values-dd">
				{html_options id="state" name="state" options=$options_states selected=$event->state}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="begindate">Date de début</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="begindate" id="begindate" title="Format : Jour/Mois/Annee H:M" maxlength="16" value="{$event->begindate|date_format:'%d/%m/%Y %H:%M'}" required="1" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="enddate">Date de fin</label>
			</dt>
			<dd class="form-values-dd">
				<input type="text" name="enddate" id="enddate" title="Format : Jour Mois Annee H:M" maxlength="16" value="{$event->enddate|date_format:'%d/%m/%Y %H:%M'}" />
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">Périodicité</dt>
			<dd class="form-values-dd">
				{html_radios name='period' options=$options_periods selected=$event->period}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="description">Raison de l'interruption (html autorisé)</label>
			</dt>
			<dd class="form-values-dd">
				<textarea id="description" name="description" cols="40" rows="6">{$event->description}</textarea>
			</dd>
		</div>
	</dl>

	<p class="well">Le format de date demandé est de type "DD/MM/YYYY hh:mm".<br />
Exemple :<br />Pour le {$smarty.now|date_format:'%A %d %B %Y à %H heures et %M minutes'}, la valeur attendue est {$smarty.now|date_format:'%d/%m/%Y %H:00'}.
	</p>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/evenements">annuler</a>
		</li>
	</ul>
</form>

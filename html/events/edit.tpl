<form action="{$smarty.const.URL}/index.php/evenements/{$eventtype}/edit/{$event->id}" class="form-horizontal" method="post">

	{if $event->id == 0}
	<h2>Ajouter un évènement</h2>
	{else}
	<h2>Mettre à jour un évènement</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="type">Type d'évènement</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id="type" name="type" options=$options_types selected=$event->type}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="service">Nom du service</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id="service" name="service" options=$options_services selected=$event->idservice}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="state">État du service lors de l'interruption</label>
			</dt>
			<dd class="col-sm-10">
				{html_options class="form-control" id="state" name="state" options=$options_states selected=$event->state}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="startdate">Date de début</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="date" name="startdate" id="startdate" maxlength="10" placeholder="aaaa-mm-jj" value="{$event->startdate|date_format:'%Y-%m-%d'}" required="1" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="starttime">Heure de début</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" type="time" name="starttime" id="starttime" maxlength="5" placeholder="hh:mm" value="{$event->startdate|date_format:'%H:%M'}" required="1" />
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="enddate">Date de fin</label>
			</dt>
			<dd class="col-sm-10">
				{if $event->enddate === null}
				<input class="form-control" type="date" name="enddate" id="enddate" maxlength="10" placeholder="aaaa-mm-jj" value="" />
				{else}
				<input class="form-control" type="date" name="enddate" id="enddate" maxlength="10" placeholder="aaaa-mm-jj" value="{$event->enddate|date_format:'%Y-%m-%d'}" />
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="endtime">Heure de fin</label>
			</dt>
			<dd class="col-sm-10">
				{if $event->enddate === null}
				<input class="form-control" type="time" name="endtime" id="endtime" maxlength="5" placeholder="hh:mm" value="" />
				{else}
				<input class="form-control" type="time" name="endtime" id="endtime" maxlength="5" placeholder="hh:mm" value="{$event->enddate|date_format:'%H:%M'}" />
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2" id="period">Périodicité</dt>
			<dd class="col-sm-10">
				{html_radios aria-labelledby="period" name="period" options=$options_periods selected=$event->period}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="description">Raison de l'interruption (html autorisé)</label>
			</dt>
			<dd class="col-sm-10">
				<textarea class="form-control" id="description" name="description" cols="40" rows="6">{$event->description}</textarea>
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
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">annuler</a>
		</li>
	</ul>
</form>

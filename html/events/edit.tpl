{if $options_services|count === 0}
	<p class="alert alert-danger">Vous n'avez pas encore défini de service isou. Avant d'ajouter un évènement, vous devez <a class="text-danger" href="{$smarty.const.URL}/index.php/services/isou/edit/0"><strong>créer un service isou</strong></a>.</p>
{else}
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
				{if $event->type === Event::TYPE_SCHEDULED || $event->type === Event::TYPE_UNSCHEDULED}
				{html_options class="form-control" id="type" name="type" options=$options_types selected=$event->type}
				{else}
				{html_options class="form-control" id="type" name="type" options=$options_types selected=$event->type disabled="1"}
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="service">Nom du service</label>
			</dt>
			<dd class="col-sm-10">
				<input class="form-control" id="service" list="services" name="service" required="1" type="text" value="{$options_services[$event->idservice]|default:''}" />
				<datalist id="services">
				{foreach $options_services as $idservice => $service}
					<option data-idservice="{$idservice}" value="{$service}">
				{/foreach}
				</datalist>
			</dd>
		</div>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="state">État du service lors de l'interruption</label>
			</dt>
			<dd class="col-sm-10">
			{if $event->type === Event::TYPE_CLOSED}
				{html_options class="form-control" id="state" name="state" options=$options_states selected=State::CLOSED disabled="1"}
			{else}
				{html_options class="form-control" id="state" name="state" options=$options_states selected=$event->state}
			{/if}
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
		{if $event->type === Event::TYPE_REGULAR}
		<div class="form-group">
			<dt class="col-sm-2" id="period">Périodicité</dt>
			<dd class="col-sm-10">
				{html_radios aria-labelledby="period" name="period" options=$options_periods selected=$event->period}
			</dd>
		</div>
		{/if}
		<div aria-hidden="true" class="form-group hidden" id="reuse-description-container">
			<dt class="col-sm-2">
				<label class="control-label" for="reuse-description">Descriptions précédemment utilisées</label>
			</dt>
			<dd class="col-sm-10">
				<select class="form-control alert-info" id="reuse-description"></select>
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

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li class="list-inline-item">
			<a class="btn btn-secondary" href="{$smarty.const.URL}/index.php/evenements/{$eventtype}">annuler</a>
		</li>
	</ul>
</form>
{/if}

<form action="{$smarty.const.URL}/index.php/configuration/rapport" class="form" method="post">

	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt id="report-enabled">Activer le rapport quotidien</dt>
			<dd>
				<p class="help-block small">Envoie un rapport quotidien des différents évènements de la veille.</p>
			</dd>
			<dd>
				{html_radios aria-labelledby="report-enabled" name="report_enabled" options=$options_yes_no selected=$CFG.report_enabled}
			</dd>
		</div>

		<div class="form-group">
			<dt>
				<label for="report-hour">Heure d'exécution du rapport</label>
			</dt>
			<dd>
				<input class="form-control" id="report-hour" name="report_hour" type="text" value="{$CFG.report_hour}" />
			</dd>
		</div>

		<div class="form-group">
			<dt>
				<label for="report-receiver">Destinataire du rapport</label>
			</dt>
			<dd>
				<input class="form-control" id="report-receiver" name="report_receiver" type="email" value="{$CFG.report_receiver}" />
			</dd>
		</div>

		<div class="form-group">
			<dt>
				<label for="report-sender">Expéditeur utilisé pour l'envoi du rapport</label>
			</dt>
			<dd>
				<input class="form-control" id="report-sender" name="report_sender" type="email" value="{$CFG.report_sender}" />
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

<h2>Mise à jour</h2>

<dl>
	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Numéro de version actuelle d'Isou</dt>
		<dd class="form-values-dd">{$CFG.version}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernière date de mise à jour d'Isou.</dt>
		<dd class="form-values-dd">{$CFG.last_update|date_format:'%c'}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernière vérification de mise à jour d'Isou.</dt>
		<dd class="form-values-dd">{$CFG.last_check_update|date_format:'%c'}</dd>
	</div>
</dl>

<h2>Version des bibliothèques tierces utilisées</h2>

<dl>
	<div class="form-information-dl-div">
		<dt class="form-topics-dt">HTMLPurifier</dt>
		<dd class="form-values-dd">{$HTMLPurifierVersion}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">jQuery</dt>
		<dd class="form-values-dd" id="jquery-version">Non définie</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">phpCAS</dt>
		<dd class="form-values-dd">{$phpCASVersion}</dt>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Smarty</dt>
		<dd class="form-values-dd">{$smarty.version}</dd>
	</div>
</dl>

<h2 id="cron">Crons</h2>

<dl>
	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernier lancement du cron</dt>
		<dd class="form-values-dd">{if $CFG.last_cron_update == 0}Aucune valeur{else}{$CFG.last_cron_update|date_format:'%c'}{/if}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernier lancement du cron quotidien</dt>
		<dd class="form-values-dd">{if $CFG.last_daily_cron_update == 0}Aucune valeur{else}{$CFG.last_daily_cron_update|date_format:'%c'}{/if}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernier lancement du cron hebdomadaire</dt>
		<dd class="form-values-dd">{if $CFG.last_weekly_cron_update == 0}Aucune valeur{else}{$CFG.last_weekly_cron_update|date_format:'%c'}{/if}</dd>
	</div>

	<div class="form-information-dl-div">
		<dt class="form-topics-dt">Dernier lancement du cron annuel</dt>
		<dd class="form-values-dd">{if $CFG.last_yearly_cron_update == 0}Aucune valeur{else}{$CFG.last_yearly_cron_update|date_format:'%c'}{/if}</dd>
	</div>
</dl>

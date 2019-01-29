<h2>Mise à jour</h2>

<dl>
	<div>
		<dt>Numéro de version actuelle d'Isou</dt>
		<dd>{$CFG.version}</dd>
	</div>

	<div>
		<dt>Dernière date de mise à jour d'Isou.</dt>
		<dd>{$CFG.last_update|date_format:'%c'}</dd>
	</div>

	<div>
		<dt>Dernière vérification de mise à jour d'Isou.</dt>
		<dd>{$CFG.last_check_update|date_format:'%c'}</dd>
	</div>
</dl>

<h2>Version des bibliothèques tierces utilisées</h2>

<dl>
	<div>
		<dt>HTMLPurifier</dt>
		<dd>{$HTMLPurifierVersion}</dd>
	</div>

	<div>
		<dt>phpCAS</dt>
		<dd>{$phpCASVersion}</dt>
	</div>

	<div>
		<dt>Smarty</dt>
		<dd>{$smarty.version}</dd>
	</div>
</dl>

<h2 id="cron">Crons</h2>

<dl>
	<div>
		<dt>Dernier lancement du cron</dt>
		<dd>{if $CFG.last_cron_update->getTimestamp()|date_format:'%F' === '1970-01-01'}jamais{else}{$CFG.last_cron_update->getTimestamp()|date_format:'%c'}{/if}</dd>
	</div>

	<div>
		<dt>Dernier rapport quotidien envoyé</dt>
		<dd>{if $CFG.last_daily_report->getTimestamp()|date_format:'%F' === '1970-01-01'}jamais{else}{$CFG.last_daily_report->getTimestamp()|date_format:'%c'}{/if}</dd>
	</div>
</dl>

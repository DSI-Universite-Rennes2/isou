<h2>Diagnostics</h2>

<h3>Informations</h3>
<dl class="dl-horizontal">
	<div>
		<dt style="text-align: left;">Isou</dt>
		<dd>{$CFG.version}</dd>
	</div>

	<div>
		<dt style="text-align: left;">HTMLPurifier</dt>
		<dd>{$HTMLPurifierVersion}</dd>
	</div>

	<div>
		<dt style="text-align: left;">phpCAS</dt>
		<dd>{$phpCASVersion}</dt>
	</div>

	<div>
		<dt style="text-align: left;">Smarty</dt>
		<dd>{$smarty.version}</dd>
	</div>

	<div>
		<dt style="text-align: left;">Dernière mise à jour</dt>
		<dd>{$CFG.last_update|date_format:'%c'}</dd>
	</div>

	<div>
		<dt class="text-left">Dernière vérification de mise à jour d'Isou.</dt>
		<dd>{if empty($CFG.check_updates_enabled)}désactivée{else}{$CFG.last_update_check|date_format:'%c'}{/if}</dd>
	</div>
</dl>

{foreach $errors as $type => $details}
	<h3>{$type}</h3>
	{if isset($details[0])}
	<div class="alert alert-danger">
		<ul>
			{foreach $details as $detail}
			<li>{$detail}</li>
			{/foreach}
		</ul>
	</div>
	{else}
		<p class="alert alert-info">Aucune erreur détectée.</p>
	{/if}
{/foreach}

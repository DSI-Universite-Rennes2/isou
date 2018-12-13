<form method="post" action="{$smarty.const.URL}/index.php/configuration/monitoring/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		<dl>
			<div class="form-group">
				<dt class="form-topics-dt" id="plugin-isou-enable">Activer</dt>
				<dd class="form-values-dd">
					{html_radios aria-labelledby="plugin-isou-enable" name="plugin_isou_enable" options=$options_yes_no selected=$plugin->active disabled="1"}
				</dd>
			</div>
			<div class="form-group">
				<dt class="form-topics-dt">
					<label for="plugin-isou-tolerance" aria-describedby="plugin-isou-tolerance-aria-describedby">Tolérance d'interruption (en minutes)</label>
				</dt>
				<dd class="form-values-dd">
					<input type="number" step="1" min="0" max="10" name="plugin_isou_tolerance" id="plugin-isou-tolerance" value="{$plugin->settings->tolerance/60}" /><br />
					<span id="plugin-isou-tolerance-aria-describedby">exemple : ne pas afficher sur les pages publiques les interruptions inférieures à 2 minutes (faux positifs)</span>
				</dd>
			</div>
		</dl>
	</fieldset>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

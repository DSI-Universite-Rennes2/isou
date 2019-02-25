<dl>
	<div class="form-group">
		<dt id="plugin-view-enable">Activer</dt>
		<dd>
			{html_radios class="isou-radios" aria-labelledby="plugin-view-enable" name="plugin_view_enable" options=$options_yes_no selected=$plugin->active}
		</dd>
	</div>
	<div class="form-group">
		<dt>
			<label for="plugin-view-label">Libellé de l'onglet</label>
		</dt>
		<dd>
			<input class="form-control" id="plugin-view-label" name="plugin_view_label" type="text" value="{$plugin->settings->label}" />
		</dd>
	</div>
	<div class="form-group">
		<dt>
			<label for="plugin-view-route">URL de l'onglet</label>
		</dt>
		<dd>
			<input class="form-control" id="plugin-view-route" name="plugin_view_route" type="text" value="{$plugin->settings->route}" />
			<p class="help-block small">Exemple: {$smarty.const.URL}/index.php/<strong>{$plugin->settings->route}</strong>.</p>
		</dd>
	</div>
</dl>

<form method="post" action="{$smarty.const.URL}/index.php/configuration/apparence/{$plugin->codename}">

	<p class="alert alert-warning">Cette vue est en phase de test et n'est pas totalement finalisée.</p>

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		{include file="views/common.tpl"}

		<ul class="list-inline">
			<li>
				<input class="btn btn-primary" type="submit" value="enregistrer" />
			</li>
		</ul>
	</fieldset>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration/apparence/{$plugin->codename}">

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

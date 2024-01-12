<form method="post" action="{$smarty.const.URL}/index.php/configuration/apparence/{$plugin->codename}">

	{include file="common/messages_form.tpl"}

	<fieldset>
		<legend>{$plugin->name}</legend>

		{include file="views/common.tpl"}

		<p>
			<label for="message">Contenu de l'annonce (html autorisé) :</label><br />
			<textarea id="message" name="message" cols="100" rows="10">{$plugin->settings->message}</textarea>
		</p>

		<ul class="list-inline">
			<li class="list-inline-item">
				<input class="btn btn-primary" type="submit" value="enregistrer" />
			</li>
		</ul>
	</fieldset>
</form>

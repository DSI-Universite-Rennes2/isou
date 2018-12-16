<h1 class="sr-only">Contact</h1>

{if isset($USER->admin, $smarty.get.edit) === true && empty($USER->admin) === false}
	<form id="contact" action="{$smarty.const.URL}/index.php/contact/edit#contact" method="post">
		{include file="common/messages_form.tpl"}
		<p>
			<label for="message">Contenu de l'annonce (html autorisé) :</label><br />
			<textarea id="message" name="message" cols="100" rows="10">{$message}</textarea>
		</p>
		<p>
			<input class="btn btn-default" type="submit" name="submit" />
			<a class="btn btn-link" href="{$smarty.const.URL}/index.php/contact">annuler</a>
		</p>
	</form>
{else}
	{if isset($USER->admin) === true && empty($USER->admin) === false}
		<p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/contact/edit#contact">éditer la page</a></p>

		{include file="common/messages_session.tpl"}
	{/if}

	{$message}
{/if}

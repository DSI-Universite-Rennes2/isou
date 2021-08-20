{if $count_subscriptions === 0}

<p class="alert alert-danger">Aucun des utilisateurs du site ne s'est abonné aux notifications web. Il n'est pas possible de diffuser de message.</p>

{else}
<p class="alert alert-info">L'information sera diffusée par notification web à tous les utilisateurs abonnés au site.</p>

<form action="{$smarty.const.URL}/index.php/annonce/notification" class="form-horizontal" method="post">
	{include file="common/messages_session.tpl"}
	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt class="col-sm-2">
				<label class="control-label" for="webpush">Contenu du message</label>
			</dt>
			<dd class="col-sm-10">
				<textarea class="form-control" id="webpush" maxlength="120" name="message" cols="75" required="1" rows="10"></textarea>
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="envoyer" />
		</li>
	</ul>
</form>
{/if}

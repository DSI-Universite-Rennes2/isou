<p class="alert alert-info">L'annonce est un message qui sera affiché en bandeau sur toutes les pages publiques.</p>

<form action="{$smarty.const.URL}/index.php/annonce" class="form-horizontal" method="post">
	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt>
				<label class="control-label" for="message">Contenu de l'annonce (html autorisé)</label>
			</dt>
			<dd>
				<textarea class="form-control" id="message" name="message" cols="75" rows="10">{$announcement->message}</textarea>
			</dd>
		</div>
		<div class="form-group">
			<dt id="visible">Afficher l'annonce</dt>
			<dd>
				{html_radios class="isou-radios" aria-labelledby="visible" name="visible" options=$options_visible selected=$announcement->visible}
			</dd>
		</div>
	</dl>

	<p class="border mb-4 px-4 py-2 rounded">Modifiée par {$announcement->author}, le {$announcement->last_modification|date_format:'%A %d %b %Y %H:%M'}.</p>

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
	</ul>
</form>

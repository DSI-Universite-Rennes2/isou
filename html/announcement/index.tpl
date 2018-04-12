<h1 class="sr-only">Annonce</h1>

<p class="alert alert-info">L'annonce est un message qui sera affiché en bandeau sur toutes les pages publiques.</p>

<form action="{$smarty.const.URL}/index.php/annonce" method="post">
	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="message">Contenu de l'annonce (html autorisé)</label>
			</dt>
			<dd class="form-values-dd">
				<textarea id="message" name="message" cols="75" rows="10">{$announcement->message}</textarea>
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt" id="visible">Afficher l'annonce</dt>
			<dd class="form-values-dd">
				{html_radios aria-labelledby="visible" name="visible" options=$options_visible selected=$announcement->visible}
			</dd>
		</div>
	</dl>

		<p class="well">Modifiée par {$announcement->author}, le {$announcement->last_modification|date_format:'%A %d %b %Y %H:%M'}.</p>

	<ul class="list-inline">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/categories">annuler</a>
		</li>
	</ul>
</form>

<p class="alert alert-info">L'annonce est un message qui sera affiché en bandeau sur toutes les pages publiques.</p>

<form action="{$smarty.const.URL}/index.php/annonce" class="form-horizontal" method="post">
	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-group">
			<dt>
				<label class="control-label" for="title">Titre de l'annonce (facultatif)</label>
			</dt>
			<dd>
				<input class="form-control" id="title" name="title" value="{$announcement->title}" type="text">
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label class="control-label" for="message">Contenu de l'annonce (html autorisé)</label>
			</dt>
			<dd>
				<textarea class="form-control" id="message" name="message" cols="75" rows="10">{$announcement->message}</textarea>
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label class="control-label" for="startdate">Date de début d'affichage</label>
			</dt>
			<dd>
				{if $announcement->startdate === null}
					<input class="form-control" type="date" name="startdate" id="startdate" maxlength="10" placeholder="aaaa-mm-jj" value="">
				{else}
					<input class="form-control" type="date" name="startdate" id="startdate" maxlength="10" placeholder="aaaa-mm-jj" value="{$announcement->startdate|date_format:'%Y-%m-%d'}">
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label class="control-label" for="starttime">Heure de début d'affichage</label>
			</dt>
			<dd>
				{if $announcement->startdate === null}
					<input class="form-control" type="time" name="starttime" id="starttime" maxlength="5" placeholder="hh:mm" value="">
				{else}
					<input class="form-control" type="time" name="starttime" id="starttime" maxlength="5" placeholder="hh:mm" value="{$announcement->startdate|date_format:'%H:%M'}">
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label class="control-label" for="enddate">Date de fin d'affichage</label>
			</dt>
			<dd>
				{if $announcement->enddate === null}
				<input class="form-control" type="date" name="enddate" id="enddate" maxlength="10" placeholder="aaaa-mm-jj" value="">
				{else}
				<input class="form-control" type="date" name="enddate" id="enddate" maxlength="10" placeholder="aaaa-mm-jj" value="{$announcement->enddate|date_format:'%Y-%m-%d'}">
				{/if}
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label class="control-label" for="endtime">Heure de fin d'affichage</label>
			</dt>
			<dd>
				{if $announcement->enddate === null}
				<input class="form-control" type="time" name="endtime" id="endtime" maxlength="5" placeholder="hh:mm" value="">
				{else}
				<input class="form-control" type="time" name="endtime" id="endtime" maxlength="5" placeholder="hh:mm" value="{$announcement->enddate|date_format:'%H:%M'}">
				{/if}
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

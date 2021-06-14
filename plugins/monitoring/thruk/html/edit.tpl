<form action="{$smarty.const.URL}/index.php/services/thruk/edit/{$service->id}" class="form" method="post">
	{if empty($service->id) === true}
		<h2>Ajouter un service Thruk</h2>
	{else}
		<h2>Remplacer le service {$service->name}</h2>
	{/if}

	{include file="common/messages_form.tpl"}

	<dl>
		<dt>
			<label for="service">Service Thruk</label>
		</dt>
		<dd class="form-group">
			<input class="form-control" list="services" id="service" name="service" value="{$service->name}" />
			<datalist id="services">
			{foreach $services as $service}
				<option value="{$service}">
			{/foreach}
			</datalist>
		</dd>
	</dl>

	<ul class="list-inline">
		{if $service->id !== 0 || $smarty.post !== array()}
		<li>
			<input class="btn btn-primary" name="submit" type="submit" value="enregistrer" />
		</li>
		{/if}

		{if empty($service->id) === true}
		<li>
			<input class="btn {if $smarty.post === array()}btn-primary{else}btn-default{/if}" name="preview" type="submit" value="aperçu" />
		</li>
		{/if}

		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/services/thruk">annuler</a>
		</li>
	</ul>

	{if isset($previews[0]) === true}
	<div class="well">
		<p>{count($previews)} services seront ajoutés :</p>
		<ul>
		{foreach $previews as $preview}
			<li>{$preview}</li>
		{/foreach}
		</ul>
	</div>
	{/if}
</form>

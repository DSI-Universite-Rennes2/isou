<main role="main">
<article id="content">

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/add/group/{$dependency_group->id}/content/0" method="post">
	{include file="common/messages_form.tpl"}

	<dl>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="service">Nom du service ISOU</label>
			</dt>
			<dd class="form-values-dd">
				{html_options name="service" id="service" disabled="1" readonly="1" options=$options_services selected=$service->id}
			</dd>
		</div>
		<div class="form-information-dl-div">
			<dt class="form-topics-dt">
				<label for="servicestate">État des services</label>
			</dt>
			<dd class="form-values-dd">
				{html_options name="servicestate" id="servicestate" options=$options_states selected=$smarty.post.servicestate|default:$dependency_group->groupstate}
			</dd>
		</div>
	</dl>

	<fieldset>
		<legend>Liste des services</legend>
		{foreach $options_services as $servicetype => $options}
			<h2>{$servicetype}</h2>
			{html_checkboxes name="services" options=$options}
		{/foreach}
	</fieldset>

	<ul class="list-inline form-submit-buttons-ul">
		<li>
			<input class="btn btn-primary" type="submit" value="enregistrer" />
		</li>
		<li>
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
		</li>
	</ul>
</form>

</article>
</main>

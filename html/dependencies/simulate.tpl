<ul class="breadcrumb">
	<li class="breadcrumb-item"><a href="{$smarty.const.URL}/index.php/dependances">dépendances</a></li>
	<li class="breadcrumb-item"><a href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">{$service->name}</a></li>
	<li class="active breadcrumb-item">simuler</li>
</ul>

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/simulate" class="well" method="post">
	<h2>État des dépendances :</h2>
	{foreach $dependencies as $iddependency => $dependency}
	<div class="form-group my-3">
		<label>{html_options name="state[$iddependency]" options=$options_states selected=$smarty.post.state[$iddependency]|default:""}{$dependency}</label>
	</div>
	{/foreach}

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" name="submit" type="submit" value="simuler" />
		</li>
		<li class="list-inline-item">
			<a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
		</li>
	</ul>
</form>

{if isset($results) === true}
<div class="row">
	<h2>Résultat de la simulation :</h2>
	<div class="col-md-12">
	{if $service->state === State::OK}
	<p class="alert alert-success">{State::get_unicode_character($service->state)} {$service->name}</p>
	{elseif $service->state === State::WARNING}
	<p class="alert alert-warning">{State::get_unicode_character($service->state)} {$service->name}</p>
	{elseif $service->state === State::CRITICAL}
	<p class="alert alert-danger">{State::get_unicode_character($service->state)} {$service->name}</p>
	{/if}
	</div>

	{foreach $results as $state => $groups}
		<div class="col-md-6">
			<ul class="list-unstyled">
			{foreach $groups as $group}
				{if $group->groupstate === State::WARNING && $service->state === $group->groupstate}
				<li class="alert alert-warning">
				{elseif $group->groupstate === State::CRITICAL && $service->state === $group->groupstate}
				<li class="alert alert-danger">
				{else}
				<li class="well">
				{/if}

					<h4>{State::get_unicode_character($group->groupstate)} {$group->name}</h4>
					{if $group->redundant === "0"}
					<p class="small"><i aria-hidden="true" class="bi bi-shield-slash me-1"></i>Groupe de services non-redondés<br />Une seule anomalie dans ce groupe suffit à modifier l'état du service.</p>
					{else}
					<p class="small"><i aria-hidden="true" class="bi bi-shield-check me-1"></i>Groupe de services redondés<br />Toutes les dépendances de ce groupe doivent être en anomalie pour modifier l'état du service.</p>
					{/if}

					<ul class="list-unstyled well">
					{foreach $group->contents as $content}
						<li>{State::get_unicode_character($content->servicestate)} {$content->name}</li>
					{/foreach}
					</ul>
				</li>
			{/foreach}
			</ul>
		</div>
	{/foreach}
</div>
{/if}

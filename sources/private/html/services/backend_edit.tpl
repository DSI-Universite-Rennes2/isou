{if isset($smarty.session.cached_search_services[$backend->url]) && $smarty.session.cached_search_services[$backend->url] === NULL}

<p>Fichier {$backend->name} non accessible. Vérifier le chemin dans la <a href="{$smarty.const.URL}/index.php/configuration/avancee#form-nagios-statusdat">page de configuration</a>.</p>

{else}

<form method="post" action="{$smarty.const.URL}/index.php/services/{$backend->url}/edit/{$service->id}">
	<h2>Rechercher un service {$backend->name}</h2>

	{if isset($smarty.post.search)}
		{include file="common/messages_form.tpl"}
	{/if}

	<dl>
		<div>
			<dt><label for="search">Nom du service {$backend->name}</label></dt>
			<dd><input type="search" id="search" name="search" value="{$smarty.session.cached_search_term}" /></dd>
		</div>
	</dl>

	<p>
		<input class="btn btn-primary" type="submit" value="Rechercher" />
		<a href="{$smarty.const.URL}/index.php/services/{$backend->url}">annuler</a>
	</p>
</form>

{if isset($smarty.session.cached_search_services[$backend->url])}
	{if $smarty.session.cached_search_services[$backend->url] === false}
		<h2>Services {$backend->name} trouvés</h2>
		<p class="alert alert-danger">Trop de services à afficher. Affinez votre recherche.</p>
	{else if count($smarty.session.cached_search_services[$backend->url]) === 0}
		<h2>Services {$backend->name} trouvés</h2>
		<p class="alert alert-info">Aucun service trouvé.</p>
	{else}
		<form method="post" action="{$smarty.const.URL}/index.php/services/{$backend->url}/edit/{$service->id}">
			<h2>Services {$backend->name} trouvés</h2>

			{if !isset($smarty.post.search)}
				{include file="common/messages_form.tpl"}
			{/if}

			<fieldset>
			{if $service->id === 0}
				<legend>Cocher les services {$backend->name} à ajouter à Isou</legend>
				<ul class="list-unstyled">
				{foreach $smarty.session.cached_search_services[$backend->url] as $i => $backend_service}
					<li>
						<label for="services-{$i}"><input type="checkbox" name="services[]" id="services-{$i}" value="{$backend_service->name}"{if $backend_service->disabled === true} checked="1" disabled="1"{else if in_array($backend_service->name, $smarty.post.services)} checked="1"{/if} /> {$backend_service->name}</label>
					</li>
				{/foreach}
				</ul>
			{else}
				<legend>Remplacer le service {$service->name} par un service de la liste</legend>
				<ul class="list-unstyled">
				{foreach $smarty.session.cached_search_services[$backend->url] as $i => $backend_service}
					<li>
						<label for="services-{$i}"><input type="radio" name="service" id="services-{$i}" value="{$backend_service->name}"{if $backend_service->name === $service->name} checked="1"{/if} /> {$backend_service->name}</label>
					</li>
				{/foreach}
				</ul>

			{/if}
			</fieldset>
			<p>
				<input class="btn btn-primary" type="submit" value="{if $service->id === 0}Ajouter{else}Modifier{/if}" />
				<a href="{$smarty.const.URL}/index.php/services/{$backend->url}">annuler</a>
			</p>
		<form>
	{/if}
{/if}

{/if}

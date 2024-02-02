<!DOCTYPE html>
<html data-bs-theme="auto" lang="fr">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{$TITLE}</title>

		<link rel="shortcut icon" type="image/png" href="{$smarty.const.URL}/themes/{$CFG.theme}/favicon.png" />

		{foreach $STYLES as $STYLE}
		<link href="{$STYLE->url}" type="text/css" media="{$STYLE->media}" rel="{$STYLE->rel}" />
		{/foreach}

		<link href="{$smarty.const.URL}/index.php/rss/config" title="Page d'abonnement au flux RSS d'ISOU" type="application/rss+xml" rel="alternate" />
	</head>
	<body role="document">
		<div id="isou-main-content">
			<aside id="top" class="visually-hidden">
				<h1>Liens d'accès rapide</h1>
				<ul class="list-inline">
					<li class="list-inline-item"><a href="#navigation">Aller au menu</a></li>
					<li class="list-inline-item"><a href="#main">Aller au contenu</a></li>
				</ul>
			</aside>

			<aside class="isou-top-aside text-end">
				<h1 class="visually-hidden">Authentification</h1>
				<ul id="isou-top-aside-authentication-dropdown" class="navbar-nav">
					<li class="nav-item dropdown">
						<button class="border btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{if $USER === false}Non connecté{else}{$USER}{/if}</button>
						<ul class="dropdown-menu dropdown-menu-dark">
							<li><a aria-hidden="true" class="dropdown-item" href="#" id="toggle-theme"><i aria-hidden="true" class="bi-moon-fill me-1"></i>Activer le mode sombre</a></li>
							<li><hr class="dropdown-divider"></li>
							{if $CFG.ical_enabled === '1'}
							<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-ical" href="#"><i aria-hidden="true" class="bi-calendar-date-fill me-1"></i>Importer le flux iCal</a></li>
							<li><hr class="dropdown-divider"></li>
							{/if}
							{if $CFG.rss_enabled === '1'}
							<li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-rss" href="#"><i aria-hidden="true" class="bi-rss-fill me-1"></i>Suivre le flux RSS</a></li>
							<li><hr class="dropdown-divider"></li>
							{/if}
							{if $CFG.notifications_enabled === '1' && $USER !== false}
							<li id="toggle-webnotification-li"><a data-bs-toggle="modal" data-bs-target="#modal-notifications" class="dropdown-item" href="#" id="toggle-webnotification"><i aria-hidden="true" class="bi-bell-fill me-1"></i>Activer les notifications web</a></li>
							<li><hr class="dropdown-divider"></li>
							{/if}
							{if $USER === false}
							<li><a class="dropdown-item" href="{$smarty.const.URL}/index.php/connexion"><i aria-hidden="true" class="bi-power me-1"></i>Se connecter</a></li>
							{else}
							<li><a class="dropdown-item" href="{$smarty.const.URL}/index.php/deconnexion"><i aria-hidden="true" class="bi-power me-1"></i>Se déconnecter</a></li>
							{/if}
						</ul>
					</li>
				</ul>

				{if $CFG.ical_enabled === '1'}
				{* Modal pour le flux iCal. *}
				<div class="modal fade in text-start hidden" id="modal-ical" tabindex="-1" role="dialog" aria-labelledby="modal-notifications-label">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Importer le flux iCal</h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
							</div>
							<div class="modal-body">
								<p>Le <em>flux iCal</em> vous permet d'ajouter à votre agenda toutes les interruptions de services prévues dans ISOU.</p>
								<p>Importer le flux iCal : <a href="{$smarty.const.URL}/isou.ics">{$smarty.const.URL}/isou.ics</a></p>
							</div>
						</div>
					</div>
				</div>
				{/if}

				{if $CFG.rss_enabled === '1'}
				{* Modal pour le flux RSS. *}
				<div class="modal fade in text-start hidden" id="modal-rss" tabindex="-1" role="dialog" aria-labelledby="modal-notifications-label">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Suivre le flux RSS</h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
							</div>
							<div class="modal-body">
								<p>Le <em>flux RSS</em> vous permet de suivre toutes les interruptions de services signalées dans ISOU.</p>
								<p>Suivre le flux RSS : <a href="{$smarty.const.URL}/rss.php">{$smarty.const.URL}/rss.php</a></p>
							</div>
						</div>
					</div>
				</div>
				{/if}

				{if $CFG.notifications_enabled === '1' && $USER !== false}
				{* Modal pour les notifications web. *}
				<div class="modal fade in text-start hidden" id="modal-notifications" tabindex="-1" role="dialog" aria-labelledby="modal-notifications-label">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header" id="modal-notifications-label">
								<h4 class="modal-title">Notifications web</h4>
								<button type="button" class="btn-close" id="modal-notifications-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
							</div>
							<div class="modal-body">
								<p>L'API de <em>Notifications Web</em> permet à une page web d'envoyer des notifications qui s'affichent hors de la page au niveau du système. Cela permet aux applications web d'envoyer des informations à un utilisateur, même si l'application est inactive.</p>
								<p>Vous recevrez une notification sur votre téléphone ou sur votre ordinateur pour chaque interruption de services.</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" id="modal-notifications-submit">Save changes</button>
							</div>
						</div>
					</div>
				</div>
				{/if}
			</aside>

			<aside class="isou-top-aside text-end">
				<h1 class="visually-hidden">Informations complémentaires sur le service</h1>
				<p class="isou-top-aside-p small">État des services actualisé toutes les minutes.</p>
			</aside>

			<div class="container">
				<header class="bg-body-secondary mb-4 p-5 page-header rounded-3" role="banner">
					<div class="container-fluid">
						<h1 class="display-5 fw-bold" id="isou-header">{$CFG.site_name}</h1>
						<p class="fs-4">{$CFG.site_header}</p>
					</div>
				</header>

				{if isset($security_local_auth) === true && $security_local_auth === true}
					<p class="alert alert-danger">Il est fortement recommandé de <strong>ne pas utiliser</strong> la <a href="{$smarty.const.URL}/index.php/configuration/authentification">méthode d'authentification locale</a> en production.</p>
				{/if}

				{if empty($MENUS->administration) === false && empty($CFG.available_update) === false}
					<p class="alert alert-info">Nouvelle mise à jour : la version {$CFG.available_update} d'Isou est disponible.</p>
				{/if}

				{if count($MENUS->public) > 1 || empty($MENUS->administration) === false}
				<nav class="mb-2" id="navigation" role="navigation">
					<h1 class="visually-hidden">Navigation</h1>

					<div id="menu" class="bg-body-secondary border container-fluid menu-div navbar navbar-expand-lg rounded">
						<ul class="menu-ul navbar-nav ps-4">
							{foreach $MENUS->public as $menu}
							<li class="menu-ul-items mx-2 nav-item">
								<a class="{if $menu->selected === true}active border-bottom {/if}menu-entries nav-link" href="{$smarty.const.URL}/index.php/{$menu->url}">{$menu->label}</a>
							</li>
							{/foreach}
						</ul>
					</div>

					{if empty($MENUS->administration) === false}
					<div id="administration-menu" class="bg-dark border border-warning-subtle container-fluid menu-div my-2 p-2 navbar navbar-dark navbar-expand-lg rounded">
						<div class="collapseuu navbar-collapse">
							<div class="d-inline navbar-header menu-title ms-auto">
								<span class="navbar-brand">Administration :</span>
							</div>
							<ul class="menu-ul navbar-nav px-4">
							{foreach $MENUS->administration as $menu}
								<li class="menu-ul-items nav-item">
									<a class="{if $menu->selected === true}active border-bottom {/if}menu-entries nav-link" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
								</li>
							{/foreach}
							</ul>
						</div>
					</div>
					{/if}

				</nav>
				{/if}

				{if isset($ANNOUNCEMENT) === true}
				<aside id="announcement" class="alert alert-warning">
						<h1 class="visually-hidden">Annonce</h1>
						{$ANNOUNCEMENT->message}
				</aside>
				{/if}

				<main role="main">
					<article id="content">
						{include file="$TEMPLATE"}
					</article>
				</main>
			</div> {* / .container *}
		</div> {* / #isou-main-content *}

		<footer class="footer small text-center" id="isou-footer" role="contentinfo">
			<p id="isou-footer-p">
				<span id="isou-footer-span">Isou {$CFG.version} - <a href="https://sourcesup.renater.fr/projects/isou/">Page officielle du projet</a></span>
			</p>
		</footer>

		<script src="{$smarty.const.URL}/scripts/darkmode.js?v={$CFG.version}" type="text/javascript"></script>

		{foreach $SCRIPTS as $SCRIPT}
			<script src="{$SCRIPT->src}" type="{$SCRIPT->type}"></script>
		{/foreach}

		{if $CFG.notifications_enabled === '1'}
			<script src="{$smarty.const.URL}/scripts/notifications.js?v={$CFG.version}" type="text/javascript"></script>
		{/if}

		{if isset($ADDITIONAL_CONTENT) === true}
			{$ADDITIONAL_CONTENT}
		{/if}
	</body>
</html>

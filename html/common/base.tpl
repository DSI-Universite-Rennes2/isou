<!DOCTYPE html>
<html lang="fr">
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

		<aside id="top" class="sr-only">
			<h1>Liens d'accès rapide</h1>
			<ul class="list-inline">
				<li><a href="#navigation">Aller au menu</a></li>
				<li><a href="#main">Aller au contenu</a></li>
			</ul>
		</aside>

		<aside class="isou-top-aside text-right">
			<h1 class="sr-only">Authentification</h1>
			<p class="isou-top-aside-p">
				{if $USER === false}
					<span id="isou-top-aside-nickname-span">Non connecté</span>
					<span id="isou-top-aside-authentification-span">(<a href="{$smarty.const.URL}/index.php/connexion">connexion</a>)</span>
				{else}
					<span id="isou-top-aside-nickname-span">{$USER}</span>
					{if $CFG.notifications_enabled === '1'}
					<button class="btn btn-xs btn-default" id="isou-top-aside-notifications-button"></button>
					{/if}
					<span id="isou-top-aside-authentification-span">(<a href="{$smarty.const.URL}/index.php/deconnexion">déconnexion</a>)</span>
				{/if}
			</p>

			{if $CFG.notifications_enabled === '1'}
			<div class="modal fade in text-left hidden" id="modal-notifications" tabindex="-1" role="dialog" aria-labelledby="modal-notifications-label">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header" id="modal-notifications-label">
							<button type="button" class="close" id="modal-notifications-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Notifications web</h4>
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
			<div class="modal-backdrop fade in hidden" id="modal-backdrop"></div>
			{/if}
		</aside>

		<aside class="isou-top-aside text-right">
			<h1 class="sr-only">Informations complémentaires sur le service</h1>
			<p class="isou-top-aside-p small">État des services actualisé toutes les minutes.</p>
		</aside>

		<div class="container">
			<header class="page-header" role="banner">
				<div class="jumbotron">
					<h1 id="isou-header">{$CFG.site_name}</h1>
					<p>{$CFG.site_header}</p>
				</div>
			</header>

			{if isset($security_local_auth) === true && $security_local_auth === true}
				<p class="alert alert-danger">Il est fortement recommandé de <strong>ne pas utiliser</strong> la <a href="{$smarty.const.URL}/index.php/configuration/authentification">méthode d'authentification locale</a> en production.</p>
			{/if}

			{if count($MENUS->public) > 1 || empty($MENUS->administration) === false}
			<nav id="navigation" role="navigation">
				<h1 class="sr-only">Navigation</h1>

				<div id="menu" class="container-fluid navbar navbar-default menu-div">
					<ul class="nav navbar-nav menu-ul">
						{foreach $MENUS->public as $menu}
						<li class="menu-ul-items{if $menu->selected === true} active{/if}">
							<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}">{$menu->label}</a>
						</li>
						{/foreach}
					</ul>
				</div>

				{if empty($MENUS->administration) === false}
				<div id="administration-menu" class="container-fluid navbar navbar-inverse menu-div">
					<div class="navbar-right">
						<div class="navbar-header menu-title">
							<span class="navbar-brand">Administration :</span>
						</div>
						<ul class="nav navbar-nav menu-ul">
						{foreach $MENUS->administration as $menu}
							<li class="menu-ul-items{if $menu->selected === true} active{/if}">
								<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
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
					<h1 class="sr-only">Annonce</h1>
					{$ANNOUNCEMENT->message}
			</aside>
			{/if}

			<main role="main">
				<article id="content">
					{include file="$TEMPLATE"}
				</article>
			</main>
		</div> {* / .container *}

		<footer class="footer text-center" id="footer" role="contentinfo">
			<p id="isou-footer-p">
				<span id="isou-footer-span">Isou {$CFG.version} - <a href="https://sourcesup.renater.fr/projects/isou/">Page officielle du projet</a></span>
			</p>
		</footer>

		{foreach $SCRIPTS as $SCRIPT}
			<script src="{$SCRIPT->src}" type="{$SCRIPT->type}"></script>
		{/foreach}

		{if $CFG.notifications_enabled === '1'}
			<script src="{$smarty.const.URL}/scripts/notifications.js" type="text/javascript"></script>
		{/if}

		{if isset($ADDITIONAL_CONTENT) === true}
			{$ADDITIONAL_CONTENT}
		{/if}
	</body>
</html>

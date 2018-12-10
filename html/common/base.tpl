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
				{if isset($smarty.session.FIRSTNAME) === true || isset($smarty.session.phpCAS.user) === true}
					{if isset($smarty.session.FIRSTNAME) === true}
						<span>{$smarty.session.FIRSTNAME} {$smarty.session.LASTNAME} (<a href="{$smarty.const.URL}/index.php/deconnexion">déconnexion</a>)</span>
					{else}
						<span>{$smarty.session.phpCAS.user} (<a href="{$smarty.const.URL}/index.php/deconnexion">déconnexion</a>)</span>
					{/if}
				{else}
					<span>Non connecté (<a href="{$smarty.const.URL}/index.php/connexion">connexion</a>)</span>
				{/if}
			</p>
		</aside>

		<aside class="isou-top-aside text-right">
			<h1 class="sr-only">Informations complémentaires sur le service</h1>
			<p class="isou-top-aside-p"><span>Service mis à jour automatiquement toutes les minutes. Heure actuelle : {$smarty.now|date_format:"%H:%M:%S"}</span></p>
		</aside>

		<div class="container">
			<header class="page-header" role="banner">
				<h1 id="isou-header">{$CFG.site_header}</h1>
			</header>

			{if count($MENU) > 1 || isset($ADMINISTRATION_MENU) === true}
			<nav id="navigation" role="navigation">
				<h1 class="sr-only">Navigation</h1>

				{if count($MENU) > 1}
				<div id="menu" class="container-fluid navbar navbar-default menu-div">
					<ul class="nav navbar-nav menu-ul">
						{foreach $MENU as $menu}
						<li class="menu-ul-items{if $menu->selected === true} active{/if}">
							<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
						</li>
						{/foreach}
					</ul>
				</div>
				{/if}

				{if isset($ADMINISTRATION_MENU) === true}
				<div id="administration-menu" class="container-fluid navbar navbar-inverse menu-div">
					<div class="navbar-right">
						<div class="navbar-header menu-title">
							<span class="navbar-brand">Administration :</span>
						</div>
						<ul class="nav navbar-nav menu-ul">
						{foreach $ADMINISTRATION_MENU as $menu}
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
			<aside id="announcement" class="alert alert-warning text-center">
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
			<p><span id="footer-span">Isou {$CFG.version} - <a href="https://sourcesup.renater.fr/projects/isounagios/" title="Accéder à la page du projet libre Isou">Page officielle du projet</a></span></p>
		</footer>

		{foreach $SCRIPTS as $SCRIPT}
			<script src="{$SCRIPT->src}" type="{$SCRIPT->type}"></script>
		{/foreach}
	</body>
</html>

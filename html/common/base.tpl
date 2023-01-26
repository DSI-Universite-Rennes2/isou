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

		<aside id="top" class="visually-hidden">
			<h1>Liens d'accès rapide</h1>
			<ul class="list-inline">
				<li class="list-inline-item"><a href="#navigation">Aller au menu</a></li>
				<li class="list-inline-item"><a href="#main">Aller au contenu</a></li>
			</ul>
		</aside>

		<aside class="isou-top-aside text-end">
			<h1 class="visually-hidden">Authentification</h1>
			<p class="isou-top-aside-p">
				{if $USER === false}
					<span id="isou-top-aside-nickname-span">Non connecté</span>
					<span id="isou-top-aside-authentification-span">(<a href="{$smarty.const.URL}/index.php/connexion">connexion</a>)</span>
				{else}
					<span id="isou-top-aside-nickname-span">{$USER}</span>
					{if $CFG.notifications_enabled === '1'}
					<button class="btn btn-sm btn-secondary" id="isou-top-aside-notifications-button"></button>
					{/if}
					<span id="isou-top-aside-authentification-span">(<a href="{$smarty.const.URL}/index.php/deconnexion">déconnexion</a>)</span>
				{/if}
			</p>

			{if $CFG.notifications_enabled === '1'}
			<div class="modal fade in text-start hidden" id="modal-notifications" tabindex="-1" role="dialog" aria-labelledby="modal-notifications-label">
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

			{if count($MENUS->public) > 1 || empty($MENUS->administration) === false}
			<nav id="navigation" role="navigation">
				<h1 class="visually-hidden">Navigation</h1>

				<div id="menu" class="bg-body-secondary border container-fluid menu-div navbar navbar-expand-lg rounded">
					<ul class="menu-ul navbar-nav ps-4">
						{foreach $MENUS->public as $menu}
						<li class="menu-ul-items mx-2 nav-item">
							<a class="{if $menu->selected === true}active {/if}menu-entries nav-link" href="{$smarty.const.URL}/index.php/{$menu->url}">{$menu->label}</a>
						</li>
						{/foreach}
					</ul>
				</div>

				{if empty($MENUS->administration) === false}
				<div id="administration-menu" class="bg-dark container-fluid menu-div my-2 p-2 navbar navbar-dark navbar-expand-lg rounded">
					<div class="collapseuu navbar-collapse">
						<div class="d-inline navbar-header menu-title ms-auto">
							<span class="navbar-brand">Administration :</span>
						</div>
						<ul class="menu-ul navbar-nav px-4">
						{foreach $MENUS->administration as $menu}
							<li class="menu-ul-items nav-item">
								<a class="{if $menu->selected === true}active {/if}menu-entries nav-link" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
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
{literal}
<script type="text/javascript">
/*!
  * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
  * Copyright 2011-2022 The Bootstrap Authors
  * Licensed under the Creative Commons Attribution 3.0 Unported License.
  */
 (() => {
   'use strict'

   const storedTheme = localStorage.getItem('theme')

   const getPreferredTheme = () => {
    if (storedTheme) {
       return storedTheme
     }
     return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
   }

  const setTheme = function (theme) {
     if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
       document.documentElement.setAttribute('data-bs-theme', 'dark')
     } else {
       document.documentElement.setAttribute('data-bs-theme', theme)
     }
  }

  setTheme(getPreferredTheme())
/*
   const showActiveTheme = theme => {
     const activeThemeIcon = document.querySelector('.theme-icon-active use')
     const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
     const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')
     document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
      element.classList.remove('active')
     })
    btnToActive.classList.add('active')
     activeThemeIcon.setAttribute('href', svgOfActiveBtn)
   }
*/
   window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
     if (storedTheme !== 'light' || storedTheme !== 'dark') {
       setTheme(getPreferredTheme())
     }
   })
/*
  window.addEventListener('DOMContentLoaded', () => {
     showActiveTheme(getPreferredTheme())
     document.querySelectorAll('[data-bs-theme-value]')
       .forEach(toggle => {
         toggle.addEventListener('click', () => {
          const theme = toggle.getAttribute('data-bs-theme-value')
           localStorage.setItem('theme', theme)
           setTheme(theme)
           showActiveTheme(theme)
         })
       })
   })
*/
 })()
</script>
{/literal}

	</body>
</html>

<aside id="top" class="pull-left">
	<h1 class="sr-only">Liens d'accès rapide</h1>
	<ul class="list-inline">
		<li><a href="#navigation">Aller au menu</a></li>
		<li><a href="#main">Aller au contenu</a></li>
	</ul>
</aside>

<aside class="isou-top-aside text-right">
	<h1 class="sr-only">Authentification</h1>
	<p class="isou-top-aside-p">
		{if isset($smarty.session.FIRSTNAME) || isset($smarty.session.phpCAS.user)}
		<span>
			{if isset($smarty.session.FIRSTNAME)}
				{$smarty.session.FIRSTNAME} {$smarty.session.LASTNAME}
			{else}
				{$smarty.session.phpCAS.user}
			{/if}
			(<a href="{$smarty.const.URL}/index.php/deconnexion">déconnexion</a>)
		</span>
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
		<h1 id="isou-header">{$smarty.const.HEADER}</h1>
	</header>

	{if count($MENU) > 1 || isset($ADMINISTRATION_MENU)}
	<nav id="navigation" role="navigation">
		<h1 class="sr-only">Navigation</h1>

		{if count($MENU) > 1}
		<div id="menu" class="container-fluid navbar navbar-default menu-div">
			<div class="navbar-header menu-title">
				<span class="navbar-brand">Consultation :</span>
			</div>
			<ul class="nav navbar-nav menu-ul">
			{foreach $MENU as $menu}
			<li class="menu-ul-items{if $menu->selected === TRUE} active{/if}">
				<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
			</li>
			{/foreach}
			</ul>
		</div>
		{/if}

		{if isset($ADMINISTRATION_MENU)}
		<div id="administration-menu" class="container-fluid navbar navbar-inverse menu-div">
			<div class="navbar-right">
				<div class="navbar-header menu-title">
					<span class="navbar-brand">Administration :</span>
				</div>

				<ul class="nav navbar-nav menu-ul">
				<li class="menu-ul-items">
					{* ADMINISTRATION GENERALE *}
					<div class="navbar-header menu-title">
						<span class="navbar-brand">Générale</span>
					</div>
					<ul class="nav navbar-nav menu-ul">
					{foreach $ADMINISTRATION_MENU as $menu}
						{if $menu->idsubmenu === '1'}
						<li class="menu-ul-items{if $menu->selected === TRUE} active{/if}">
							<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
						</li>
						{/if}
					{/foreach}
					</ul>
				</li>
				<li class="menu-ul-items">
					{* ADMINISTRATION AVANCEE *}
					<div class="navbar-header menu-title">
						<span class="navbar-brand">Avancée</span>
					</div>
					<ul class="nav navbar-nav menu-ul">
					{foreach $ADMINISTRATION_MENU as $menu}
						{if $menu->idsubmenu === '2'}
						<li class="menu-ul-items{if $menu->selected === TRUE} active{/if}">
							<a class="menu-entries" href="{$smarty.const.URL}/index.php/{$menu->url}" title="{$menu->title}">{$menu->label}</a>
						</li>
						{/if}
					{/foreach}
					</ul>
				</li>
				</ul>
			</div>
		</div>
		{/if}
	</nav>
	{/if}

	{if isset($ANNOUNCEMENT)}
	<aside class="alert alert-warning text-center">
		<h1 class="sr-only">Annonce</h1>
		{$ANNOUNCEMENT->message}
	</aside>
	{/if}


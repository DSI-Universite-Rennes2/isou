<div id="header">
	<a name="top"></a>
	<div id="topBorder">
		<span class="quickaccess">
			<a href="#menu">Aller au menu</a>
			<a href="#content">Aller au contenu</a>
		</span>
		{if isset($smarty.session.FIRSTNAME) || isset($smarty.session.phpCAS.user)}
		<span id="connexion">
			{if isset($smarty.session.FIRSTNAME)}
				{$smarty.session.FIRSTNAME} {$smarty.session.LASTNAME}
			{else}
				{$smarty.session.phpCAS.user}
			{/if}
			 (<a href="{$connexion_url}deconnexion">déconnexion</a>)
		</span>
		{else}
		<span id="connexion">
			Non connecté (<a href="{$connexion_url}connexion">connexion</a>)
		</span>
		{/if}
	</div>
	<div>
		<span>Service mis à jour automatiquement toutes les minutes. Heure actuelle : {$smarty.now|date_format:"%H:%M:%S"}</span>
	</div>
</div>

<h1>{$smarty.const.HEADER}</h1>

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

{if isset($annonce)}
	{$annonce}
{/if}




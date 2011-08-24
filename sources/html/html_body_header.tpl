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

<div id="menu-div">
	<p id="menu-title"><a name="menu"></a><span>Consultation :</span></p>
	<ul id="menu-ul">
		{if isset($menu['actualite'])}<li{if $page === 'actualite'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/actualite" title="Afficher par actualité">actualité</a></li>{/if}
		{if isset($menu['liste'])}<li{if $page === 'liste'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/liste" title="Afficher en liste" >liste</a></li>{/if}
		{if isset($menu['tableau'])}<li{if $page === 'tableau'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/tableau" title="Afficher en tableau" >tableau</a></li>{/if}
		{if isset($menu['journal'])}<li{if $page === 'journal'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/journal" title="Afficher le journal d'évènements" >journal</a></li>{/if}
		{if isset($menu['calendrier'])}<li{if $page === 'calendrier'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/calendrier" title="Afficher en calendrier" >calendrier</a></li>{/if}
		{if isset($menu['contact'])}<li{if $page === 'contact'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/contact" title="Nous contacter" >contact</a></li>{/if}
		<li{if $page === 'rss'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/rss/config" title="S'abonner au flux RSS" >flux RSS <img width="31px" height="11px" alt="" src="{$smarty.const.URL}/images/rss_logo.gif" /></a></li>
	</ul>
</div>

{if $is_admin === TRUE}

<div id="menu-admin-div" class="menu-admin">
	<p id="menu-admin-title"><span>Administration :</span></p>
	<ul id="menu-admin-ul">
		<li><span>Générale</span>
		<ul class="submenu-admin-ul">
			<li{if $page === 'evenements'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/evenements" title="Ajouter un évenement" >évènements</a></li>
			<li{if $page === 'annonce'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/annonce" title="Ajouter une annonce générale" >annonce</a></li>
			<li{if $page === 'statistiques'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/statistiques" title="Afficher les statistiques" >statistiques</a></li>
		</ul>
		</li>
		<li><span>Avancée</span>
		<ul class="submenu-admin-ul">
			<li{if $page === 'services'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/services" title="Ajouter un service" >services</a></li>
			<li{if $page === 'dependances'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/dependances" title="Ajouter une dépendance" >dépendances</a></li>
			<li{if $page === 'categories'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/categories" title="Ajouter une catégorie" >catégories</a></li>
			<li{if $page === 'aide'} class="selectedPage" {/if}><a href="{$smarty.const.URL}/index.php/aide" title="Consulter la documentation" >aide</a></li>
		</ul>
		</li>
	</ul>
	<div class="spacer"></div>
</div>

	<p id="menu-admin-preference" class="menu-admin">
		{if $smarty.session.hide === 0}
		<a href="{$FULLURL}?hide=1">masquer les interruptions de moins de {$smarty.const.TOLERANCE/60} minutes</a>
		{else}
		<a href="{$FULLURL}?hide=0">afficher les interruptions de moins de {$smarty.const.TOLERANCE/60} minutes</a>
		{/if}
		<a href="{$refresh_url}" title="Réactuliser les données">
			rafraîchir les données
		</a>
	</p>

{/if}

{if isset($refresh)}
	{if $refresh === TRUE}
		<p id="refresh">Les données Nagios ont été synchronisées avec ISOU</p>
	{else}
		<p id="refresh">Les données Nagios n'ont pas pu être synchronisées avec ISOU</p>
	{/if}
{/if}

{if isset($annonce)}
	{$annonce}
{/if}




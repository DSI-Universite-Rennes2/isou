<h1>statistiques des visites sur Isou</h1>
<ul>
	<li><a href="#daily">statistiques de la semaine courante</a></li>
	<li><a href="#weekly">statistiques du traffic hebdomadaire</a></li>
	<li><a href="#monthly">statistiques mensuelles</a></li>
</ul>

<h2 id="daily">statistiques des visites sur la semaine courante</h2>

{if isset($visits)}

<h3>Visites</h3>
<ul>
	<li>Visites externes : {$visits->externe}</li>
	<li>Visites UHB : {$visits->interne}</li>
	<li>Visites du CRI : {$visits->cri}</li>
	<li>Total des visites : {$visits->count}</li>
</ul>

<h3>Navigateurs</h3>
<ul>
{foreach from=$browsers item=browser}
	<li>{$browser->browser} : {$browser->total} visites</li>
{/foreach}
</ul>

<h3>Système d'exploitation</h3>
<ul>
{foreach from=$os item=o}
	<li>{$o->os} : {$o->total} visites</li>
{/foreach}
</ul>

<h3>Bots et Autres</h3>
<p>Total des visites : {$visits->bots}</p>
<ul>
{if isset($googlebot)}
	<li>googlebot : {$googlebot->total} visites</li>
{/if}
{foreach from=$bots item=bot}
	<li>{$bot->userAgent|escape:'htmlall'} : {$bot->total} visites</li>
{/foreach}
</ul>
{else}
<p>Aucune visite pour le moment.</p>
{/if}

<p><a title="Retourner au menu de la page" href="#menu"><img alt="remonter" src="{$smarty.const.URL}/images/page_up.gif"></a></p>

{* <!-- Traffic hebdomadaire --> *}
<h2 id="weekly">Traffic hebdomadaire</h2>
{if count($traffic) > 0}
<table id="table-weekly">
	<tr>
	<td></td>
	{foreach from=$weeks item=week}
		<th>{$week|date_format:'%W %Y'}</th>
	{/foreach}
	</tr>

{foreach from=$traffic key=type item=visits}
	<tr>
		<th>{$type}</th>
	{foreach from=$visits item=visit}
		<td>{$visit}</td>
	{/foreach}
	</tr>
{/foreach}
</table>
{else}
<p>Aucune donnée disponible.</p>
{/if}

<p><a title="Retourner au menu de la page" href="#menu"><img alt="remonter" src="{$smarty.const.URL}/images/page_up.gif"></a></p>

<h2 id="monthly">Traffic mensuel</h2>
{* <!-- Traffic mensuel par navigateurs --> *}
<h3>Traffic mensuel par navigateurs</h3>
{if count($browsersTraffic) > 0}
<table id="table-browsers">
	<tr>
	<td></td>
	{foreach from=$months item=month}
		<th>{$month}</th>
	{/foreach}
	</tr>
{foreach from=$browsersTraffic key=monthStr item=month}
	<tr>
		<th>{$monthStr}</th>
	{foreach from=$month item=browser}
		<td>{$browser}</td>
	{/foreach}
	</tr>
{/foreach}
</table>
{else}
<p>Aucune donnée disponible.</p>
{/if}

{* <!-- Traffic mensuel par systèmes d'exploitation --> *}
<h3>Traffic mensuel par systèmes d'exploitation</h3>
{if count($osTraffic) > 0}
<table id="table-os">
	<tr>
	<td></td>
	{foreach from=$months item=month}
		<th>{$month}</th>
	{/foreach}
	</tr>
{foreach from=$osTraffic key=monthStr item=month}
	<tr>
		<th>{$monthStr}</th>
	{foreach from=$month item=os}
		<td>{$os}</td>
	{/foreach}
	</tr>
{/foreach}
</table>
{else}
<p>Aucune donnée disponible.</p>
{/if}

<p><a title="Retourner au menu de la page" href="#menu"><img alt="remonter" src="{$smarty.const.URL}/images/page_up.gif"></a></p>


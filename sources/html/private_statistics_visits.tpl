<h1>statistiques de visites sur la semaine courante</h1>

{if isset($visits)}

<h2>Visites</h2>
<ul>
	<li>Visites externes : {$visits->externe}</li>
	<li>Visites UHB : {$visits->interne}</li>
	<li>Visites du CRI : {$visits->cri}</li>
	<li>Total des visites : {$visits->count}</li>
</ul>

<h2>Navigateurs</h2>
<ul>
{foreach from=$browsers item=browser}
	<li>{$browser->browser} : {$browser->total} visites</li>
{/foreach}
</ul>

<h2>Système d'exploitation</h2>
<ul>
{foreach from=$os item=o}
	<li>{$o->os} : {$o->total} visites</li>
{/foreach}
</ul>

<h2>Bots et Autres</h2>
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

{* <!-- Traffic hebdomadaire --> *}
<h2>Traffic hebdomadaire</h2>
{if count($traffic) > 0}
<table id="table-weekly">
	<tr>
	<th></th>
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

{* <!-- Traffic mensuel par navigateurs --> *}
<h2>Traffic mensuel par navigateurs</h2>
{if count($browsersTraffic) > 0}
<table id="table-browsers">
	<tr>
	<th></th>
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
<h2>Traffic mensuel par systèmes d'exploitation</h2>
{if count($osTraffic) > 0}
<table id="table-os">
	<tr>
	<th></th>
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

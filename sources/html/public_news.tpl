{if !isset($calendar)}
{* <!-- partie non requise lors de l'affichage du calendrier --> *}
<div id="content">
	<a name="content"></a>

	<p id="summary">Liste des interruptions passées ou à venir dans un délai de 48h.</p>

	{if count($messages) === 0 && count($categories) === 0}
		<p id="no-event">Aucun évènement à signaler</p>
	{else}
	<div id="legend">
		<h2>Légende :</h2>
		<dl>
		{foreach name=i item=flag from=$flags}
			<div class="legend-container">
				<dt><img src="{$smarty.const.URL}/images/{$flag->src}" alt="{$flag->alt}" /></dt>
				<dd>{$flag->title}</dd>
			</div>
		{/foreach}
		</dl>
	</div>
	{/if}
{/if}


{if count($messages) > 0}
	<h2>Informations :</h2>
	<ul class="service">
		{foreach name=i item=message from=$messages}
		<li>{$message->shortText|nl2br}</li>
		{/foreach}
	</ul>
	<p>
		<a class="top" href="#top" title="allez en haut de la page">
			<img src="{$smarty.const.URL}/images/page_up.gif" alt="" width="16px" height="16px" />
			Revenir en haut de la page
		</a>
	</p>
{/if}

{if count($categories) > 0}
{section name=i loop=$categories}
	<h2>{$categories[i]->name} :</h2>

	<ul class="service">
	{section name=j loop=$categories[i]->services}
		<li>
			<img src="{$smarty.const.URL}/images/{$flags.{$categories[i]->services[j]->state}->src}" alt="{$flags.{$categories[i]->services[j]->state}->alt}" />&nbsp;
			<a name="{$categories[i]->services[j]->stripName}"></a>
			{if empty($categories[i]->services[j]->url)}
			<span class="state-{$categories[i]->services[j]->state}">
				{$categories[i]->services[j]->name}
			</span>
			{else}
			<a class="state-{$categories[i]->services[j]->state}" href="{$categories[i]->services[j]->url}" title="Accéder à la page du service {$categories[i]->services[j]->name}">{$categories[i]->services[j]->name}</a>
			{/if}

			{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
			{if count($categories[i]->services[j]->parents) > 0}
			<div class="parentsList">
				{include file="public_news_recursive_parents.tpl" parents=$categories[i]->services[j]->parents}
			</div>
			{/if}
			{/if}

			{if isset($categories[i]->services[j]->events)}
				<ul class="alert">
				{section name=k loop=$categories[i]->services[j]->events}
					{* <!-- message : le service a été arrêté... --> *}
					<li>
					{if $categories[i]->services[j]->events[k]->scheduled === 3}
						{if $categories[i]->services[j]->events[k]->endDate === NULL}
							Service fermé depuis le {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y"}.
						{else}
							Service fermé depuis le {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y"}. Réouverture le {$categories[i]->services[j]->events[k]->endDate|date_format:"%A %d %B %Y"}.
						{/if}
					{else if $categories[i]->services[j]->events[k]->scheduled === 2}
						{if $categories[i]->services[j]->events[k]->period === 86400}
							Le service est en maintenance quotidienne de {$categories[i]->services[j]->events[k]->beginDate|date_format:"%H:%M"} à {$categories[i]->services[j]->events[k]->endDate|date_format:"%H:%M"}.
						{else if $categories[i]->services[j]->events[k]->period === 604800}
							Le service est en maintenance hebdomadaire de {$categories[i]->services[j]->events[k]->beginDate|date_format:"%H:%M"} à {$categories[i]->services[j]->events[k]->endDate|date_format:"%H:%M"}.
						{else}
							Le service est en maintenance de {$categories[i]->services[j]->events[k]->beginDate|date_format:"%H:%M"} à {$categories[i]->services[j]->events[k]->endDate|date_format:"%H:%M"}.
						{/if}
					{else}
						{if $categories[i]->services[j]->events[k]->endDate === NULL}
							<span class="current-event">Le service est actuellement perturbé depuis le {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y %H:%M"}.</span>
						{else}
							{if $categories[i]->services[j]->events[k]->endDate !== NULL && $categories[i]->services[j]->events[k]->endDate < $smarty.const.TIME}
								{if {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A%d%B"} === {$categories[i]->services[j]->events[k]->endDate|date_format:"%A%d%B"}}
									<span class="previous-event">Le service a été perturbé le {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y"} de {$categories[i]->services[j]->events[k]->beginDate|date_format:"%H:%M"} à {$categories[i]->services[j]->events[k]->endDate|date_format:"%H:%M"}.</span>
								{else}
									<span class="previous-event">Le service a été perturbé du {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y %H:%M"} au {$categories[i]->services[j]->events[k]->endDate|date_format:"%A %d %B %Y %H:%M"}.</span>
								{/if}
							{else}
								<span class="next-event">Le service sera perturbé du {$categories[i]->services[j]->events[k]->beginDate|date_format:"%A %d %B %Y %H:%M"} au {$categories[i]->services[j]->events[k]->endDate|date_format:"%A %d %B %Y %H:%M"}.</span>
							{/if}
						{/if}
					{/if}



					{if !empty($categories[i]->services[j]->events[k]->description)}
						<p class="reason"><span class="bold">Raison :</span> {$categories[i]->services[j]->events[k]->description|nl2br}</p>
					{/if}

					{if isset($categories[i]->services[j]->events[k]->nagios)}
					<ul>
					{section name=l loop=$categories[i]->services[j]->events[k]->nagios}
						<li>
							<img src="{$smarty.const.URL}/images/{$flags.{$categories[i]->services[j]->events[k]->nagios[l]->state}->src}" alt="{$flags.{$categories[i]->services[j]->events[k]->nagios[l]->state}->alt}" />
							{$categories[i]->services[j]->events[k]->nagios[l]->name}
							{$categories[i]->services[j]->events[k]->nagios[l]->beginDate|date_format:"%x %T"} -
							{$categories[i]->services[j]->events[k]->nagios[l]->endDate|date_format:"%x %T"}
						</li>
					{/section}
					</ul>
					{/if}
					</li>
				{/section}
				</ul>
			{/if}
		</li>
	{/section}
	</ul>

	<p>
		<a class="top" href="#top" title="allez en haut de la page">
			<img src="{$smarty.const.URL}/images/page_up.gif" alt="" width="16px" height="16px" />
			Revenir en haut de la page
		</a>
	</p>
{/section}
{/if}


{if !isset($calendar)}
{* <!-- partie non requise lors de l'affichage du calendrier --> *}
</div>

{literal}
<!--[if lt IE 8]>
<style type="text/css">
	#legend{
		display: block;
	}
</script>
<![endif]-->
{/literal}
{/if}

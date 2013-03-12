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
			<img src="{$smarty.const.URL}/images/{$flags.{$categories[i]->services[j]->getState()}->src}" alt="{$flags.{$categories[i]->services[j]->getState()}->alt}" />&nbsp;
			<a name="{$categories[i]->services[j]->stripName}"></a>
			{if $categories[i]->services[j]->getUrl() === NULL}
			<span class="state-{$categories[i]->services[j]->getState()}">
				{$categories[i]->services[j]->getNameForUsers()}
			</span>
			{else}
			<a class="state-{$categories[i]->services[j]->getState()}" href="{$categories[i]->services[j]->getUrl()}" title="Accéder à la page du service {$categories[i]->services[j]->getNameForUsers()}">{$categories[i]->services[j]->getNameForUsers()}</a>
			{/if}

			{* <!-- affichage des services parents (dépendances) --> *}
			{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
			{if count($categories[i]->services[j]->parents) > 0}
			<div class="parentsList">
				{include file="public_news_recursive_parents.tpl" parents=$categories[i]->services[j]->parents}
			</div>
			{/if}
			{/if}

			{* <!-- affichage des interruptions --> *}
			{if $categories[i]->services[j]->hasEvents()}
				<ul class="alert">
				{foreach from=$categories[i]->services[j]->getEvents() item=event}
					{* <!-- affichage des messages du type "le service a été arrêté..." --> *}
					<li>
					{if $event->getScheduled() === 3}
						{if $event->getEndDate() === NULL}
							Service fermé depuis le {$event->getBeginDate()|date_format:"%A %d %B %Y"}.
						{else}
							Service fermé depuis le {$event->getBeginDate()|date_format:"%A %d %B %Y"}. Réouverture le {$event->getEndDate()|date_format:"%A %d %B %Y"}.
						{/if}
					{else if $event->getScheduled() === 2}
						{if $event->getPeriod() === 86400}
							Le service est en maintenance quotidienne de {$event->getBeginDate()|date_format:"%H:%M"} à {$event->getEndDate()|date_format:"%H:%M"}.
						{else if $event->getPeriod() === 604800}
							Le service est en maintenance hebdomadaire de {$event->getBeginDate()|date_format:"%H:%M"} à {$event->getEndDate()|date_format:"%H:%M"}.
						{else}
							Le service est en maintenance de {$event->getBeginDate()|date_format:"%H:%M"} à {$event->getEndDate()|date_format:"%H:%M"}.
						{/if}
					{else}
						{if $event->getEndDate() === NULL}
							<span class="current-event">Le service est actuellement perturbé depuis le {$event->getBeginDate()|date_format:"%A %d %B %Y %H:%M"}.</span>
						{else}
							{if $event->getEndDate() !== NULL && $event->getEndDate() < $smarty.const.TIME|date_format:"%Y-%m-%dT%H:%M"}
								{if {$event->getBeginDate()|date_format:"%A%d%B"} === {$event->getEndDate()|date_format:"%A%d%B"}}
									<span class="previous-event">Le service a été perturbé le {$event->getBeginDate()|date_format:"%A %d %B %Y"} de {$event->getBeginDate()|date_format:"%H:%M"} à {$event->getEndDate()|date_format:"%H:%M"}.</span>
								{else}
									<span class="previous-event">Le service a été perturbé du {$event->getBeginDate()|date_format:"%A %d %B %Y %H:%M"} au {$event->getEndDate()|date_format:"%A %d %B %Y %H:%M"}.</span>
								{/if}
							{else}
								<span class="next-event">Le service sera perturbé du {$event->getBeginDate()|date_format:"%A %d %B %Y %H:%M"} au {$event->getEndDate()|date_format:"%A %d %B %Y %H:%M"}.</span>
							{/if}
						{/if}
					{/if}

					{* <!-- affichage d'une description de l'interruption ; ex: mise à jour en version 2.x" --> *}
					{if $event->getDescription() !== NULL}
						<p class="reason"><span class="bold">Raison :</span> {$event->getDescription()|nl2br}</p>
					{/if}

					{* <!-- affichage des états des services parents (dépendances) lors de l'interruption --> *}
					{if $smarty.const.DEBUG === TRUE || $is_admin === TRUE}
					{if count($event->getNagiosEvents($categories[i]->services[j]->getId())) > 0}
						{include file="public_news_recursive_nagios.tpl" parents=$event->getNagiosEvents($categories[i]->services[j]->getId())}
					{/if}
					{/if}
					</li>
				{/foreach}
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

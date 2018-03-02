<main role="main">
<article id="content">

{if count($categories) === 0}
	<p class="alert alert-info">Aucun service disponible pour le moment.</p>
{else}

<div class="sr-only">
	<h1>Liste des catégories</h1>
	<ul class="list-inline">
	{foreach $categories as $category}
		<li><a href="#{$category->name}">{$category->name}</a></li>
	{/foreach}
	</ul>
</div>

<table class="table table-bordered table-condensed" summary="Tableau des perturbations et interruptions de services">
	<caption class="text-center">Tableau des services monitorés</caption>
	<thead>
		<tr>
			<th class="col-md-1" id="lth1" rowspan="2">Service</th>
			<th class="col-md-1 text-center" id="lth2" rowspan="2">État</th>
			<th class="col-md-4 text-center" id="lth4" colspan="3">Dernière perturbation ou interruption depuis 48h</th>
			<th class="col-md-4 text-center" id="lth5" colspan="3">Prochaine interruption exceptionnelle programmée</th>
			<th class="col-md-2 text-center" id="lth6" rowspan="2">Interruptions régulières</th>
		</tr>
		<tr>
			<th class="col-md-1 text-center" id="lth41">Début</th>
			<th class="col-md-1 text-center" id="lth42">Fin</th>
			<th class="col-md-2 text-center" id="lth43">Raison</th>
			<th class="col-md-1 text-center" id="lth41">Début</th>
			<th class="col-md-1 text-center" id="lth42">Fin</th>
			<th class="col-md-2 text-center" id="lth43">Raison</th>
		</tr>
	</thead>
	<tbody>
	{foreach $categories as $category}
		<tr id="{$category->name}" class="active">
			<th colspan="10">{$category->name}</th>
		</tr>
		{foreach $category->services as $service}
		<tr>
			{if $service->url === NULL}
			<td headers="lth1" class="text-left">{$service->name}</td>
			{else}
			<td headers="lth1" class="text-left"><a href="{$service->url}" title="Accéder à la page du service {$service->name|escape:'html':'UTF-8'}">{$service->name}</a></td>
			{/if}
			<td headers="lth2">{$STATES[$service->state]}</td>
			{if $service->is_closed === TRUE}
			{* service fermé *}
			<td headers="lth4" colspan="5" class="text-center">
				<p>
				{if !empty($service->closed_event->description)}
					{$service->closed_event->description|nl2br}
				{else}
				{if $service->closed_event->enddate === NULL}
					Service fermé depuis le {$service->closed_event->begindate|date_format:"%A %e %B %Y"}.
				{else}
					Service fermé depuis le {$service->closed_event->begindate|date_format:"%A %e %B %Y"}.
					Réouverture le {$service->closed_event->enddate|date_format:"%A %e %B %Y"}.
				{/if}
				{/if}
				</p>
			</td>
			{else}

			{* dernier évènement *}
			{if $service->last_event === FALSE}
				<td headers="lth4 lth41">&nbsp;</td>
				<td headers="lth4 lth42">&nbsp;</td>
				<td headers="lth4 lth43">&nbsp;</td>
			{else}
				<td headers="lth4 lth41">{$service->last_event->begindate|date_format:"%a %d %b %Hh%M"}</td>

				{if $service->last_event->enddate === NULL}
				<td headers="lth4 lth42">indéterminé</td>
				{else}
				<td headers="lth4 lth42">{$service->last_event->enddate|date_format:"%a %d %b %Hh%M"}</td>
				{/if}

				<td headers="lth4 lth43">{$service->last_event->description}</td>
			{/if}

			{* prochain évènement prévu *}
			{if $service->next_scheduled_event === FALSE}
				<td headers="lth4 lth41">&nbsp;</td>
				<td headers="lth4 lth42">&nbsp;</td>
				<td headers="lth4 lth43">&nbsp;</td>
			{else}
				<td headers="lth5">{$service->next_scheduled_event->begindate|date_format:"%a %d %b %Hh%M"}</td>

				{if $service->next_scheduled_event->enddate === NULL}
				<td headers="lth4 lth42">indéterminé</td>
				{else}
				<td headers="lth4 lth42">{$service->next_scheduled_event->enddate|date_format:"%a %d %b %Hh%M"}</td>
				{/if}

				<td headers="lth4 lth43">{$service->next_scheduled_event->description}</td>
			{/if}

			{* interruptions régulères *}
			<td headers="lth6">
			{if isset($service->regular_events[0])}
				<ul class="list-unstyled">
				{foreach $service->regular_events as $event}
					<li>
					{if $event->period === 7*24*60*60}
						{* Tous les mois *}
						Tous les {$event->begindate|date_format:"%d"} du mois de {$event->begindate|date_format:"%Hh%M"} à {$event->enddate|date_format:"%Hh%M"}
					{else if $event->period === 7*24*60*60}
						{* Toutes les semaines *}
						Tous les {$event->begindate|date_format:"%A"} de {$event->begindate|date_format:"%Hh%M"} à {$event->enddate|date_format:"%Hh%M"}
					{else}
						{* Tous les jours *}
						Tous les jours de {$event->begindate|date_format:"%Hh%M"} à {$event->enddate|date_format:"%Hh%M"}
					{/if}

					{if !empty($event->description)}
						&nbsp;({$event->description})
					{/if}
					</li>
				{/foreach}
				</ul>
			{/if}
			</td>
			{/if}
		</tr>
		{/foreach}
	{/foreach}
	</tbody>
</table>
{/if}

</article>
</main>

<main role="main">
<article id="content">

<table id="list" class="table table-bordered" summary="Tableau des perturbations et interruptions de services Rennes 2">
	<caption class="text-center">Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1" class="thWide">Service</th>
			<th id="lth2">Actuellement</th>
			{foreach $days as $i => $day}
				{if $i === 0}
				<th id="lth3" class="thWide">Aujourd'hui</th>
				{elseif $i=== 1}
				<th id="lth3" class="thWide">Hier</th>
				{else}
				<th id="lth3" class="thWide">{$day|date_format:"%A"}</th>
				{/if}
			{/foreach}
			<th id="lth2">Taux de disponibilité</th>
		</tr>
	</thead>
	<tbody>
	{foreach $categories as $category}
		<tr class="category">
			<th colspan="10" class="active">{$category->name}</th>
		</tr>
		{foreach $category->services as $service}
		<tr>
			<td headers="lth1" class="left">{$service->name}</td>
			<td headers="lth2">{$STATES[$service->state]->get_flag_html_renderer()}</td>
			{foreach $service->availabilities as $availability}
			{if $availability > 89}
			<td headers="lth2" class="success">{$availability}%</td>
			{elseif $availability > 69}
			<td headers="lth2" class="warning">{$availability}%</td>
			{else}
			<td headers="lth2" class="danger">{$availability}%</td>
			{/if}
			{/foreach}
			</td>
			<td>{$service->availabilities_total}%</td>
		</tr>
		{/foreach}
	{/foreach}
	</tbody>
</table>

</article>
</main>

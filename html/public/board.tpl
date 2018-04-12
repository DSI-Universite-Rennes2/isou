<table id="list" class="table table-bordered table-condensed" summary="Tableau des perturbations et interruptions de services Rennes 2">
	<caption class="text-center">Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1" class="thWide">Services</th>
			<th id="lth2">États</th>
			{foreach $days as $i => $day}
				{if $i === 0}
				<th id="lth{$i+3}" class="thWide">Aujourd'hui</th>
				{elseif $i === 1}
				<th id="lth{$i+3}" class="thWide">Hier</th>
				{else}
				<th id="lth{$i+3}" class="thWide">{$day|date_format:"%a&nbsp;%e"}</th>
				{/if}
			{/foreach}
			<th id="lth10">Disponibilité</th>
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
			<td headers="lth2">{$STATES[$service->state]}</td>
			{foreach $service->availabilities as $i => $availability}
			{if $availability > 89}
			<td headers="lth{$i+3}" class="success">{$availability}%</td>
			{elseif $availability > 69}
			<td headers="lth{$i+3}" class="warning">{$availability}%</td>
			{else}
			<td headers="lth{$i+3}" class="danger">{$availability}%</td>
			{/if}
			{/foreach}
			<td headers="lth10">{$service->availabilities_total}%</td>
		</tr>
		{/foreach}
	{/foreach}
	</tbody>
</table>

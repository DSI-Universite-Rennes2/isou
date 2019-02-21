<table id="isou-board" class="table table-bordered table-condensed" summary="Tableau des perturbations et interruptions de services Rennes 2">
	<caption class="text-center">Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1">Services</th>
			<th id="lth2" class="text-center">États</th>
			{foreach $days as $i => $day}
				{if $i === 0}
				<th id="lth{$i+3}" class="text-center today">Aujourd'hui</th>
				{elseif $i === 1}
				<th id="lth{$i+3}" class="text-center">Hier</th>
				{else}
				<th id="lth{$i+3}" class="text-center">{$day|date_format:"%a&nbsp;%e"}</th>
				{/if}
			{/foreach}
			<th id="lth10" class="text-center">Disponibilité</th>
		</tr>
	</thead>
	<tbody>
	{foreach $categories as $category}
		<tr class="category">
			<th colspan="10" class="active">{$category->name}</th>
		</tr>
		{foreach $category->services as $service}
		<tr class="text-right">
			<td headers="lth1" class="text-left">{$service->name}</td>
			<td headers="lth2" class="text-right">{$STATES[$service->state]}</td>

			{foreach $service->availabilities as $i => $availability}
			{if $availability > 89}
			<td headers="lth{$i+3}" class="success{if $i === 0} today{/if}">{$availability}%</td>
			{elseif $availability > 69}
			<td headers="lth{$i+3}" class="warning{if $i === 0} today{/if}">{$availability}%</td>
			{else}
			<td headers="lth{$i+3}" class="danger{if $i === 0} today{/if}">{$availability}%</td>
			{/if}
			{/foreach}

			{if $service->availabilities_total > 89}
			<td headers="lth10" class="success">{$service->availabilities_total}%</td>
			{elseif $service->availabilities_total > 69}
			<td headers="lth10" class="warning">{$service->availabilities_total}%</td>
			{else}
			<td headers="lth10" class="danger">{$service->availabilities_total}%</td>
			{/if}
		</tr>
		{/foreach}
	{/foreach}
	</tbody>
</table>

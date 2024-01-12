<table id="isou-board" class="caption-top table table-bordered table-condensed" summary="Tableau des perturbations et interruptions de services">
	<caption class="text-center">Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1">Services</th>
			<th id="lth2" class="text-center">États</th>
			{foreach $days as $i => $day}
				{if $i === 0}
				<th id="lth{$i+3}" class="text-center bg-info-subtle">Aujourd'hui</th>
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
		<tr class="text-end">
			<td headers="lth1" class="text-start">{$service->name}</td>
			<td headers="lth2" class="text-end">{$STATES[$service->state]}</td>

			{foreach $service->availabilities as $i => $availability}
			{if $availability > 89}
			<td headers="lth{$i+3}" class="table-success text-success{if $i === 0} today{/if}">{$availability}%</td>
			{elseif $availability > 69}
			<td headers="lth{$i+3}" class="table-warning text-warning{if $i === 0} today{/if}">{$availability}%</td>
			{else}
			<td headers="lth{$i+3}" class="table-danger text-danger{if $i === 0} today{/if}">{$availability}%</td>
			{/if}
			{/foreach}

			{if $service->availabilities_total > 89}
			<td headers="lth10" class="table-success text-success">{$service->availabilities_total}%</td>
			{elseif $service->availabilities_total > 69}
			<td headers="lth10" class="table-warning text-warning">{$service->availabilities_total}%</td>
			{else}
			<td headers="lth10" class="table-danger text-danger">{$service->availabilities_total}%</td>
			{/if}
		</tr>
		{/foreach}
	{/foreach}
	</tbody>
</table>

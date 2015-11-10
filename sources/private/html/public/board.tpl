<div id="content">
<a name="content"></a>
<table id="list" summary="Tableau des perturbations et interruptions de services Rennes 2">
	<caption>Tableau des services monitorés</caption>
	<thead>
		<tr class="header">
			<th id="lth1" class="thWide">Service</th>
			<th id="lth3" class="thWide">{{{$smarty.const.TIME}-6*86400}|date_format:"%A"}</th>
			<th id="lth4" class="thWide">{{{$smarty.const.TIME}-5*86400}|date_format:"%A"}</th>
			<th id="lth5" class="thWide">{{{$smarty.const.TIME}-4*86400}|date_format:"%A"}</th>
			<th id="lth5" class="thWide">{{{$smarty.const.TIME}-3*86400}|date_format:"%A"}</th>
			<th id="lth5" class="thWide">{{{$smarty.const.TIME}-2*86400}|date_format:"%A"}</th>
			<th id="lth5" class="thWide">Hier</th>
			<th id="lth2">Aujourd'hui</th>
			<th id="lth2">Actuellement</th>
		</tr>
	</thead>
	<tbody>
	{section name=i loop=$categories}
		<tr class="category">
			<th colspan="10">{$categories[i]->name}</th>
		</tr>
		{section name=j loop=$categories[i]->services}
		<tr class="status{$categories->services[j]->trClass}">
			<td headers="lth1" class="left">{$categories[i]->services[j]->name}</td>
			<td headers="lth2">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[6]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[6]->state}->alt}" />
			</td>
			<td headers="lth2">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[5]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[5]->state}->alt}" />
			</td>
			<td headers="lth3">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[4]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[4]->state}->alt}" />
			</td>
			<td headers="lth4">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[3]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[3]->state}->alt}" />
			</td>
			<td headers="lth4">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[2]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[2]->state}->alt}" />
			</td>
			<td headers="lth4">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[1]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[1]->state}->alt}" />

			</td>
			<td headers="lth5">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->days[0]->state}->src}" alt="{$STATES.{$categories[i]->services[j]->days[0]->state}->alt}" />
			</td>
			<td headers="lth6">
				<img src="{$smarty.const.URL}/images/{$STATES.{$categories[i]->services[j]->now}->src}" alt="{$STATES.{$categories[i]->services[j]->now}->alt}" />
			</td>
		</tr>
		{/section}
	{/section}
	</tbody>
</table>

</div>

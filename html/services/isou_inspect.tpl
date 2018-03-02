<h1>{$service->name}</h1>
<p>État actuel : {$STATES[{$service->state}]}</p>
<ul>
{foreach $service->dependencies as $dependency}
	<li>{$STATES[{$dependency->state}]} {$dependency->name}</li>
{/foreach}
</ul>

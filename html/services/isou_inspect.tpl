<h1>{$service->name}</h1>
<p>État actuel : {$STATES[{$service->state}]->get_flag_html_renderer()}</p>
<ul>
{foreach $service->dependencies as $dependency}
	<li>{$STATES[{$dependency->state}]->get_flag_html_renderer()} {$dependency->name}</li>
{/foreach}
</ul>

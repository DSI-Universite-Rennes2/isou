{foreach $errors as $type => $details}
	<h2>{$type}</h2>
	{if isset($details[0])}
	<ul>
		{foreach $details as $detail}
		<li>{$detail}</li>
		{/foreach}
	</ul>
	{else}
		<p class="alert alert-info">Aucune erreur détectée.</p>
	{/if}
{/foreach}

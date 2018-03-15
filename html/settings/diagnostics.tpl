{foreach $errors as $type => $details}
	<h2>{$type}</h2>
	{if isset($details[0])}
	<div class="alert alert-danger">
	<ul>
		{foreach $details as $detail}
		<li>{$detail}</li>
		{/foreach}
	</ul>
	</div>
	{else}
		<p class="alert alert-info">Aucune erreur détectée.</p>
	{/if}
{/foreach}

{if isset($smarty.session.messages.successes[0]) === true}
<div class="alert alert-success">
	<ul class="list-unstyled">
	{foreach $smarty.session.messages.successes as $success}
		<li>{$success}</li>
	{/foreach}
	</ul>
</div>
{/if}

{if isset($smarty.session.messages.warnings[0]) === true}
<div class="alert alert-warning">
	<ul class="list-unstyled">
	{foreach $smarty.session.messages.warnings as $warning}
		<li>{$warning}</li>
	{/foreach}
	</ul>
</div>
{/if}

{if isset($smarty.session.messages.errors[0]) === true}
<div class="alert alert-danger">
	<ul class="list-unstyled">
	{foreach $smarty.session.messages.errors as $error}
		<li>{$error}</li>
	{/foreach}
	</ul>
</div>
{/if}

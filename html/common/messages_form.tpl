{if isset($smarty.post.successes[0]) === true}
<div class="alert alert-success">
	<ul class="list-unstyled">
	{foreach $smarty.post.successes as $success}
		<li>{$success}</li>
	{/foreach}
	</ul>
</div>
{/if}

{if isset($smarty.post.warnings[0]) === true}
<div class="alert alert-warning">
	<ul class="list-unstyled">
	{foreach $smarty.post.warnings as $warning}
		<li>{$warning}</li>
	{/foreach}
	</ul>
</div>
{/if}

{if isset($smarty.post.errors[0]) === true}
<div class="alert alert-danger">
	<ul class="list-unstyled">
	{foreach $smarty.post.errors as $error}
		<li>{$error}</li>
	{/foreach}
	</ul>
</div>
{/if}

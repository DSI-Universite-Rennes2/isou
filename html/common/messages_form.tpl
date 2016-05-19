{if isset($smarty.post.successes[0])}
<div class="alert alert-success">
{foreach $smarty.post.successes as $success}
    <p>{$success}</p>
{/foreach}
</div>
{/if}

{if isset($smarty.post.warnings[0])}
<div class="alert alert-warning">
{foreach $smarty.post.warnings as $warning}
    <p>{$warning}</p>
{/foreach}
</div>
{/if}

{if isset($smarty.post.errors[0])}
<div class="alert alert-danger">
{foreach $smarty.post.errors as $error}
    <p>{$error}</p>
{/foreach}
</div>
{/if}


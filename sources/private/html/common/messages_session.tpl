{if isset($smarty.session.messages.successes[0])}
<div class="alert alert-success">
{foreach $smarty.session.messages.successes as $success}
    <p>{$success}</p>
{/foreach}
</div>
{/if}

{if isset($smarty.session.messages.warnings[0])}
<div class="alert alert-warning">
{foreach $smarty.session.messages.warnings as $warning}
    <p>{$warning}</p>
{/foreach}
</div>
{/if}

{if isset($smarty.session.messages.errors[0])}
<div class="alert alert-danger">
{foreach $smarty.session.messages.errors as $error}
    <p>{$error}</p>
{/foreach}
</div>
{/if}


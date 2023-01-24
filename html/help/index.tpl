<div class="row">
	<div class="col-md-2">
		<ol class="flex-column nav nav-pills">
			{foreach $helps as $code => $item}
				<li class="nav-item"><a class="{if $item->active === true}active {/if}nav-link" href="{$smarty.const.URL}/index.php/aide/{$code}">{$item->label}</a></li>
			{/foreach}
		</ol>
	</div>

	<div class="col-md-10">
		{$help}
	</div>
</div>

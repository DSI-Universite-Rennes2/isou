<div class="row">
	<div class="col-md-2">
		<ol class="nav nav-pills nav-stacked">
			{foreach $helps as $code => $item}
				{if $item->active === true}
					<li class="active"><a href="{$smarty.const.URL}/index.php/aide/{$code}">{$item->label}</a></li>
				{else}
					<li><a href="{$smarty.const.URL}/index.php/aide/{$code}">{$item->label}</a></li>
				{/if}
			{/foreach}
		</ol>
	</div>

	<div class="col-md-10">
		{$help}
	</div>
</div>

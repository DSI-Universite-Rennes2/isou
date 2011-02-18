<ul class="listParent">
{foreach item=parent from=$parents}
	<li>
		<img src="{$smarty.const.URL}/images/{$flags.{$parent->state}->src}" alt="{$flags.{$parent->state}->alt}" />
		{if empty($parent->nameForUsers)}
			{$parent->name}
		{else}
			{$parent->nameForUsers}
		{/if}

		{if isset($parent->parents)}
			{include file="public_news_recursive_parents.tpl" parents=$parent->parents}
		{/if}
	</li>
{/foreach}
</ul>


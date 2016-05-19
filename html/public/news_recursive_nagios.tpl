<ul>
{foreach from=$parents item=nagios}
	<li>
		{if is_array($nagios)}
			{$nagios.0}
			{include file="public_news_recursive_nagios.tpl" parents=$nagios.1}
		{else}
		<img src="{$smarty.const.URL}/images/{$STATES.{$nagios->getState()}->src}" alt="{$STATES.{$nagios->getState()}->alt}" />
		{$nagios->getServiceName()}
		{$nagios->getBeginDate()|date_format:"%x %T"} -
		{$nagios->getEndDate()|date_format:"%x %T"}
		{/if}
	</li>
{/foreach}
</ul>


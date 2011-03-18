<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><![CDATA[{$smarty.const.HEADER}]]></title>
		<link>{$smarty.const.ISOU_URL}</link>
		<description>Liste des interruptions des services informatiques de l'université</description>
		<language>fr</language>
		<lastBuildDate>{$lastBuildDate} GMT</lastBuildDate>
		<atom:link href="{$RSS_URL}" rel="self" type="application/rss+xml" />
		{section name=itemIndex loop=$items}
		<item>
			<title>{$items[itemIndex][0]}</title>
			<link>{$items[itemIndex][1]}</link>
			<pubDate>{$items[itemIndex][2]} GMT</pubDate>
			<description><![CDATA[{$items[itemIndex][3]|nl2br}]]></description>
			<guid>{$items[itemIndex][1]}</guid>
		</item>
		{/section}
	</channel>
</rss>

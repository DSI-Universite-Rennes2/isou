<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title><![CDATA[{$site_header|unescape:"htmlall"}]]></title>
		<description>Liste des interruptions des services informatiques de l'université</description>
		<language>fr</language>
		<lastBuildDate>{$last_build_date} GMT</lastBuildDate>
		<link>{$smarty.const.ISOU_URL}</link>
		{foreach $items as $item}
		<item>
			<guid>{$item->guid}</guid>
			<title><![CDATA[{$item->title}]]></title>
			<description><![CDATA[{$item->description|nl2br}]]></description>
			<pubDate>{$item->pubdate} GMT</pubDate>
			<link>{$item->link}</link>
		</item>
		{/foreach}
	</channel>
</rss>

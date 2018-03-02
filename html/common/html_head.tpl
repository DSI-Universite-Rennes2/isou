<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{$TITLE}</title>

	<link rel="shortcut icon" type="image/png" href="{$smarty.const.URL}/themes/{$CFG.theme}/favicon.png" />

	{foreach $STYLES as $STYLE}
	<link href="{$STYLE->url}" type="text/css" media="{$STYLE->media}" rel="{$STYLE->rel}" />
	{/foreach}

	<link href="{$smarty.const.URL}/index.php/rss/config" title="Page d'abonnement au flux RSS d'ISOU" type="application/rss+xml" rel="alternate" />
</head>
<body role="document">

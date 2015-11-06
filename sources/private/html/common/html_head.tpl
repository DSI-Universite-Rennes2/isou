<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>{$TITLE}</title>

{foreach $STYLES as $STYLE}
	<link href="{$STYLE->url}" type="text/css" media="{$STYLE->media}" rel="{$STYLE->rel}" />
{/foreach}

<link rel="shortcut icon" href="{$smarty.const.URL}/styles/classic/favicon.ico" />

<link href="{$smarty.const.URL}/index.php/rss/config" title="Page d'abonnement au flux RSS d'ISOU" type="application/rss+xml" rel="alternate" />

</head>
<body>

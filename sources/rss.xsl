<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match='/'>
	<html>
		<xsl:apply-templates />
	</html>
</xsl:template>

<xsl:template match='channel'>
	<head>
		<title>
			<xsl:value-of select='title' />
		</title>
	</head>

	<body style="background-color:#E6DDD5;margin:0;padding:0 3em;">
		<p style="background-color:#F5F5B5;margin:0;padding:0.8em;border:1px solid #AD9A89;text-align: center;font-weight:bold;">
			Ceci est un flux RSS mis en page.
		</p>

		<div style="background-color:#FFFFFF;margin:2em auto;padding:2em;border:1px solid #AD9A89;">
		<h1 style="font-size: 1.3em;margin: 0;font-weight: bold;border-bottom: 2px solid #AD9A89;"><xsl:value-of select='title' /> </h1>
		<p style="font-size: 1.2em;margin: 0;"><xsl:value-of select='description' /> </p>
		<ul style="list-style: none;padding:0;margin:1em 0.5em;">
			<xsl:apply-templates select='item' />
		</ul>
		</div>
	</body>
</xsl:template>

<xsl:template match='item'>
	<xsl:variable name="link"><xsl:value-of select='link' /></xsl:variable>
	<xsl:variable name="pubDate"><xsl:value-of select='pubDate' /></xsl:variable>
	<xsl:variable name="description"><xsl:value-of select='description' /></xsl:variable>
	<xsl:variable name="guid"><xsl:value-of select='guid' /></xsl:variable>

	<li style="margin=2em 0em;">
		<p style="margin: 0em;font-size: 1.1em;"><a href="{$link}"><xsl:value-of select='title' /></a></p>
		<p style="margin: 0 0 1em 0;"><xsl:value-of select='pubDate' /> </p>
		<p><xsl:value-of disable-output-escaping='yes' select='description' /> </p>
	</li>
</xsl:template>

</xsl:stylesheet>

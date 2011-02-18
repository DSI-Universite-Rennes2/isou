<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
<!--<xsl:template match='/'>
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

	<body>
		<h1><xsl:value-of select='title' /> </h1>
		<p></p>
		<p>Ceci est un flux RSS mis en page.</p>

		<p><a href="http://cursus.uhb.fr/" title="Revenir sur Cursus">Retourner sur la page de Cursus</a></p>
		
		<xsl:variable name="enlace"><xsl:value-of select='link' /></xsl:variable>

		<ul>
			<xsl:apply-templates select='item' />
		</ul>

	</body>

</xsl:template>-->

<!-- <xsl:template match='item'>
	<xsl:variable name="enlace"><xsl:value-of select='link' /></xsl:variable>
	<li><a href="{$enlace}"><xsl:value-of select='title' /></a></li>
</xsl:template>-->

</xsl:stylesheet>

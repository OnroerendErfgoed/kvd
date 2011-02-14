<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:template match="error">
		<problem category="{@severity}" file="{../@name}" line="{@line}" tag="{@source}">
			<xsl:value-of select="@message" />
		</problem>
	</xsl:template>
	<xsl:template match="checkstyle">
		<report category="lint">
			<xsl:apply-templates/>
		</report>
	</xsl:template>
</xsl:stylesheet>

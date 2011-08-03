<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:rdf="http://www.w3.org/TR/REC-rdf-syntax#">
  <!-- 
    the value starts out null, and gets passed in optionally from the calling script. 
    to test without a script, use this line instead:
  <xsl:param name="schema">lter</xsl:param>
  -->

  <xsl:output method="text"/>

  <!-- 
    template to look at a set of concepts -->
  <xsl:template match="SKOSConcepts">
      <xsl:for-each select="SKOSConcept">
            <xsl:text>"</xsl:text><xsl:value-of select="rdf:RDF/rdf:Description/skos:prefLabel"/><xsl:text>",</xsl:text><xsl:text></xsl:text>
      </xsl:for-each>
  </xsl:template>
 
  <xsl:template match="comment()">
    <xsl:copy/>
  </xsl:template>
</xsl:stylesheet>

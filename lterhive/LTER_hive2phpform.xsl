<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:rdf="http://www.w3.org/TR/REC-rdf-syntax#">
  <!-- 
    the value starts out null, and gets passed in optionally from the calling script. 
    to test without a script, use this line instead:
  <xsl:param name="schema">lter</xsl:param>
  -->

  <xsl:output method="html" indent="yes" encoding="UTF-8" version="1.0"/>
  <xsl:strip-space elements="*"/>

  <!-- 
    template to look at a set of concepts -->
  <xsl:template match="SKOSConcepts">

<xsl:element name="table">
  <xsl:attribute name="border">1</xsl:attribute>
  <xsl:attribute name="cellpadding">8</xsl:attribute>
<xsl:element name="tr">
  <xsl:element name="th">Term
   </xsl:element>
  <xsl:element name="th">Select?</xsl:element>
</xsl:element>

      <xsl:for-each select="SKOSConcept">
        <xsl:element name="tr">
          <xsl:element name="td">
            <xsl:value-of select="rdf:RDF/rdf:Description/skos:prefLabel"/>
            </xsl:element>
          <xsl:element name="td">
        <xsl:element name="input">
          <xsl:attribute name="type">checkbox</xsl:attribute>
          <!-- if we knew which taxonomy the term came from, we might want to control the keywordType -->
          <xsl:attribute name="name">term[]</xsl:attribute>
          <xsl:attribute name="value"><xsl:value-of select="rdf:RDF/rdf:Description/skos:prefLabel"/>
         </xsl:attribute>
        </xsl:element>
          </xsl:element>
        </xsl:element>
      </xsl:for-each>
      <!-- there might be a better way to test if thesaurus got set, mob look it up -->
</xsl:element> 
  </xsl:template>
 
  <xsl:template match="comment()">
    <xsl:copy/>
  </xsl:template>




</xsl:stylesheet>

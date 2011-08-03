<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:rdf="http://www.w3.org/TR/REC-rdf-syntax#">
  <!-- 
    the value starts out null, and gets passed in optionally from the calling script. 
    to test without a script, use this line instead:
  <xsl:param name="schema">lter</xsl:param>
  -->
  <xsl:param name="schema"/>

  <xsl:output method="xml" indent="yes" encoding="UTF-8" version="1.0"/>
  <xsl:strip-space elements="*"/>

  <!-- 
    template to look at a set of concepts -->
  <xsl:template match="SKOSConcepts">
    <!-- 
      param sets name of thesaurus for the keywordSet-->
    <xsl:param name="thesaurus">
      <xsl:choose>
        <xsl:when test="$schema='lter'">LTER Controlled Vocabulary</xsl:when>
        <xsl:when test="$schema='nbii'">NBII</xsl:when>
        <xsl:otherwise>
          <!-- no default thesuarus -->
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <!-- 
      build the keywordSet -->
    <xsl:element name="keywordSet">
      <xsl:for-each select="SKOSConcept">
        <xsl:element name="keyword">
          <!-- if we knew which taxonomy the term came from, we might want to control the keywordType -->
          <xsl:attribute name="keywordType">theme</xsl:attribute>
          <xsl:value-of select="rdf:RDF/rdf:Description/skos:prefLabel"/>
        </xsl:element>
      </xsl:for-each>
      <!-- there might be a better way to test if thesaurus got set, mob look it up -->
      <xsl:if test="string-length($thesaurus)">
        <xsl:element name="keywordThesaurus">
          <xsl:value-of select="$thesaurus"/>
        </xsl:element>
      </xsl:if>

    </xsl:element>

  </xsl:template>

  <xsl:template match="comment()">
    <xsl:copy/>
  </xsl:template>




</xsl:stylesheet>

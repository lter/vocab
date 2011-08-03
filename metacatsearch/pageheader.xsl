<?xml version="1.0"?>
<!--
*  '$RCSfile: pageheader.xsl,v $'
*      Authors: Chris Jones
*    Copyright: 2000 Regents of the University of California and the
*         National Center for Ecological Analysis and Synthesis
*  For Details: http://www.nceas.ucsb.edu/
*
*   '$Author: cjones $'
*     '$Date: 2004/10/05 23:50:46 $'
* '$Revision: 1.1 $'
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* This is an XSLT (http://www.w3.org/TR/xslt) stylesheet designed to
* convert an XML file showing the resultset of a query
* into an HTML format suitable for rendering with modern web browsers.
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:param name="httpServer"/>

  <xsl:output method="html" encoding="iso-8859-1" indent="yes" standalone="yes"
    doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
    doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
    
  <xsl:template name="pageheader">
    <xsl:comment>begin the header area</xsl:comment>
    <xsl:comment>
      these div's must have closing elements for the css to work. Don't
      reduce them to &lt;div id="blah" /&gt; 
    </xsl:comment>
    <div id="header">
      <xsl:comment>begin the left logo area</xsl:comment>
      <div id="left_logo"></div>
      <xsl:comment>end the left logo area</xsl:comment>

      <xsl:comment>begin the banner area</xsl:comment>
      <div id="banner"></div>
      <div class="header-title">
        <xsl:text>Controlled Vocabulary Working Group</xsl:text>
      </div>
      <div class="header-subtitle">
        <a href="http://vocab.lternet.edu">Vocabulary Term Broswer -- http://vocab.lternet.edu</a>
      </div>
      <xsl:comment>end the banner area</xsl:comment>

      <xsl:comment>begin the right logo area</xsl:comment>
      <div id="right_logo1"></div>
      <div id="right_logo2"></div>
      <xsl:comment>end the right logo area</xsl:comment>
      
      <!-- these urls temporary, till we get a second url token -->
      <xsl:comment>begin the header-memu</xsl:comment>
      <div class="header-menu"> 
        <!-- <script language="JavaScript" type="text/javascript" SRC="http://sbc.lternet.edu/navigation.js"> -->
        <!-- you can remove the url if the javascript resides at root level (docs). otherwise, either
        define a javascript dir  and add it (/javascript/navigation.js) or configure with template tokens. -->
        <!--
<script language="JavaScript" type="text/javascript" SRC="/navigation.js">
                </script>
-->
	<div style="padding-right: 200px;">built by <a href="http://vcr.lternet.edu">VCR.lternet.edu</a> &amp; 
	  <a href="http://sbc.lternet.edu">SBC.lternet.edu</a></div>
      </div>                                                  
      <xsl:comment>end the header-menu</xsl:comment> 

    </div>
    <xsl:comment>end the header area</xsl:comment>
  </xsl:template>

</xsl:stylesheet>

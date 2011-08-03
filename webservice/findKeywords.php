<?php
// Generate HTML table containing suggested words for a search
// John Porter, 2011

//set baseURL to directory for querymetacat.php and LTER_resultset.xsl 
define(BASE_URL,"http://vocab.lternet.edu/metacatsearch/");

//set source for EML packages
define(EMLPackageSource,'http://knb.ecoinformatics.org/knb/metacat?action=read&qformat
=xml&docid='); 

// set the serviceURL to the LTER hive service 
define(LTER_SERVICE_URL,"http://scoria.lternet.edu:8080/lter-hive/schemes/lter/concepts/tags/SKOSFormat");
// set the serviceURL to the NBII hive service 
define(NBII_SERVICE_URL,"http://scoria.lternet.edu:8080/lter-hive/schemes/nbii/concepts/tags/SKOSFormat");


$inPath=$_SERVER['PATH_INFO'];
//echo $inPath;

$settings=explode("/",$inPath);
//print_r($settings);
$thesaurus=$settings[1];
$format=$settings[2];
$search=$settings[3];
$search=str_replace("+"," ",$search);

if ($search == ""){
documentService();
exit;
}


//Read the source EML file into a temporary file
//echo("openning:".EMLPackageSource.$search);
if($_REQUEST['docurl']!=""){
$inEMLhandle=fopen($_REQUEST['docurl'],"rb");
}else{
$inEMLhandle=fopen(EMLPackageSource.$search,"rb");
}

$inEMLstr="";
while (($inbuf=fgets($inEMLhandle)) != FALSE){
      $inEMLstr=$inEMLstr. $inbuf;
}
//echo($inEMLstr);
$tmpFileName=tempnam('/tmp',$search);
//echo("tmpfile is: ".$tmpFileName);
$tmpFileHandle=fopen($tmpFileName,"wb");
fwrite($tmpFileHandle,$inEMLstr);
fclose($tmpFileHandle);
fclose($inEMLHandle);
switch ($thesaurus){
       case "lter":
       	    $urlcall=LTER_SERVICE_URL;
	    break;
       case "LTER":
       	    $urlcall=LTER_SERVICE_URL;
	    break;
       case "nbii":
       	    $urlcall=NBII_SERVICE_URL;
	    break;
       case "NBII":
       	    $urlcall=NBII_SERVICE_URL;
	    break;
       default:
       	    $urlcall=LTER_SERVICE_URL;
	    break;
}
// Now query the HIVE service using the locally stored EML file
exec("/usr/bin/curl -T ". $tmpFileName . " ".$urlcall,$curlOutput);

// get rid of the temp file - we don't need it anymore
unlink($tmpFileName);

// read the returned XML lines into a single string
$xml_returned="";
foreach ($curlOutput as $curlLine){
	$xml_returned=$xml_returned.$curlLine;
}

//echo("<b>Data Returned</b>\n");
//echo($xml_returned);

//Correct XML until web service is fixed
$xml_returned=str_replace("<SKOSConcepts",'<SKOSConcepts xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:rdf="http://www.w3.org/TR/REC-rdf-syntax#" ',$xml_returned);
$xml_returned=str_replace("/>",'"/>',$xml_returned);
$xml_returned=str_replace('""','"',$xml_returned);
$xml_returned=str_replace('</rdf:RDF>','</rdf:Description></rdf:RDF>',$xml_returned);

//echo("<p>Edited SKOS<br>");
//echo($xml_returned);


/// GET THE XLS
        $xsl = new DOMDocument;
switch ($format){
       case "html":
               $xsl->load('/var/www/lterhive/LTER_hive2form.xsl');
	       break;
       case "php":
               $xsl->load('/var/www/lterhive/LTER_hive2phpform.xsl');
	       break;
       case "eml":
               $xsl->load('/var/www/lterhive/hive2EMLkeywordSet.xsl');
	       break;
       case "xml":
               $xsl->load('/var/www/lterhive/hive2EMLkeywordSet.xsl');
	       break;
       case "list":
               $xsl->load('/var/www/lterhive/LTER_hive2list.xsl');
	       break;
       case "quotedlist":
               $xsl->load('/var/www/lterhive/LTER_hive2quotedlist.xsl');
	       break;
       case "csv":
               $xsl->load('/var/www/lterhive/LTER_hive2csv.xsl');
	       break;
       case "HTML":
               $xsl->load('/var/www/lterhive/LTER_hive2form.xsl');
	       break;
       case "PHP":
               $xsl->load('/var/www/lterhive/LTER_hive2phpform.xsl');
	       break;
       case "EML":
               $xsl->load('/var/www/lterhive/hive2EMLkeywordSet.xsl');
	       break;
       case "XML":
               $xsl->load('/var/www/lterhive/hive2EMLkeywordSet.xsl');
	       break;
       case "LIST":
               $xsl->load('/var/www/lterhive/LTER_hive2list.xsl');
	       break;
       case "QUOTEDLIST":
               $xsl->load('/var/www/lterhive/LTER_hive2quotedlist.xsl');
	       break;
       case "CSV":
               $xsl->load('/var/www/lterhive/LTER_hive2csv.xsl');
	       break;
       default:
               $xsl->load('/var/www/lterhive/LTER_hive2quotedlist.xsl');
	       break;
}
       
            $xslt = new XSLTProcessor;
        $xslt->importStyleSheet($xsl);


/// MAKE A DOM FROM THE XML
        $xml = new DOMDocument;
        $xml->loadXML($xml_returned);
//
            // Transform XML and echo
        echo $xslt->transformToXML($xml);

function documentService(){
echo("<pre>\n
This web service provides a list of keywords in a thesaurus based on their relationship to a search term. 

BASIC SYNTAX: 
http://vocab.lternet.edu/webservice/findKeywords.php/XXTHESAURUSXX/XXFORMATXX/XXPACKAGEIDXX

Where:
 -- XXTHESAURUSXX is the Thesaurus to use for searching
 ---- lter - search the LTER Thesaurus for keywords
 ---- nbii - search the NBII Thesaurus for keywords

 -- XXFORMATXX is:
 ---- html (returns html table with checkboxes)* 
 ---- php (returns html table with checkboxes that load into a PHP array)*
 ---- list (list of terms separated by newlines)
 ---- quotedlist (list of terms in double quotes, separated by newlines)
 ---- csv (comma-separated list of quoted values)
 ---- xml (XML file containing an EML keywordSet)
 ---- eml same as xml (XML file containing an EML keywordSet)
*designed for integration into an HTML form

 -- XXPACKAGEIDXX is the PackageID of the dataset to be searched in the LTER metacat

ADVANCED SYNTAX:
http://vocab.lternet.edu/webservice/findKeywords.php/XXTHESAURUSXX/XXFORMATXX/XXPACKAGEIDXX?docurl=http://myserver.edu/mydocument
To refer to documents not included in the LTER Metacat, you can choose
an arbitrary packageID and provide a URL pointing to the document to
be scanned for keywords. As in:
http://vocab.lternet.edu/webservice/findKeywords.php/lter/quotedlist/anyname?docurl=http://www1.vcrlter.virginia.edu/data/query/text/eml/BPH8801A.xml


Samples: 

http://vocab.lternet.edu/webservice/findKeywords.php/lter/php/knb-lter-vcr.25
creates an HTML table suitable for insertion into an HTML form that
loads the suggested terms from the LTER Thesaurus into a PHP array
named term[], based on the contents of dataset \"knb-lter-vcr.25\".

http://vocab.lternet.edu/webservice/findKeywords.php/lter/quotedList/knb-lter-vcr.25
the same as the previous sample, except a simple list of quoted terms is returned. 
");
}
?>
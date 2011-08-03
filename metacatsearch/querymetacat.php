<?php

require("./config.metacatsearch.php");

$textstring =
  '<pathquery version="1.2">' .
  '  <returndoctype>eml://ecoinformatics.org/eml-2.1.0</returndoctype>' .
  '  <returndoctype>eml://ecoinformatics.org/eml-2.0.1</returndoctype>' .
  '  <returndoctype>eml://ecoinformatics.org/eml-2.0.0</returndoctype>' .
  '  <returnfield>eml/dataset/title</returnfield>' .
  '  <returnfield>eml/dataset/creator/individualName/surName</returnfield>' .
  '  <returnfield>eml/dataset/creator/organizationName</returnfield>' .
  '  <returnfield>eml/dataset/keywordSet/keyword</returnfield>' .
  '  <returnfield>eml/dataset/keywordSet/keywordThesaurus</returnfield>' ;
if (PACKAGEID_START <> "%") {
$textstring .=  '  <querygroup operator="INTERSECT">' .
  '   <queryterm casesensitive="false" searchmode="starts-with"><value>'. PACKAGEID_START . '</value><pathexpr>@packageId</pathexpr></queryterm>' ;
}
$textstring .=  '  <querygroup operator="UNION">' ;

     
     $textstring .= urldecode($_REQUEST["search"]) ;

if (PACKAGEID_START <> "%") {
$textstring .= 
  '  </querygroup>' .
  '  </querygroup>' .
  '</pathquery>';
}else{  
$textstring .= 
  '  </querygroup>' .
  '</pathquery>';
}
  
// send the string to the parameter pathquery_str if using GET query
//$urlcall="http://metacat.lternet.edu/knb/metacat?action=squery&qformat=xml&query=". urlencode($textstring);
//echo("<b>URL sent:</b> ". $urlcall."<p>");

$ch = curl_init("http://metacat.lternet.edu/knb/metacat");
//$ch = curl_init($urlcall);

// set URL and other appropriate options
//curl_setopt($ch, CURLOPT_URL, "http://www1.vcrlter.virginia.edu");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=squery&qformat=xml&query=". urlencode($textstring));


// grab URL and pass it to the browser
$xml_returned = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

// echo($xml_returned);


/// GET THE XLS
        $xsl = new DOMDocument;
        $xsl->load('./LTER_resultset.xsl');
            $xslt = new XSLTProcessor;
        $xslt->importStyleSheet($xsl);


/// MAKE A DOM FROM THE XML
        $xml = new DOMDocument;
        $xml->loadXML($xml_returned);
//
            // Transform XML and echo
        echo $xslt->transformToXML($xml);










?>
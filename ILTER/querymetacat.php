<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<?php
# This program accepts either a web form or REST input to set up a search of a Metacat for data. 
# PHP Written by John Porter, 2012, stylesheet processing by Margaret O'Brien 2011

$DEBUG=0;
mb_language('uni');
mb_internal_encoding('UTF-8');

//set baseURL to directory for querymetacat.php and LTER_resultset.xsl 
define(BASE_URL,"http://vocab.lternet.edu/ILTER/");

// set the scope for the search using the start of the packageID
define(PACKAGEID_START,"%");
//define(PACKAGEID_START,"knb-lter-");

// set the string describing the scope of the search
define(SCOPE_DESCR_STRING,"ILTER");

# Accept parameters either from path or from a form
$inPath=$_SERVER['PATH_INFO'];
$settings=explode("/",$inPath);
if($DEBUG){print_r($settings);}

if($_REQUEST['inLang']==""){
	$inLang=$settings[1];
}else{
	$inLang=$_REQUEST['inLang'];
}
if($_REQUEST['outLang']==""){
	$outLang=$settings[2];
}else{
	$outLang=$_REQUEST['outLang'];
}
if($_REQUEST['relation']==""){
	$relation=$settings[3];
}else{
	$relation=$_REQUEST['relation'];
}
if($_REQUEST['search']==""){
	$searchStr=$settings[4];
}else{
	$searchStr=$_REQUEST['search'];
}
$searchStr=str_replace("+"," ",$searchStr);

$metacatUrl=$_REQUEST['metacat'];
if ($metacatUrl==""){$metacatUrl="http://metacat.lternet.edu/knb/metacat";}

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
$textstring .=  ' <querygroup operator="INTERSECT">' .
  '   <queryterm casesensitive="false" searchmode="starts-with"><value>'. PACKAGEID_START . '</value><pathexpr>@packageId</pathexpr></queryterm>' ;
}
$textstring .=  '  <querygroup operator="UNION">' ;

# Go get the expanded list of keywords in pathquery form
$url="http://vocab.lternet.edu/ILTER/keywordlist/".$inLang."/".$outLang."/".$relation."/pathquery/".$searchStr;
if($DEBUG){print($url."\n");}
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_POST, 1);
// grab URL and pass it to the browser
$xml_returned = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

if ($DEBUG){echo(urldecode($xml_returned));}

$textstring .= urldecode($xml_returned) ;

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

if($DEBUG){print(urldecode($textstring));}
  
// send the string to the parameter pathquery_str if using GET query
//$urlcall="http://metacat.lternet.edu/knb/metacat?action=squery&qformat=xml&query=". urlencode($textstring);
//echo("<b>URL sent:</b> ". $urlcall."<p>");

$ch = curl_init($metacatUrl);
//$ch = curl_init($urlcall);

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "action=squery&qformat=xml&query=". urlencode($textstring));


// grab URL and pass it to the browser
$xml_returned = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

if($DEBUG){echo($xml_returned);}


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
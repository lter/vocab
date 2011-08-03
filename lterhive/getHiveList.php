<head>
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/lib/prototype/prototype.js">
</script> 
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/lib/scriptaculous/scriptaculous.js">
</script> 
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/src/AutoComplete.js"></script> 
</head>
<body>
<?php
if ((($_FILES["file"]["type"] == "text/xml")
|| ($_FILES["file"]["type"] == "text/plain")
|| ($_FILES["file"]["type"] == "text/csv"))
&& ($_FILES["file"]["size"] < 1000000))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
//    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
$inFileName= $_FILES["file"]["name"] ;
//    echo "Type: " . $_FILES["file"]["type"] . "<br />";
//    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

    if (file_exists("../tmp/" . $_FILES["file"]["tmp_name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      ".." . $_FILES["file"]["tmp_name"]);
      $tmpFileName= $_FILES["file"]["tmp_name"];
//      echo "Stored in: " . ".." . $_FILES["file"]["tmp_name"];
      }
    }
  }
else
  {
   if ((strlen($_REQUEST['inURL'])==0) && (strlen($_REQUEST['packageID'])==0)){
         echo "No valid inputs, please use back to return to the form and try again";
   	 exit();
   }
  }


$urlcall="http://scoria.lternet.edu:8080/lter-hive/schemes/lter/concepts/tags/SKOSFormat" ;
//echo("<b>URL sent:</b> ". $urlcall."<br>");
//echo("upload file /var/www".$tmpFileName."<p>");

//echo("/usr/bin/curl -T /var/www". $tmpFileName . " ".$urlcall);
exec("/usr/bin/curl -T /var/www". $tmpFileName . " ".$urlcall,$curlOutput);
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

echo("<h2>Suggested Keywords from: ".$inFileName."</h2>");
echo("<form action=\"/lterhive/LTERhive2list.php\" method=\"post\">");
echo("<input type=\"hidden\" name=\"thesaurus\" value=\"lter\">");
echo("<input type=\"hidden\" name=\"inFileName\" value=\"".$inFileName."\">");
echo("<table><tr><td width=\"40%\" bgcolor=\"cyan\">");

echo("<strong>Select the keywords you wish to use in your metadata from the suggested list.</strong><br><font size=-1>When there are similar terms (e.g., humidity, relative humdity), select only the most specific term that applies (e.g., relative humidity).</font> <br></td><td bgcolor=\"beige\">and/or <strong>Add Additional Keywords</td></tr><tr><td valign=\"top\" bgcolor=\"cyan\">");


/// GET THE XLS
        $xsl = new DOMDocument;
        $xsl->load('./LTER_hive2phpform.xsl');
            $xslt = new XSLTProcessor;
        $xslt->importStyleSheet($xsl);


/// MAKE A DOM FROM THE XML
        $xml = new DOMDocument;
        $xml->loadXML($xml_returned);
//
            // Transform XML and echo
        echo $xslt->transformToXML($xml);
echo("</td><td valign=\"top\" bgcolor=\"beige\">");
for ($i=0;$i < 10;$i++){
echo('
<input type="text" id="my_ac'.$i.'" name="term[]" size="45" autocomplete="off"/> 
<script type="text/javascript">
 new AutoComplete(\'my_ac'.$i.'\', \'../autocomplete/LTERAutoComp.php?m=text&s=\', { 
 delay: 0.1, 
 threshold: 1,
 resultFormat: AutoComplete.Options.RESULT_FORMAT_TEXT 
}); 
</script> 
'); 
}

echo("</td></tr></table>");

echo('<input type="Submit" value="Return List of Selected Terms">
Optional: Specify output keyword delimiter: 
<select name="textDelimiter">
<option value="" SELECTED>None</option>
<option value=\'"\'>double-quote (")</option>
<option value="\'">single-quote (\')</option>
</select>
');
echo("</form>");

?>
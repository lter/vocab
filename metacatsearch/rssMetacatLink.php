<?php
// Generate RSS list containing a variety of searches

$urlcall='http://vocab.lternet.edu/vocab/vocab/services.php?task=search&arg='. $_REQUEST['search']." ";
if ($xml= simplexml_load_file($urlcall,SimpleXMLElement,LIBXML_NOCDATA)){
//print_r($xml);
// test for any results found for search string
if($xml->result->count() > 0){
$numTerms=$xml->result[0]->term->count() ;
// multiply the number of terms by the number of types of searches
$numRssItems=$numTerms*4;

//echo "Num Terms: " . $numTerms ;

if ($numTerms > 0){
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\">
  <channel>
    <title>Search LTER Metacat for Datasets related to ".$_REQUEST["search"]."</title>
    <link>http://vocab.lternet.edu/metacatsearch/querymetacat.php?search=".urlencode($_REQUEST["search"])."</link>
    <description></description>
");
echo("<ttl>$numRssItems</ttl>
");

foreach ($xml->result->term as $term) {
// ONLY search for the actual term specified - not things that include it
//echo("<item><title>string lengths: ".$_REQUEST["search"]." ". $term->string . strlen($term->string). " " .strlen($_REQUEST["search"])."</title></item>");
//  if(strlen($term->string) == strlen($_REQUEST["search"])){
      generateSearchList($term->term_id,$term->string);
//  }
}
echo("</channel></rss>");
}
} // if any result tags found
} //if initial query successful

function generateSearchList($term_id,$termstr){
//echo("generateSearchList for ".$termstr);
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $relatedTerms=array();
	 $broaderTerms=array();
         $emptyArray=array();
	 if (getusefor($term_id,&$useforTerms) > 0){
	    outputItem($termstr,"and related non-preferred terms.",$useforTerms);
	 }else{
            outputItem($termstr,"only.",$emptyArray);
	 }
	 if (getnarrower($term_id,&$narrowerTerms) > 0){
	    outputItem($termstr,"and more specific terms.",$narrowerTerms);
	 }
	 if (getrelated($term_id,&$relatedTerms) > 0){
	    outputItem($termstr,"and related terms.",$relatedTerms);
	 }
}

function outputItem($termstr,$what,&$list){
	 echo "<item><title>Search LTER Datasets using: ";
       echo($termstr . "  " . $what." </title>" );
       echo("    <link>http://vocab.lternet.edu/metacatsearch/querymetacat.php?search=");
     echo(urlencode("<queryterm casesensitive=\"false\" searchmode=\"contains\"><value>".$termstr."</value><pathexpr>keyword</pathexpr></queryterm>"));
    if (count($list) > 0){
    foreach ($list as $term){
    	    echo(urlencode("<queryterm casesensitive=\"false\" searchmode=\"contains\"><value>" . $term . "</value><pathexpr>keyword</pathexpr></queryterm>" )) ;
	    }
    }
echo("</link> ");
echo("    <description>Searching: ".$termstr );
    if (count($list) > 0){
    foreach ($list as $term){
    	    echo(", ".$term) ;
	    }
    }
    echo ("</description>
    </item>
");
}


function getnarrower($term_id,&$narrowerTerms)
{
$numTerms=0;
$urlcall='http://vocab.lternet.edu/vocab/vocab/services.php?task=fetchDown&arg='.$term_id;
$xml= simplexml_load_file($urlcall,SimpleXMLElement,LIBXML_NOCDATA);
//print_r($xml);

//make sure at least one narrower term found by checking for result and resume
if ($xml->count() > 1){
$numTerms=$xml->result->term->count() ;
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($xml->result->term as $term) {
   $narrowerTerms[]=$term->string;
   $moreNarrower[]=$term->hasMoreDown;
   $termIds[]=$term->term_id;
}
$i=0;
foreach ($narrowerTerms as $narrowerTerm){
//   echo $narrowerTerm, $moreNarrower[$i] ;
   if($moreNarrower[$i] == 1) getNarrower($termIds[$i],$narrowerTerms);
   $i++;
}
}
} //end if 
return $numTerms;
}

function getusefor($term_id,&$useforTerms)
{
$numTerms=0;
$urlcall='http://vocab.lternet.edu/vocab/vocab/services.php?task=fetchAlt&arg='.$term_id;
$xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA);
//print_r($xml);

//make sure at least one narrower term found by checking for result and resume
if ($xml->count() > 1){
$numTerms=$xml->result->term->count() ;
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($xml->result->term as $term) {
   $useforTerms[]=$term->string;
//echo($term->string);
}
}
} //end if 
return $numTerms;
}

function getrelated($term_id,&$relatedTerms)
{
$numTerms=0;
$urlcall='http://vocab.lternet.edu/vocab/vocab/services.php?task=fetchRelated&arg='.$term_id;
$xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA);
//print_r($xml);

//make sure at least one narrower term found by checking for result and resume
if ($xml->count() > 1){
$numTerms=$xml->result->term->count() ;
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($xml->result->term as $term) {
   $relatedTerms[]=$term->string;
//echo($term->string);
}
}
} //end if 
return $numTerms;
}



?>
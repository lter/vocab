<?php
// Generate HTML list containing a variety of searches

require("./config.metacatsearch.php");

echo("<html>
<head>
     <title>Search LTER Metacat for Datasets related to ".$_REQUEST["search"]."</title>
 </head>");
echo("<h2>Search the LTER Metacat for datasets related to &quot;".$_REQUEST["search"]."&quot;</h2>");

$search=$_REQUEST['search'];
$urlcall=SERVICE_URL . '?task=search&arg='. $_REQUEST['search']." ";
if ($xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA)){
//print_r($xml);
// test for any results found for search string
if($xml->result->count() > 0){
$numTerms=$xml->result[0]->term->count() ;
//echo "Num Terms: " . $numTerms ;

if ($numTerms > 0){
$isTermFound=FALSE;
foreach ($xml->result->term as $term) {
// ONLY search for the actual term specified - not things that include it
//echo("<br>string lengths: ".$search."=". strlen($search)." " . $term->string. "= ".strlen($term->string)."");
// search if either the term, or its nonpreferred form is the same length as the search
if((strlen($term->string) == strlen($search))||(strlen($term->no_term_string) == strlen($search))){
      generateSearchList($term->term_id,$term->string);
      $isTermFound=TRUE;
  }
}
if($isTermFound == FALSE){
echo("Recommended Alternate Searches: ");
foreach ($xml->result->term as $term) {
   echo("<a href=\"/metacatsearch/urlMetacatLink.php?search=".$term->string. "\">".$term->string."</a>, "); 
}
    $emptyArray=array();
    outputItem($_REQUEST["search"],"",$emptyArray);
}
echo("<hr>Note: searches of large numbers of terms can be slow! Please be patient. </html>");
} // if numterms > 0
}else{
    $emptyArray=array();
    outputItem($_REQUEST["search"],"",$emptyArray);
} // if any result tags found
} //if result count >0 

function generateSearchList($term_id,$termstr){
//echo("generateSearchList for ".$termstr);
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $narrowerTerms=array();
	 $relatedNarrowerTerms=array();
	 $relatedTerms=array();
	 $broaderTerms=array();
         $emptyArray=array();
	 if (getusefor($term_id,&$useforTerms) > 0){
	    outputItem($termstr,"and related non-preferred terms.",$useforTerms);
	 }else{
            outputItem($termstr,"only.",$emptyArray);
	 }
	 if (getnarrower($term_id,&$narrowerTerms) > 0){
	    $numNarrower=count($narrowerTerms);
	    $narrowerTerms=array_merge($useforTerms,$narrowerTerms);
	    outputItem($termstr,"and more specific terms.",$narrowerTerms);
	 }
	 if (getrelated($term_id,&$relatedTerms) > 0){
	    $numRelated=count($relatedTerms);
	    $relatedTerms=array_merge($useforTerms,$relatedTerms);
	    outputItem($termstr,"and related terms.",$relatedTerms);
	 }

	 if (($numNarrower > 0)&&($numRelated > 0)){
	    $relatedTerms=array_merge($relatedTerms,$narrowerTerms);
	    outputItem($termstr,"and both related and more specific terms.",$relatedTerms);
	 }

	 if (getrelatednarrower($term_id,&$relatedNarrowerTerms) > 0){
	    $relatedNarrowerTerms=array_merge($relatedNarrowerTerms,$narrowerTerms);
	    outputItem($termstr,"and more specific terms, related terms and their more specific terms.",$relatedNarrowerTerms);
	 }

}

function outputItem($termstr,$what,&$list){
//use exact searches for strings less than this length
    $minLength=4;  
    if (strlen($termstr) < $minLength){	    
    	    $searchmode="equals";
    }else{
    	    $searchmode="contains";
    }
    echo("\n <hr>"); 	 
    echo("<li>Search ". SCOPE_DESCR_STRING." Datasets using: ");
    echo ("<form action=\"".BASE_URL."/querymetacat.php\" method=\"post\">\n");
    echo("<input type=\"hidden\" name=\"search\" value=\"".urlencode("<queryterm casesensitive=\"false\" searchmode=\"". $searchmode. "\"><value>".$termstr."</value><pathexpr>keyword</pathexpr></queryterm>")."\n");
    if (count($list) > 0){
    foreach ($list as $term){
    	    if (strlen($term) < $minLength){	    
    	       $searchmode="equals";
	    }else{
	       $searchmode="contains";
    	    }
    	    echo(urlencode("<queryterm casesensitive=\"false\" searchmode=\"". $searchmode . "\"><value>" . $term . "</value><pathexpr>keyword</pathexpr></queryterm>")."\n") ;
	    }
    }
       echo("\"><input type=\"submit\" value=\"".$termstr . "  " . $what."\" >" );
//echo(" <br>Searching: ".$termstr );
    if (count($list) > 0){
    foreach ($list as $term){
    	    echo(", ".$term) ;
	    }
    }
echo("</form>");
}


function getnarrower($term_id,&$narrowerTerms)
{
$usefornarrowerTerms=array();
$numTerms=0;
$urlcall=SERVICE_URL . '?task=fetchDown&arg='.$term_id;
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
    getusefor($term->term_id,&$usefornarrowerTerms);
}
$i=0;
foreach ($narrowerTerms as $narrowerTerm){
//   echo $narrowerTerm, $moreNarrower[$i] ;
   if($moreNarrower[$i] == 1) getNarrower($termIds[$i],$narrowerTerms);
   $i++;
}
    $narrowerTerms=array_merge($narrowerTerms,$usefornarrowerTerms);
//print_r($narrowerTerms);
}
} //end if 
return $numTerms;
}

function getusefor($term_id,&$useforTerms)
{
$numTerms=0;
$urlcall=SERVICE_URL . '?task=fetchAlt&arg='.$term_id;
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
$useforTerms=array();
$numTerms=0;
$urlcall=SERVICE_URL . '?task=fetchRelated&arg='.$term_id;
$xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA);
//print_r($xml);

//make sure at least one related term found by checking for result and resume
if ($xml->count() > 1){
$numTerms=$xml->result->term->count() ;
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($xml->result->term as $term) {
   $relatedTerms[]=$term->string;
    getusefor($term->term_id,&$useforTerms);

//echo($term->string);
}
    $relatedTerms=array_merge($relatedTerms,$useforTerms);
}
} //end if 
return $numTerms;
}


function getrelatednarrower($term_id,&$relatedNarrowerTerms)
{
$useforTerms=array();
$narrowerTerms=array();
$numTerms=0;
$urlcall=SERVICE_URL . '?task=fetchRelated&arg='.$term_id;
$xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA);
//print_r($xml);

//make sure at least one related term found by checking for result and resume
if ($xml->count() > 1){
$numTerms=$xml->result->term->count() ;
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($xml->result->term as $term) {
   $relatedNarrowerTerms[]=$term->string;
    getusefor($term->term_id,&$useforTerms);
    getnarrower($term->term_id,&$narrowerTerms);

//echo($term->string);
}
    $relatedNarrowerTerms=array_merge($relatedNarrowerTerms,$useforTerms,$narrowerTerms);
}
} //end if 
return $numTerms;
}


?>
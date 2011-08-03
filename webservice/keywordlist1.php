<?php
// Generate list of keywords from a thesaurus based on relationships

define(DEFAULT_SERVICE_URL,"http://vocab.lternet.edu/vocab/vocab/services.php");

// set the service_URL to the tematres service web address 
$service_URL=$_REQUEST['service'];
if ($service_URL == ""){$service_URL=DEFAULT_SERVICE_URL;}

$inPath=$_SERVER['PATH_INFO'];
//echo $inPath;

$settings=explode("/",$inPath);
//print_r($settings);
$relation=$settings[1];
$format=$settings[2];
$search=$settings[3];
$search=str_replace("+"," ",$search);

if ($search == ""){
documentService();
exit;
}


$urlcall=$service_URL . '?task=search&arg='. $search." ";
if ($xml= simplexml_load_file($urlcall,SimpleXMLElement,LIBXML_NOCDATA)){
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
// PROBLEM NOTE: no_term_string is NOT set when the use-for term is a substring of the term....
// so checking for matching no-term_string is not 100% successful
  if((strlen($term->string) == strlen($search))||(strlen($term->no_term_string) == strlen($search))){
  	$termstr=$term->string;
	$term_id=$term->term_id;
//print "term is $termstr and term_id is $term_id";

// Now go hunting for terms based on their relationship
      switch ($relation) {
      case "exact":
        generateUseFor($term_id,$termstr);
        break;
    case "narrow":
        generateNarrow($term_id,$termstr);
        break;
    case "related":
        generateRelated($term_id,$termstr);
        break;
    case "narrowrelated":
        generateNarrowRelated($term_id,$termstr);
        break;
    case "all":
        generateNarrowRelatedNarrow($term_id,$termstr);
        break;
}
    $isTermFound=TRUE;
  }
}
if($isTermFound == FALSE){
    $emptyArray=array();
    outputItem($search,$emptyArray);
}
} // if numterms > 0
}else{
    $emptyArray=array();
    outputItem($search,$emptyArray);
} // if any result tags found
} //if result count >0 

function generateUseFor($term_id,$termstr){
	 $useforTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
         outputItem($termstr,$useforTerms);
}

function generateNarrow($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
  	 getnarrower($term_id,&$narrowerTerms);
         $narrowerTerms=array_merge($useforTerms,$narrowerTerms);
         outputItem($termstr,$narrowerTerms);


}

function generateRelated($term_id,$termstr){
	 $useforTerms=array();
	 $relatedTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
	 getrelated($term_id,&$relatedTerms);
         $relatedTerms=array_merge($useforTerms,$relatedTerms);
	 outputItem($termstr,$relatedTerms);
}

function generateNarrowRelated($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $relatedTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
	 getnarrower($term_id,&$narrowerTerms);
	 $numNarrower=count($relatedTerms);
	 getrelated($term_id,&$relatedTerms);
         $numRelated=count($relatedTerms);
         $relatedTerms=array_merge($useforTerms,$relatedTerms);
         $relatedTerms=array_merge($relatedTerms,$narrowerTerms);
         outputItem($termstr,$relatedTerms);
}
function generateNarrowRelatedNarrow($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $relatedNarrowerTerms=array();
	 $relatedTerms=array();

	 getusefor($term_id,&$useforTerms);
  	 getnarrower($term_id,&$narrowerTerms);
	 getrelatednarrower($term_id,&$relatedNarrowerTerms);
	 $relatedNarrowerTerms=array_merge($useforTerms,$relatedNarrowerTerms);
	 $relatedNarrowerTerms=array_merge($relatedNarrowerTerms,$narrowerTerms);
	 outputItem($termstr,$relatedNarrowerTerms);
}

function outputItem($termstr,&$list){
    global $format;	 
    $format=strtolower($format);
    if ($format == ''){$format="list";}
//sort list and eliminate duplicates
    $list=array_unique($list);
    natcasesort($list);

    switch ($format){
    	   case "list":	
    	   	echo($termstr ."\n");
                if (count($list) > 0){
                   foreach ($list as $term){
       	    		echo($term ."\n") ;
        	   }
    		}
		break;
    	   case "csv":	
    	   	echo('"'.$termstr.'"');
                if (count($list) > 0){
                   foreach ($list as $term){
       	    		echo(', "' . $term . '"') ;
        	   }
    		}
		break;
    	   case "quotedlist":	
    	   	echo('"'.$termstr.'"'."\n");
                if (count($list) > 0){
                   foreach ($list as $term){
       	    		echo('"' . $term . '"'."\n") ;
        	   }
    		}
		break;
    	   case "xml":	
	        echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<termList>\n");
    	   	echo('  <term>'.$termstr.'</term>'."\n");
                if (count($list) > 0){
                   foreach ($list as $term){
       	    		echo('  <term>' . $term . '</term>'."\n") ;
        	   }
    		}
	        echo("</termList>\n");
		break;
    }
}


function getnarrower($term_id,&$narrowerTerms)
{
global $service_URL;
$usefornarrowerTerms=array();
$numTerms=0;
$urlcall=$service_URL . '?task=fetchDown&arg='.$term_id;
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
global $service_URL;
$numTerms=0;
$urlcall=$service_URL . '?task=fetchAlt&arg='.$term_id;
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
global $service_URL;
$useforTerms=array();
$numTerms=0;
$urlcall=$service_URL . '?task=fetchRelated&arg='.$term_id;
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
global $service_URL;
$useforTerms=array();
$narrowerTerms=array();
$numTerms=0;
$urlcall=$service_URL . '?task=fetchRelated&arg='.$term_id;
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

function documentService(){
echo("<pre>\n
This web service provides a list of keywords in a thesaurus based on their relationship to a search term. 

BASIC SYNTAX: http://vocab.lternet.edu/webservice/keywordlist.php/XXRELATIONXX/XXFORMATXX/XXKEYWORDXX

Where:
 -- XXRELATIONXX is:
 ---- exact (returns the term plus any synonyms or \"use for\" terms)
 ---- narrow (returns the term plus its more specific, narrower terms or \"children\")
 ---- related (returns the term plus its related terms)
 ---- narrowrelated (returns the term plus both its narrower and related terms)
 ---- all (returns the term plus its narrower terms, its related terms and the narrower terms of the related terms)

 -- XXFORMATXX is:
 ---- list (list of terms separated by newlines)
 ---- quotedlist (list of terms in double quotes, separated by newlines)
 ---- csv (comma-separated list of quoted values)
 ---- xml (XML file containing a single <termList> consisting of one or more <terms>)

 -- XXKEYWORDXX is the term to be searched for.

ADVANCED SYNTAX: 
http://vocab.lternet.edu/webservice/keywordlist.php/XXRELATIONXX/XXFORMATXX/XXKEYWORDXX?service=XXSERVICEXX

As above, but where XXSERVICEXX is the URL of the Tematres Service that you want to use. 
For example: http://vocab.lternet.edu/vocab/luq/services.php. 

Samples: 
http://vocab.lternet.edu/webservice/keywordlist.php/narrow/csv/carbon
searches for the term \"carbon\" and its narrower terms and returns them as a comma-separated value format. 

http://vocab.lternet.edu/webservice/keywordlist.php/narrow/csv/carbon?service=http://vocab.lternet.edu/vocab/luq/services.php
does the same, but uses narrower terms from the \"luq\" thesaurus.

http://vocab.lternet.edu/webservice/keywordlist.php/related/xml/carbon+dioxide
searches for the term \"carbon dioxide\" and its narrower terms and returns them as a comma-separated-value format. A URL-encoded space (%20) can also be used to separate words within a multi-word term. 

");
}
?>
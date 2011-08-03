<?php
// Generate list of keywords from a thesaurus based on relationships

define(DEFAULT_SERVICE_URL,"http://vocab.lternet.edu/vocab/vocab/services.php");
//set the output format to list
$format="list";

// set the service_URL to the tematres service web address 
$service_URL=$_REQUEST['service'];
if ($service_URL == ""){$service_URL=DEFAULT_SERVICE_URL;}

$inPath=$_SERVER['PATH_INFO'];
//echo $inPath;


$urlcall=$service_URL . '?task=fetchTopTerms';
if ($xml= simplexml_load_file($urlcall,"SimpleXMLElement",LIBXML_NOCDATA)){
//print_r($xml);
// test for any results found for search string
if($xml->result->count() > 0){
$numTerms=$xml->result[0]->term->count() ;
//echo "Num Terms: " . $numTerms ;

if ($numTerms > 0){
foreach ($xml->result->term as $term) {
        generateNarrowRelatedNarrow($term->term_id,$term->string);
  }
} // if numterms > 0
} // if any result tags found
} //if result count >0 

function generateNarrowRelatedNarrow($term_id,$termstr){
	 $narrowerTerms=array();
	 $relatedNarrowerTerms=array();
	 $relatedTerms=array();
  	 getnarrower($term_id,&$narrowerTerms);
	 getrelatednarrower($term_id,&$relatedNarrowerTerms);
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

?>
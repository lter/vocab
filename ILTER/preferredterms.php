<?php
// Generate list of preferred or alternate keywords from a thesaurus for a particular language

// include ARC2 libraries
require_once('ARC2.php');
#print "ARC2 run";
mb_language('uniy');
mb_internal_encoding('UTF-8');

define(DEFAULT_SERVICE_URL,"http://vocabs.lter-europe.net/PoolParty/sparql/iltervocabdemo");
$DEBUG=0;

// set the service_URL to SPARQL endpoint
$service_URL=$_REQUEST['service'];
if ($service_URL == ""){$service_URL=DEFAULT_SERVICE_URL;}

$inPath=$_SERVER['PATH_INFO'];
//echo $inPath;

$settings=explode("/",$inPath);
if($DEBUG){print_r($settings);}
$inLang=$settings[1];
$format=$settings[2];

if ($inLang == ""){$inLang = "en";}
if ($format == ""){$format = "list";}


// set the service_URL to the tematres service web address 
$service_URL=$_REQUEST['service'];
if ($service_URL == ""){$service_URL=DEFAULT_SERVICE_URL;}

$inPath=$_SERVER['PATH_INFO'];
//echo $inPath;
// configure the remote store
$configuration = array('remote_store_endpoint'  => $service_URL);

#print $configuration['remote_store_endpoint'];

$store = ARC2::getRemoteStore($configuration);
if ($DEBUG){print_r($store);}



if ($inLang=="en"){
# search for concepts as prefLabels
$query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?Concept ?Label
WHERE
{ ?Concept ?x skos:Concept .
{ ?Concept skos:prefLabel ?Label .
  FILTER (langMatches(lang(?Label),'".$inLang."'))
}
} ORDER BY ?Label
" ;
if ($DEBUG) {print $query;}
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print_r($rows1);}
}else{
# If we don't find hit in prefLabels, search altLabels
   $query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?Concept ?Label
WHERE
{ ?Concept ?x skos:Concept .
{ ?Concept skos:altLabel ?Label .
  FILTER (langMatches(lang(?Label),'".$inLang."'))
}
} ORDER BY ?Label
" ;
  if ($DEBUG) {print $query;}
  $rows1 = $store->query($query, 'rows');
  if ($DEBUG){print_r($rows1);}
} // end if else
$numTerms=count($rows1);
$myTerms=array();
if ($numTerms > 0){
# Loop through the concepts to find the desired terms
foreach ($rows1 as $row1) {
	if($DEBUG){print_r($row1);}
	$myTerms[]=$row1['Label'];
} // end foreach
outputItem($row1['Label'],$myTerms);
} // end if

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

?>
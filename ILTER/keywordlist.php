<?php
// Generate list of keywords from a thesaurus based on relationships
// written by John Porter, 2012 

// include ARC2 libraries
require_once('ARC2.php');
#print "ARC2 run";
mb_language('uni');
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
$outLang=$settings[2];
#$thesaurus=$settings[3];
$relation=$settings[3];
$format=$settings[4];
$search=$settings[5];
$search=str_replace("+"," ",$search);


if($DEBUG){print($search);}

if ($search == ""){
documentService();
exit;
}
// configure the remote store
$configuration = array('remote_store_endpoint'  => $service_URL);

#print $configuration['remote_store_endpoint'];

$store = ARC2::getRemoteStore($configuration);
if ($DEBUG){print_r($store);}

# search for concepts as prefLabels
$query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?Concept ?Label
WHERE
{ ?Concept ?x skos:Concept .
{ ?Concept skos:prefLabel ?Label .
  FILTER ((regex(str(?Label), '^".$search."$', 'i'))&&(langMatches(lang(?Label),'".$inLang."')))
}
}
" ;
if ($DEBUG) {print $query;}
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print_r($rows1);}

# If we don't find hit in prefLabels, search altLabels
if (count($rows1) == 0){
   $query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?Concept ?Label
WHERE
{ ?Concept ?x skos:Concept .
{ ?Concept skos:altLabel ?Label .
  FILTER ((regex(str(?Label), '^".$search."$', 'i'))&&(langMatches(lang(?Label),'".$inLang."')))
}
}
" ;
  if ($DEBUG) {print $query;}
  $rows1 = $store->query($query, 'rows');
  if ($DEBUG){print_r($rows1);}
} //end if count(rows1)>0
$numTerms=count($rows1);
$myTerms=array();
if ($numTerms > 0){
# Loop through the concepts to find the desired terms
foreach ($rows1 as $row1) {
	if($DEBUG){print_r($row1);}
	$term_id=$row1['Concept'];
	$termstr=$row1['Label'];
	if($DEBUG){print "\nterm is $termstr and term_id is $term_id\n";}
// Now go hunting for terms based on their relationship
      switch ($relation) {
    case "exact":
        $myNewTerms=generateUseFor($term_id,$termstr);
        break;
    case "narrow":
        $myNewTerms=generateNarrow($term_id,$termstr);
        break;
    case "related":
        $myNewTerms=generateRelated($term_id,$termstr);
        break;
    case "narrowrelated":
        $myNewTerms=generateNarrowRelated($term_id,$termstr);
        break;
    case "all":
        $myNewTerms=generateNarrowRelatedNarrow($term_id,$termstr);
        break;
} //end switch
    $myTerms=array_merge($myTerms,$myNewTerms);
} // end foreach
    outputItem($search,$myTerms);
} // if numterms > 0
else{
    $emptyArray=array();
    outputItem($search,$emptyArray);
} // if any matches found


function generateUseFor($term_id,$termstr){
	 $useforTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
#         outputItem($termstr,$useforTerms);
	  return($useforTerms);
}

function generateNarrow($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
  	 getnarrower($term_id,&$narrowerTerms);
         $narrowerTerms=array_merge($useforTerms,$narrowerTerms);
#         outputItem($termstr,$narrowerTerms);
	  return($narrowerTerms);
}

function generateRelated($term_id,$termstr){
	 $useforTerms=array();
	 $relatedTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
	 getrelated($term_id,&$relatedTerms);
         $relatedTerms=array_merge($useforTerms,$relatedTerms);
#	 outputItem($termstr,$relatedTerms);
         return($relatedTerms);
}

function generateNarrowRelated($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $relatedTerms=array();
         $emptyArray=array();
	 getusefor($term_id,&$useforTerms);
	 getnarrower($term_id,&$narrowerTerms);
	 getrelated($term_id,&$relatedTerms);
         $relatedTerms=array_merge($useforTerms,$relatedTerms);
         $relatedTerms=array_merge($relatedTerms,$narrowerTerms);
#         outputItem($termstr,$relatedTerms);
         return($relatedTerms);
}
function generateNarrowRelatedNarrow($term_id,$termstr){
	 $useforTerms=array();
	 $narrowerTerms=array();
	 $relatedNarrowerTerms=array();
	 $relatedTerms=array();
	 getusefor($term_id,&$useforTerms);
  	 getnarrower($term_id,&$narrowerTerms);
	 getrelatednarrower($term_id,&$relatedNarrowerTerms);
	 $relatedNarrowerTerms=array_merge($narrowerTerms,$useforTerms,$relatedNarrowerTerms);
         return($relatedNarrowerTerms);
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
		        if($term !=""){
       	    			 echo($term ."\n") ;
			}
        	   }
    		}
		break;
    	   case "csv":	
    	   	echo('"'.$termstr.'"');
                if (count($list) > 0){
                   foreach ($list as $term){
		        if($term !=""){
				 echo(', "' . $term . '"') ;
			 }
        	   }
    		}
		break;
    	   case "quotedlist":	
    	   	echo('"'.$termstr.'"'."\n");
                if (count($list) > 0){
                   foreach ($list as $term){
       	    		if($term !=""){
				echo('"' . $term . '"'."\n") ;
			}
        	   }
    		}
		break;
    	   case "xml":	
	        echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<termList>\n");
    	   	echo('  <term>'.$termstr.'</term>'."\n");
                if (count($list) > 0){
                   foreach ($list as $term){
		        if($term !=""){
				echo('  <term>' . $term . '</term>'."\n") ;
			}
        	   }
    		}
	        echo("</termList>\n");
		break;
    	   case "pathquery":
	        $minLength=3;	
    	    if (strlen($termstr) < $minLength){	    
    	       $searchmode="equals";
	    }else{
	       $searchmode="contains";
    	    }
    echo(urlencode('<queryterm casesensitive="false" searchmode="'. $searchmode. '"><value>'.$termstr.'</value><pathexpr>keyword</pathexpr></queryterm>')."\n");
    if (count($list) > 0){
    foreach ($list as $term){
        if($term !=""){
    	    if (strlen($term) < $minLength){	    
    	       $searchmode="equals";
	    }else{
	       $searchmode="contains";
    	    }
    	    echo(urlencode("<queryterm casesensitive=\"false\" searchmode=\"". $searchmode . "\"><value>" . $term . "</value><pathexpr>keyword</pathexpr></queryterm>")."\n") ;
	    } //end foreach
      } //end if 
      } //end if 
		break;
    }
}

function getnarrower($term_id,&$narrowerTerms)
{
global $store,$DEBUG,$outLang;
if ($DEBUG){print("\ngetnarrower\n");}
if ($term_id !=""){
$usefornarrowerTerms=array();
$numTerms=0;
$query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?label  ?narrow
WHERE
{ <".$term_id."> skos:narrower ?narrow .
{OPTIONAL { ?narrow skos:prefLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
 } UNION {
  OPTIONAL { ?narrow skos:altLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
}
}" ;
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print $query;print_r($rows1);}
$numTerms=count($rows1);
if ($numTerms > 0){
foreach ($rows1 as $term) {
   $narrowerTerms[]=$term['label'];
   $termIds[]=$term['narrow'];
    getusefor($term['narrow'],&$usefornarrowerTerms);
} //end inner foreach
} //end outer foreach
$i=0;
foreach ($termIds as $narrowerTerm){
//   echo $narrowerTerm, $moreNarrower[$i] ;
     getnarrower($termIds[$i],&$narrowerTerms);
     $i++;
}
$narrowerTerms=array_merge($narrowerTerms,$usefornarrowerTerms);
//print_r($narrowerTerms);
} //end if 
else{
	$numTerms=0;
}
return $numTerms;
}


function getusefor($term_id,&$useforTerms)
{
global $store,$DEBUG,$outLang;
if ($DEBUG){print("\ngetusefor\n");}
$numTerms=0;
   $query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?label
WHERE
{ <".$term_id."> skos:altLabel ?label .
  FILTER (langMatches(lang(?label),'".$outLang."'))
}
" ;
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print $query;print_r($rows1);}
$numTerms=count($rows1);
if ($numTerms > 0){
foreach ($rows1 as $term) {
   $useforTerms[]=$term['label'];
   if ($DEBUG){echo($term['label']);}
}
if ($DEBUG){print_r($useforTerms);}
} //end if 
return $numTerms;
}

function getrelated($term_id,&$relatedTerms)
{
global $store,$DEBUG,$outLang;
if ($DEBUG){print("\ngetrelated\n");}
$useforTerms=array();
$numTerms=0;
$query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?label  ?related
WHERE
{ <".$term_id."> skos:related ?related .
{OPTIONAL { ?related skos:prefLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
 } UNION {
  OPTIONAL { ?related skos:altLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
}
}" ;
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print $query;print_r($rows1);}
$numTerms=count($rows1);
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($rows1 as $term)  {
   $relatedTerms[]=$term['label'];
   $termIds[]=$term['related'];
    getusefor($term->term_id,&$useforTerms);
}
    $relatedTerms=array_merge($relatedTerms,$useforTerms);
} //end if 
return $numTerms;
}


function getrelatednarrower($term_id,&$relatedNarrowerTerms)
{
global $store,$DEBUG,$outLang;
if ($DEBUG){print("\ngetrelatednarrower\n");}

$useforTerms=array();
$narrowerTerms=array();
$allUseforNarrowerTerms=array();
$numTerms=0;
$query = "
PREFIX skos:<http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?label  ?related
WHERE
{ <".$term_id."> skos:related ?related .
{OPTIONAL { ?related skos:prefLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
 } UNION {
  OPTIONAL { ?related skos:altLabel ?label; 
  FILTER (langMatches(lang(?label),'".$outLang."'))}
}
}" ;
$rows1 = $store->query($query, 'rows');
if ($DEBUG){print $query;print_r($rows1);}
$numTerms=count($rows1);
//echo $numTerms." Terms found";
if ($numTerms > 0){
foreach ($rows1 as $term)  {
   $relatedNarrowerTerms[]=$term['label'];
   $termIds[]=$term['related'];
    getusefor($term['related'],&$useforTerms);
    getnarrower($term['related'],&$narrowerTerms);
    $allUseforNarrowerTerms=array_merge($allUseforNarrowerTerms,$useforTerms,$narrowerTerms);
}
    $relatedNarrowerTerms=array_merge($relatedNarrowerTerms,$allUseforNarrowerTerms);
} //end if 
return $numTerms;
}

function documentService(){
echo("<pre>\n
This web service provides a list of keywords in a thesaurus based on their relationship to a search term. 

BASIC SYNTAX: http://vocab.lternet.edu/ILTER/keywordlist.php/XXINLANGXX/XXOUTLANGXX/XXRELATIONXX/XXFORMATXX/XXKEYWORDXX

Where:
 -- XXINLANGXX
 ---- two character code for the language (e.g., es, en, zh) used for input of the keyword 

 -- XXOUTLANGXX
 ---- two character code for the language (e.g., es, en, zh) used for output of the keyword list

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
 ---- xml (XML file containing a single &lt;termList> consisting of one or more &lt;terms>)
 ---- pathquery (XML stub containing URLencoded queryterms for use with Metacat )

 -- XXKEYWORDXX is the term to be searched for.

ADVANCED SYNTAX: 
http://vocab.lternet.edu/ILTER/keywordlist.php/XXRELATIONXX/XXFORMATXX/XXKEYWORDXX?service=XXSERVICEXX

As above, but where XXSERVICEXX is the URL of the SPARQL endpoint that you want to use. 

Samples: 
http://vocab.lternet.edu/ILTER/keywordlist.php/en/fr/narrow/csv/carbon
searches for the term \"carbon\" and its narrower terms and returns them as a comma-separated value format in French. 
http://vocab.lternet.edu/ILTER/keywordlist.php/en/fr/related/xml/carbon+dioxide
searches for the term \"carbon dioxide\" and its narrower terms and returns them as a comma-separated-value format. A URL-encoded space (%20) can also be used to separate words within a multi-word term. 

John Porter - jporter@lternet.edu

");
}
?>
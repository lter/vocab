<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 

</head>
<body>
Display ILTER Search options: <br>
<?php
mb_language('uni');
mb_internal_encoding('UTF-8');

$DEBUG=0;
$inlang=$_REQUEST['lang'];
$outlang=$_REQUEST['outlang'];
$langName=$outlang;
if ($outlang==""){
   $outlang='*';
   $langName='All Others'; 
};

$searchStr=$_REQUEST['search'];

print("<h1>Language:".$inlang."     Search Term: ".$searchStr."</h1><hr>");
$searchStr=urlEncode($searchStr);

print("<h2>Term and Synonyms - <a href='multisearch.php?lang=".$inlang."&outlang=".$outlang."&search=".$searchStr."&relation=exact'>Search....</a>
</h2>");
print("<h3>English</h3>");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/en/exact/csv/".$searchStr));
print("<h3>Language ".$langName." </h3>");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/$outlang/exact/csv/".$searchStr));

print("<hr><h2>Term, Synonyms and Narrower Terms - <a href='multisearch.php?lang=".$inlang."&outlang=".$outlang."&search=".$searchStr."&relation=narrow'>Search....</a></h2>\n");
print("<h3>English</h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/en/narrow/csv/".$searchStr));
print("<h3>Language ".$langName." </h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/$outlang/narrow/csv/".$searchStr));

print("<hr><h2>Term, Synonyms and Related Terms  - <a href='multisearch.php?lang=".$inlang."&outlang=".$outlang."&search=".$searchStr."&relation=related'>Search....</a></h2>\n");
print("<h3>English</h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/en/related/csv/".$searchStr));
print("<h3>Language ".$langName." </h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/$outlang/related/csv/".$searchStr));

print("<hr><h2>Term, Synonyms, Narrower and Related Terms  - <a href='multisearch.php?lang=".$inlang."&outlang=".$outlang."&search=".$searchStr."&relation=narrowrelated'>Search....</a></h2>\n");
print("<h3>English</h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/en/narrowrelated/csv/".$searchStr));
print("<h3>Language ".$langName." </h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/$outlang/narrowrelated/csv/".$searchStr));

print("<hr><h2>Term, Synonyms, Narrower, Related and Narrower Terms of Related Terms - <a href='multisearch.php?lang=".$inlang."&outlang=".$outlang."&search=".$searchStr."&relation=all'>Search....</a></h2>\n");
print("<h3>English</h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/en/all/csv/".$searchStr));
print("<h3>Language ".$langName." </h3>\n");
print(webfetch("http://vocab.lternet.edu/ILTER/keywordlist/".$inlang."/$outlang/all/csv/".$searchStr));


function webfetch($url){
global $DEBUG;
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

return($xml_returned); 
}


?> 
</body>

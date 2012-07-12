<?php
$languages=array(0 => "ar","cs","zh","zh-cn","zh-tw","da","en","fi","fr","de","hu","it","ja","ko","ms","no","pl","pt","sr","sk","es","sv","ro","vi","iw");



print("Refreshing preferred terms for: <br><ul>\n");
foreach ($languages as $lang){
	print("<li>".$lang." </li>\n");
	$outfile=fopen("/var/www/ILTER/prefterms/".$lang.".txt","w");
	$url="http://vocab.lternet.edu/ILTER/preferredterms.php/".$lang;
	if($DEBUG){print($url."\n");}
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_MUTE, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	// grab URL and pass it to the browser
	$returned = curl_exec($ch);
	curl_close($ch);
        fwrite($outfile,$returned);
}
?>





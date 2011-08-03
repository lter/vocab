<?php
echo("<h2>Keywords selected for: ".$_REQUEST['inFileName']."</h2><hr>");

echo("<!-- generate list of selected terms as a list -->");
$numTerms=count($_REQUEST["term"]);
//echo($numTerms . " terms were selected");
$terms=$_REQUEST["term"];
$textDelimiter=$_REQUEST["textDelimiter"];
//echo("Delimiter is: ". $textDelimiter. "<br>");
for ($i=0;$i < $numTerms; $i++){
  if (strlen($terms[$i])>0){
      echo($textDelimiter.$terms[$i].$textDelimiter."<br>");
  }
}

echo("<!-- now do it again as XML -->");
echo("<hr>As EML keywordSet:<br>
<form>
<textarea rows=20 cols=60>
");
echo('<?xml version="1.0" encoding="UTF-8" ?>
<keywordSet>');
echo("\n");
for ($i=0;$i < $numTerms; $i++){
  if (strlen($terms[$i])>0){
      echo('  <keyword keywordType="theme">'.$terms[$i]."</keyword>\n");
  }
}
echo("</keywordSet>");
echo("</textarea></form>");



?>
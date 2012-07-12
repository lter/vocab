<head>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>International LTER Data Search</title>
</head>
<body>
<h2>International LTER Data Search</h2>

View search results (Note: it may take a while for each search to complete, so please be patient): 
<ul>
<?php
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/".$_REQUEST['search']."'>Knowledge Network for Biocomplexity</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/".$_REQUEST['search']."?metacat=http://metacat.lternet.edu/knb/metacat'>US LTER</a><br><font size=-2>Note: this search duplicates the search of the KNB because the servers are replicated</font>\n");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/es/narrowrelated/".$_REQUEST['search']."?metacat=http://metacat.iecolab.es/knb/metacat'>Spain LTER</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/ja/narrowrelated/".$_REQUEST['search']."?metacat=http://db.cger.nies.go.jp/JaLTER/metacat/metacat'>JaLTER (search English keywords)</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/".$_REQUEST['search']."?metacat=http://db.cger.nies.go.jp/JaLTER/metacat/metacat'>JaLTER (search Japanese keywords)</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/".$_REQUEST['search']."?metacat=http://myernet.frim.gov.my/FRIM/metacat'>MyERnet Forestry Institute of Malaysia</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/zh-tw/narrowrelat".$_REQUEST['search']."ed/?metacat=http://metacat.tfri.gov.tw/tfri/metacat'>Taiwan Forestry Research Institute (searched using Chinese-traditional keywords)</a>");
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/".$_REQUEST['search']."?metacat=http://metacat.tfri.gov.tw/tfri/metacat'>Taiwan Forestry Research Institute (searched using English keywords)</a>");
	     break;
print("<li><a href='querymetacat.php/".$_REQUEST['lang']."/pt/narrowrelated/".$_REQUEST['search']."?metacat=http://ppbio.inpa.gov.br/knb/metacat'>PPBio Brazil</a>");
?>
</ul>
</body>

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<!-- put the following scripts in the header of your web page -->
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/lib/prototype/prototype.js">
</script> 
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/lib/scriptaculous/scriptaculous.js">
</script> 
<script type="text/javascript" src="http://vocab.lternet.edu/autocomplete/src/AutoComplete.js"></script> 
<!-- End of scripts to be included -->

</head>
<body>
Test ILTER Search: <br>
<?php
# comment out the metacat you don't want to use.....
switch($_REQUEST['metacat']){
	case 'knb': print("<form action='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/' method='post'>");
	     break;
	case 'USLTER': print("<form action='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/?metacat=http://metacat.lternet.edu/knb/metacat' method='post'>");
	     break;
	case 'ES': print("<form action='querymetacat.php/".$_REQUEST['lang']."/es/narrowrelated/?metacat=http://metacat.iecolab.es/knb/metacat' method='post'>");
	     break;
	case 'JA': print("<form action='querymetacat.php/".$_REQUEST['lang']."/ja/narrowrelated/?metacat=http://db.cger.nies.go.jp/JaLTER/metacat/metacat' method='post'>");
	     break;
	case 'JA_EN': print("<form action='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/?metacat=http://db.cger.nies.go.jp/JaLTER/metacat/metacat' method='post'>");
	     break;
	case 'MY_EN': print("<form action='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/?metacat=http://myernet.frim.gov.my/FRIM/metacat' method='post'>");
	     break;
	case 'ZHTW_ZHTW': print("<form action='querymetacat.php/".$_REQUEST['lang']."/zh-tw/narrowrelated/?metacat=http://metacat.tfri.gov.tw/tfri/metacat' method='post'>");
	     break;
	case 'ZHTW_EN': print("<form action='querymetacat.php/".$_REQUEST['lang']."/en/narrowrelated/?metacat=http://metacat.tfri.gov.tw/tfri/metacat' method='post'>");
	     break;
	case 'PT': print("<form action='querymetacat.php/".$_REQUEST['lang']."/pt/narrowrelated/?metacat=http://ppbio.inpa.gov.br/knb/metacat' method='post'>");
	     break;
}
?>

<!-- put these <input> and <script> tags in your form. The "name" and "size" can be set to whatever  -->
<!-- you want, but don't change the "id" or "autocomplete" it won't work -->
<input type="text" id="my_ac3" name="search" size="45" autocomplete="off"/> 

<!-- You can customize the response time and number of characters needed to be typed, below -->
<!-- The accompanying LTERAutoComp.php file needs to be in the same directory as this form -->
<script type="text/javascript">
<?php 
print("
new AutoComplete('my_ac3', './LTERAutoComp.php?m=text&lang=".$_REQUEST['lang']."&s=', { 
 delay: 0.1, 
 threshold: 1,
 resultFormat: AutoComplete.Options.RESULT_FORMAT_TEXT 
});
");
?> 
</script> 
<!-- End of tags to be included -->

<input type=submit>
</body>

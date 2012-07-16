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
<h2>Test ILTER Search of Multiple Repositories<h2>

<form action="searchDisplay.php" method="get">
<!-- put these <input> and <script> tags in your form. The "name" and "size" can be set to whatever  -->
<!-- you want, but don't change the "id" or "autocomplete" it won't work -->
<?php
print("<input type='hidden' name='lang' value='".$_REQUEST['lang']."'>\n");
print("<input type='hidden' name='outlang' value='".$_REQUEST['outlang']."'>\n");
?>
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

<!-- This is a sample form in PHP that uses the LTER HIVE keywording web service -->

<h1>Sample Form</h1>

<form action="http://vocab.lternet.edu/lterhive/LTERhive2list.php" 
      method="post">
<!-- set the "action" to go to the program that you would use to
process the php array of terms. That processing might just be a simple
listing, or it could be insertion into a dataset etc. The sample 
LTERhive2list.php action produces a listing -->

<!-- start using PHP -->
<?php

// set the myPackageId string to the document you want to scan. Here
// we are setting it via a variable returned by a web form or added to
//the URL e.g. 
// http://myserver.edu/sampleHivePHPform.php?packageid=lter-knb-vcr.49

// create variables containing the package ID and web service URL
$myPackageId=$_REQUEST['packageid'];
$webServiceCall='http://vocab.lternet.edu/webservice/findKeywords.php/lter/php/'.$myPackageId ; 

// test to make sure a packageID was provided, otherwise print an
// error message and quit
if(strlen($myPackageId) == 0) exit("error: no packageid was specified<br>
     if this page was accessed directly by a URL, you should add: <br>
     ?packageid=knb-lter-vcr.49 to the URL <br> where knb-lter-vcr.49 
     is the ID of the desired package in the LTER Metacat. 
");


//print and informative message (optional)
echo("Fetching table of terms from:".$webServiceCall."<br>");

// The following invokes the web service to populate the form with a 
// table of terms found, with checkboxes, that store the results in a PHP
// array named "terms[]" and pass it to the program specified in the
// "action" attribute of the <form> tag. 
//  Note, the use of "readfile() to fetch a URL requires that the 
//  PHP "allow_url_fopen" initialization option be set to TRUE
//  your php.ini file. This is usually the default. 

readfile($webServiceCall); 


?> <!-- done using PHP -->
<input type="submit"> <!-- add a submit button to the form -->
</form>
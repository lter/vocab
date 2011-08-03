<?php
//set baseURL to directory for querymetacat.php and LTER_resultset.xsl 
define(BASE_URL,"http://vocab.lternet.edu/metacatsearch/");

// set the serviceURL to the tematres service web address 
define(SERVICE_URL,"http://vocab.lternet.edu/vocab/vocab/services.php");

// set the scope for the search using the start of the packageID
define(PACKAGEID_START,"%");
//define(PACKAGEID_START,"knb-lter-");

// set the string describing the scope of the search
define(SCOPE_DESCR_STRING,"LTER");
?>

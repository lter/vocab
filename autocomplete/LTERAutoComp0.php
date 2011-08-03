<?php
    $s = $_REQUEST['s'];
    $matches = array();
    if(strlen($s) > 0)
    {
// set the $dict file to a list of the words to be presented in the popup
// it can be a local file or a web link
        $dict = file_get_contents('http://vocab.lternet.edu/autocomplete/LTERpf.txt');
        $search = '/^' . preg_replace('/([\W])/i', '\\\\$1', $_REQUEST['s']) . '[^\s]*([\ ]*[^\s]*)*/mi';
        $found = preg_match_all($search, $dict, $matches);
        if($found)
            $matches = $matches[0];
        else
            $matches = array();
    }
    // Case-insensitive sort
    $matches_lowercase = array_map('strtolower', $matches);
    array_multisort($matches_lowercase, SORT_ASC, SORT_STRING, $matches);
    // get rid of duplicates
    $matches = array_unique($matches);
    
    $type = 'text/xml';
    $response = '';
    
    ob_clean();
    
    switch($_REQUEST['m'])
    {
        case 'json':
            $type = "text/plain";
            //
            // You don't have to do both, but Prototype has automatic
            // evaluation support if you use the X-JSON header instead of the body.
            //
            $response = json_encode($matches);
            header("X-JSON: $response");
            break;
        
        case 'text':
            $type = "text/plain";
            $response = join("\r\n", $matches);
            break;
            
        case 'xml':
        default:
            $type = "application/xml";
            
            $dom = new DOMDocument('1.0');
            $root = $dom->createElement('Suggestions');
            for($i = 0; $i < count($matches); $i++)
            {
                $e = $dom->createElement('suggestion', $matches[$i]);
                $root->appendChild($e);
            }
            $dom->appendChild($root);
            $response = $dom->saveXML();
            break;        
    }

    header("Content-Type: $type");
    echo $response;
//echo("Search is:". $search ."\r\n");
//print_r($matches);
//print($dict);
    exit;

?> 
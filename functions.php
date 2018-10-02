<?php

require __DIR__ . '/vendor/autoload.php';


function getCampaign($template){
   
    $campaignFile = $template . 'campaign.json';
    if(!file_exists($campaignFile)){
        $res = new stdClass();
    }
    else{
        p('Usando archivo de campaña ' . $campaignFile);
        $res = json_decode(file_get_contents($campaignFile,true));
    }

    return $res;
}

function getSettings($template){

    $settingsFile = $template . 'settings.json';
    if(!file_exists($settingsFile)){
        p('Falta el archivo settings.json del template (' . $settingsFile . ')',true);    
    }
    else{
        p('Usando archivo de configuración ' . $settingsFile);
    }
    $res = json_decode(file_get_contents($settingsFile,true));

    return $res;
}



function getRecipents($template){

    $recipentsFile = $template . 'recipents.json';
    if(!file_exists($recipentsFile)){
        p('Falta el archivo recipents.json del template (' . $recipentsFile . ')',true);    
    }
    else{
        p('Usando archivo de destinatarios ' . $recipentsFile);
    }
    $res = json_decode(file_get_contents($recipentsFile,true));

        return $res;
}




function dynCampaign($str, $campaign){
    foreach ($campaign as $key => $value) {
        if(!is_array($value)){
            $str = str_replace('%%CAMPAIGN_' . strtoupper($key) . '%%', $value, $str);    
        }
    }
    return $str;
}

function dynRecipent($str, $recipent){
    foreach ($recipent as $key => $value) {
        if(!is_array($value)){
            $str = str_replace('%%USER_' . strtoupper($key) . '%%', $value, $str);    
        }
    }
    return $str;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function startsWith($haystack, $needle){
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle){
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function p($str = '', $isError = false){

    if($isError){
        echo 'ERROR: ' . $str;
        exit();
    }
    else{
        echo $str;
    }

    echo '<br>';

    ob_flush();
	flush();

}

// function fixMySpanish($str) { 
//     return str_replace(     
//         array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ","©","®","&","¿"),
//         array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&copy;","&reg;","&amp;","&iquest;"), 
//         $str
//     );     
// }

?>
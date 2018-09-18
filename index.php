<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Levantando Template del origen que sea

    // $templateName = 'test.zip';
    // $templateName = 'test';
    if(!isset($_REQUEST["template"])){
        p('Debe especificar el origen del mail ?template=',true);    
    }
    $templateName = $_REQUEST["template"];


    if(endsWith($templateName,'zip')){
            
        $templatePath = 'tmp/' . generateRandomString() . '/';

        $zip = new ZipArchive;

        $res = $zip->open($templateName);

        if ($res === TRUE) {
            $zip->extractTo($templatePath);
            $zip->close();
            p('Zip descomprimido a ' . $templatePath);
        } else {
            p('Error al descomprimir Zip ' . $templateName,true);
        }

    }
    else{

        $templatePath = 'mails/' . $templateName . '/';

        p('Usando template en la ruta ' . $templatePath);

    }
    
    $htmlRaw = file_get_contents($templatePath . 'index.html');

    // $htmlRaw = htmlspecialchars($htmlRaw);
    // $html .= "<h1>Test</h1><p>Esta es una imagen: <img src=\"cid:testimgid\" /></p>";


    sleep(1);
   



// ARCHIVO DE CONFIGURACION SETTINGS

    $settingsFile = $templatePath . 'settings.json';
    if(!file_exists($settingsFile)){
        p('Falta el archivo settings.json del template (' . $settingsFile . ')',true);    
    }
    else{
        p('Usando archivo de configuración ' . $settingsFile);
    }
    $settings = json_decode(file_get_contents($settingsFile,true));
    // var_dump($settings);



// ARCHIVO DE CAMPAÑA

    $campaignFile = $templatePath . 'campaign.json';
    if(!file_exists($campaignFile)){
        $campaign = new stdClass();
    }
    else{
        p('Usando archivo de campaña ' . $campaignFile);
        $campaign = json_decode(file_get_contents($campaignFile,true));
    }
    // var_dump($campaign);



// ARCHIVO DE DESTINATARIOS

    $recipentsFile = $templatePath . 'recipents.json';
    if(!file_exists($recipentsFile)){
        p('Falta el archivo recipents.json del template (' . $recipentsFile . ')',true);    
    }
    else{
        p('Usando archivo de destinatarios ' . $recipentsFile);
    }
    $recipents = json_decode(file_get_contents($recipentsFile,true));
    // var_dump($recipents);


// PREPARAMOS MAIL

    $mail = new PHPMailer();

    $mail->CharSet = 'UTF-8';

    if(isset($settings->smtp)){
        $mail->IsSMTP();
        $mail->Host = $settings->smtp->host;
        $mail->SMTPAuth = $settings->smtp->auth;
        $mail->Username = $settings->smtp->username;
        $mail->Password = $settings->smtp->password;
    }

    $mail->From = $settings->from->email;
    $mail->FromName = $settings->from->name;
    $mail->Sender = $settings->from->email;


    $mail->AddReplyTo($settings->reply->email, $settings->reply->name);

    if(isset($settings->cc)){
        foreach ($settings->cc as $key => $value) {
            $mail->AddCC($value->email, $value->name);
        }
    }

    if(isset($settings->bcc)){
        foreach ($settings->bcc as $key => $value) {
            $mail->AddBCC($value->email, $value->name);
        }
    }


// COMENZANDO ENVIOS

    foreach ($recipents as $key => $recipent) {

        p();
        p('Preparando mail para  ' . $recipent->email);

        $mail->ClearAddresses();
        $mail->AddAddress($recipent->email, $recipent->name);

        if(isset($settings->body->html) && $settings->body->html){
            $mail->IsHTML($settings->body->html);
            $mail->AltBody=dynRecipent($settings->body->alt, $recipent);
        }

        $subject = dynRecipent($campaign->subject, $recipent);
        $subject = dynCampaign($subject, $campaign);
        $mail->Subject = $subject;

        // MAGIA 1 buscar imagenes linkeadas en el HTML y embeberlas al mail, reemplazando tambien la imagen en el mismo HTML.
        $html = $htmlRaw;
        if(isset($settings->body->autoEmbedImages) && $settings->body->autoEmbedImages){
            preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $html, $images ); 
            // var_dump($images[1]);
            foreach ($images[1] as $key => $value) {    
                $imagePath = $templatePath . $value;

                if(file_exists($imagePath)){
                    $imageKey = 'reb' . $key;

                    p("Procesando imagen " . $imagePath . " como " . $imageKey);

                    $mail->AddEmbeddedImage($imagePath, $imageKey , $imagePath); 

                    $html = str_replace($value,"cid:". $imageKey ,$html);

                }

            }
        }    

        $html = dynRecipent($html, $recipent);
        $html = dynCampaign($html, $campaign);
        // $html = fixMySpanish($html); // los que venian mal los arregla, pero los que venian bien los rompe. Mejor usar el CharSet del phpmailer        
        $mail->Body = $html;


        p('Enviando mail a  ' . $recipent->email);

        
        if(!$mail->Send()){
            p("Error al enviar: " . $mail->ErrorInfo,true);
        }
        else{
            p("Mail enviado!");
        }
        
        sleep(0.5);

    }




///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////


function dynCampaign($str, $campaign){
    foreach ($campaign as $key => $value) {
        $str = str_replace('%%CAMPAIGN_' . strtoupper($key) . '%%', $value, $str);    
    }
    return $str;
}

function dynRecipent($str, $recipent){
    foreach ($recipent as $key => $value) {
        $str = str_replace('%%USER_' . strtoupper($key) . '%%', $value, $str);    
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

function fixMySpanish($str) { 
    return str_replace(     
        array("á","é","í","ó","ú","ñ","Á","É","Í","Ó","Ú","Ñ","©","®","&","¿"),
        array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&Ntilde;","&copy;","&reg;","&amp;","&iquest;"), 
        $str
    );     
}

?>
<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Levantando Template del origen que sea

if(!isset($_REQUEST["template"])){
    p('Debe especificar el origen del mail ?template=',true);    
}
$templateName = $_REQUEST["template"];


// if(endsWith($templateName,'zip')){
        
//     $templatePath = 'tmp/' . generateRandomString() . '/';

//     $zip = new ZipArchive;

//     $res = $zip->open($templateName);

//     if ($res === TRUE) {
//         $zip->extractTo($templatePath);
//         $zip->close();
//         p('Zip descomprimido a ' . $templatePath);
//     } else {
//         p('Error al descomprimir Zip ' . $templateName,true);
//     }

// }
// else{

    $templatePath = 'mails/' . $templateName . '/';

    p('Usando template en la ruta ' . $templatePath);

// }

$htmlRaw = file_get_contents($templatePath . 'index.html');

sleep(0.25);

// ARCHIVO DE CONFIGURACION SETTINGS
$settings = getSettings($templatePath);

// ARCHIVO DE CAMPAÑA
$campaign = getCampaign($templatePath);

// ARCHIVO DE DESTINATARIOS
$recipents = getRecipents($templatePath);



// PREPARAMOS MAIL

$mail = new PHPMailer();

$mail->CharSet = 'UTF-8';

if(isset($settings->smtp)){
    $mail->IsSMTP();
    $mail->Host = $settings->smtp->host;
    $mail->SMTPAuth = $settings->smtp->auth;
    $mail->Username = $settings->smtp->username;
    $mail->Password = $settings->smtp->password;
    $mail->Port = $settings->smtp->port;
    $mail->SMTPSecure = $settings->smtp->security;
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

    // adjuntos de la campaña (no poner debajo de magia de embeds porque me limpia imagenes)
    $mail->clearAttachments();
    if(isset($campaign->attachs)){
        foreach ($campaign->attachs as $key => $value) {                
            $mail->addAttachment($templatePath .'attachs/' . $value->file, $value->name); 
        }
    }

    if(isset($recipent->attachs)){
        foreach ($recipent->attachs as $key => $value) {                
            $mail->addAttachment($templatePath .'attachs/' . $value->file, $value->name); 
        }
    }

    if(isset($settings->body->html) && $settings->body->html){
        $mail->IsHTML($settings->body->html);            
    }

    if(isset($campaign->subject)){
        $subject = dynRecipent($campaign->subject, $recipent);
        $subject = dynCampaign($subject, $campaign);
        $mail->Subject = $subject;
    }
    else{
        p("La campaña no tiene asunto",true);
    }

    if(isset($campaign->preview)){
        $mail->AltBody=dynRecipent($campaign->preview, $recipent);
    }
    

    // MAGIA 1 buscar imagenes linkeadas en el HTML y embeberlas al mail, reemplazando tambien la imagen en el mismo HTML.
    $html = $htmlRaw;
    if(isset($settings->body->autoEmbedImages) && $settings->body->autoEmbedImages){
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $html, $images ); 
        
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

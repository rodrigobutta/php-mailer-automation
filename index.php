<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';

$mails = array_filter(glob('mails/*'), 'is_dir');

foreach ($mails as $key => $mail) {
    
    // var_dump($mail);
    $campaign = getCampaign($mail);

    var_dump($campaign);
}
    
    


print_r($mails);
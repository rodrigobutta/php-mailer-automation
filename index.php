<?php

$dirs = array_filter(glob('mails/*'), 'is_dir');

print_r($dirs);
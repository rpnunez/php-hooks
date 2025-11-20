<?php

//#########################################
// Initialize Hooker
require('Hooker.php');
Hooker::setHookFolderPath(__DIR__ . '/hooks');
Hooker::init();
echo '<pre><p>Hooker::$events Array</p>'.print_r(Hooker::$events, true).'</pre>';
//#########################################

$x = 'The answer is 42.';
Hooker::event('userLogInAttempt', array('x' => $x));

// Console/logs
Hooker::consolePrint();

// Just adding
// some dummy
// code
echo 'hey';

//#########################################
// Clean/garbage collect Hooker
Hooker::collectGarbage();
//#########################################

?>
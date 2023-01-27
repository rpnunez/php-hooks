<?php

/**
 * App
 * @author Raymond Nunez <raypn93@gmail.com>
 */

//#########################################
// Initialize Hooker
require('Hooker.php');
Hooker::setHookFolderPath('C:\Program Files (x86)\EasyPHP-12.1\www\Hooker\Hooker_v2\hooks');
Hooker::init();
echo '<pre><p>Hooker::$events Array</p>'.print_r(Hooker::$events, true).'</pre>';
//#########################################

$x = 'The answer is 42.';
Hooker::event('userLogInAttempt', array('x' => $x));

// Console/logs
Hooker::consolePrint();

/**
 * d
 * d
 * d
 * dde
 * e
 * 
 * ee
 * 
 */

echo '<p>hello world</p>';

echo "<p>yeah man</p>";

// Just adding
// some dummy
// code
echo 'hey';

//#########################################
// Clean/garbage collect Hooker
Hooker::collectGarbage();
//#########################################

?>
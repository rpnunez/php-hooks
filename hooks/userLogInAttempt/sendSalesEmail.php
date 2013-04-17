<?php

class sendSalesEmail implements Hook {
    public $name = 'sendSalesEmail';
    public static $event = 'userLogInAttempt';

    /*function __construct() {
        Hooker::bind($this, 'userLogInAttempt.before');
    }*/

    function execute($use) {
        //@mail('raymond@inreact.com', 'Hello, world!', 'Lorem ipsum...');
        echo 'Hello, world!';
        echo '<br />';
        echo $use['x'];
        var_dump($use);
    }
}

?>
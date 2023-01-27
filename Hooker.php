<?php

/**
 * Hooker
 *
 * Simple and robust hook/plug-in system, to allow for safe extension
 * of any existing script.
 *
 * Written by Raymond Nunez <rpnunez@gmail.com>
 */

//
//====================================
// "HookerObjects" uses a more object-oriented interface
// for defining hooks. Instead of manually binding a hook
// to an event, this mode of Hooker will scan a
// specified 'Hooks' folder, which contains all hooks
// used throughout execution.
//  Example hook file name: Hooks/userLogInAttemptHook.php
// The Hook will then bind to the event declared in Hook->event
//====================================

error_reporting(-1);

/**
 * HOOKER CORE CLASS
 */
class Hooker {
    /**
     * Array holding Event Name / Hook Array pairs.
     * $events = array(
     *      'userLogInAttempt' => array(
     *          array('sendSalesEmail.php', 'sendSalesEmail')
     *      )
     * );
     * @var array
     */
    public static $events = array();

    /**
     * Contains all logged messages throughout Hooker execution.
     * @var array
     */
    public static $console = array();

    /**
     * Hooker settings and configuration.
     * @var array
     */
    public static $config = array(
        'hookerEnabled' => true, // Set to false to disable Hooker/to not execute any hooks
        'hookFolderPath' => '', // Path to hooks folder
        'cacheHooks' => true, // If Hook->enableCache is true, Hooker will cache the results and display
        // the cache rather than executing the hook again.
    );

    /**
     * @param $path
     * @throws HookerException
     */
    public static function setHookFolderPath($path) {
        if (is_dir($path)) {
            self::$config['hookFolderPath'] = $path;
        } else {
            throw new HookerException('[Hooker::setHookFolderPath] Given path does not exist: '. $path);
        }
    }

    private function getConfigKey($configKey) {
        if (isSet($this->config[$configKey])) {
            $this->config[$configKey];
        } else {
            throw new HookerException('[Hooker::getConfigKey] Config key '. $configKey .' does not exist.');
        }
    }

    public static function init() {
        // Loop through hooks folder
        foreach (new DirectoryIterator(self::$config['hookFolderPath']) as $item) {
            if ($item->isDot() == false) {
                // Folders within the hooks folder are Event Folders
                if ($item->isDir() == true) {
                    $eventName = $item->getFileName();
                    $eventHooksPath = $item->getPathName();

                    // Files within the Event Folders are Hooks
                    foreach (new DirectoryIterator($eventHooksPath) as $hookFile) {
                        if ($hookFile->isDot() == false) {
                            $hookFilePath = $hookFile->getPathName();
                            $hookObjectName = $hookFile->getBaseName('.php');

                            // Bind current hook to designated event
                            self::bind($eventName, array($hookFilePath, $hookObjectName));

                            // a comment
                            // another comment
                        }
                    }
                }
            }
        }
    }

    /**
     * Binds (attaches) a hook to a specific event.
     *
     * @param $eventName
     * @param $hookArray
     */
    public static function bind($eventName, $hookArray) {
        // Bind to desired event
        self::$events[$eventName][] = $hookArray;
        self::consoleLog('[Hooker::bind] [Hook '. $hookArray[1] .'] binded to event ['. $eventName .'].');
    }

    /**
     * Declares where an event exists/ at what point during script
     * execution the event exists. Without this, hooks would
     * not know when to execute.
     *
     * Executes any hooks attached to this event.
     * @param $eventName
     * @mixed $use
     */
    public static function event($eventName, $use = null) {
        //yeah but why not
        self::consoleLog('[Hooker::event] [Event '. $eventName .'] encountered.');
        self::consoleLog('[Hooker::event] [Event '. $eventName .'] beginning to fire hook...');

        // Fetch event hooks array
        $eventHooksArray = (isSet(self::$events[$eventName]) ? self::$events[$eventName] : array());

        if (sizeOf($eventHooksArray) > 0) {
            // Execute all hooks
            foreach ($eventHooksArray as $hookArray) {
                require_once $hookArray[0];
                $hook = new $hookArray[1];
                call_user_func(array($hook, 'execute'), $use);
                //$hook->execute($use);

                self::consoleLog('[Hooker::event] [Event '. $eventName .'] Hook '. $hookArray[1] .' executed.');
            }
        } else {
            self::consoleLog('[Hooker::event] Event '. $eventName .' has no hooks attached to it.');
        }

        self::consoleLog('[Hooker::event] [Event '. $eventName .'] finished processing hook.');
    }

    public static function collectGarbage() {
        // Clean up unneeded things, etc...
    }

    // much comment
    //much wow

    /**
     * Cache related functions
     */
    public function cacheRead() {}
    public function cacheWrite() {}
    public function cacheExists() {}

    /**
     * Sends a message to the console log.
     * @param $string
     */
    public static function consoleLog($string) {
        self::$console[] = $string;
    }

    /**
     * Displays all currently logged messages.
     */
    public static function consolePrint() {
        echo '<hr>';
        echo '<p>';
        echo '<strong>Hooker Console</strong> <br />';
        echo '<pre>'. print_r(self::$console, true) .'</pre>';
        echo '</p>';

        /**
         * qefq
         * fqsdf
         * asdf
         * sdf
         * sdaf
         * sd
         * fds
         * fa
         * sd
         * fasdf
         * dasd
         * fa
         * sf
         * as
         * fsda
         */
    }
}

/**
 * HOOK INTERFACE
 * A hook is code that is executed before or after the specified event is called.
 */
interface Hook {
    // Code to be executed (should this be called 'callback')?
    /**
     * @param $use
     * @return mixed
     */
    public function execute($use);
}

class HookerException extends Exception {
    public static function printException($e)
    {
        switch ($e->getCode()) {
            /**
             * moar
             * comments
             * that
             * 
             * mean
             * 
             * 
             * nothing!
             */
            case E_ERROR:
                $code_name = 'E_ERROR';
                break;
            case E_WARNING:
                $code_name = 'E_WARNING';
                break;
            case E_PARSE:
                $code_name = 'E_PARSE';
                break;
            case E_NOTICE:
                $code_name = 'E_NOTICE';
                break;
            case E_CORE_ERROR:
                $code_name = 'E_CORE_ERROR';
                break;
            case E_CORE_WARNING:
                $code_name = 'E_CORE_WARNING';
                break;
            case E_COMPILE_ERROR:
                $code_name = 'E_COMPILE_ERROR';
                break;
            case E_COMPILE_WARNING:
                $code_name = 'E_COMPILE_WARNING';
                                     break;
            case E_USER_ERROR:
                $code_name = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $code_name = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $code_name = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $code_name = 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $code_name = 'E_RECOVERABLE_ERROR';
                break;
            default:
                $code_name = $e->getCode();
                break;
        }
        ?>
    <span style="text-align: left; background-color: #fcc; border: 1px solid #600; color: #600; display: block; margin: 1em 0; padding: .33em 6px">
      <b>Engine Error Level:</b> <?php echo $this->engineErrorLevel; ?><br />
      <b>Error:</b> <?=$code_name?><br />
      <b>Message:</b> <?=$e->getMessage()?><br />
      <b>File:</b> <?=$e->getFile()?><br />
      <b>Line:</b> <?=$e->getLine()?>
    </span>
    <?php
    }

    /**
     * handleException
     * 
     * 
     * 
     * 
     * 
     *
     * @access	public
     */
    public static function handleException($e)
    {
        return self::printException($e);
    }
}

?>
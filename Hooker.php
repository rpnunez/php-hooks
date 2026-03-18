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
    public static $events = [];

    /**
     * Contains all logged messages throughout Hooker execution.
     * @var array
     */
    public static $console = [];

    /**
     * Hooker settings and configuration.
     * @var array
     */
    public static $config = [
        'hookerEnabled' => true, // Set to false to disable Hooker/to not execute any hooks
        'hookFolderPath' => '', // Path to hooks folder
        'cacheHooks' => true, // If Hook->enableCache is true, Hooker will cache the results and display
        // the cache rather than executing the hook again.
    ];

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
        if (isset($this->config[$configKey])) {
            return $this->config[$configKey];
        }

        throw new HookerException('[Hooker::getConfigKey] Config key '. $configKey .' does not exist.');
    }

    public static function init() {
        // Loop through hooks folder
        foreach (new DirectoryIterator(self::$config['hookFolderPath']) as $item) {
            if (!$item->isDot()) {
                // Folders within the hooks folder are Event Folders
                if ($item->isDir()) {
                    $eventName = $item->getFileName();
                    $eventHooksPath = $item->getPathName();

                    // Files within the Event Folders are Hooks
                    foreach (new DirectoryIterator($eventHooksPath) as $hookFile) {
                        if (!$hookFile->isDot()) {
                            $hookFilePath = $hookFile->getPathName();
                            $hookObjectName = $hookFile->getBaseName('.php');

                            // Bind current hook to designated event
                            self::bind($eventName, [$hookFilePath, $hookObjectName]);
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
        self::consoleLog('[Hooker::event] [Event '. $eventName .'] encountered.');

        // Fetch event hooks array
        $eventHooksArray = isset(self::$events[$eventName]) ? self::$events[$eventName] : [];

        if (count($eventHooksArray) > 0) {
            $hooksToExecute = [];

            // Prepare and sort hooks
            foreach ($eventHooksArray as $hookArray) {
                if (file_exists($hookArray[0])) {
                    require_once $hookArray[0];
                }

                $className = $hookArray[1];
                $priority = 50; // Default priority

                if (class_exists($className)) {
                    $vars = get_class_vars($className);
                    if (isset($vars['priority'])) {
                        $priority = $vars['priority'];
                    }
                }

                $hooksToExecute[] = [
                    'hookArray' => $hookArray,
                    'priority' => $priority
                ];
            }

            // Sort by priority (ascending)
            usort($hooksToExecute, function($a, $b) {
                if ($a['priority'] == $b['priority']) {
                    return 0;
                }
                return ($a['priority'] < $b['priority']) ? -1 : 1;
            });

            // Execute all hooks in order
            foreach ($hooksToExecute as $item) {
                $hookArray = $item['hookArray'];
                $className = $hookArray[1];

                if (class_exists($className)) {
                    $hook = new $className;
                    call_user_func([$hook, 'execute'], $use);
                    //$hook->execute($use);

                    self::consoleLog('[Hooker::event] [Event '. $eventName .'] Hook '. $className .' executed.');
                }
            }
        } else {
            self::consoleLog('[Hooker::event] Event '. $eventName .' has no hooks attached to it.');
        }
    }

    public static function collectGarbage() {
        // Clean up unneeded things, etc...
    }

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
     * @access	public
     */
    public static function handleException($e)
    {
        return self::printException($e);
    }
}

?>
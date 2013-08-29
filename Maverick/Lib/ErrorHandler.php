<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class ErrorHandler {
    /**
     * The error handler method
     *
     * @param  intenger $number
     * @param  string   $message
     * @param  string   $file
     * @param  integer  $line
     * @return null
     */
    public static function handleError($number, $message, $file, $line) {
        $errorFile = MAVERICK_PATH . 'ErrorTemplates/PHPError.html';

        if(file_exists($errorFile) && \Maverick\Lib\Environment::lessThan('PROD') && \Maverick\Maverick::getConfig('Environment')->get('display_errors')) {
            print sprintf(file_get_contents($errorFile), $number, $message, $file, $line);
        } else {
            \Maverick\Lib\Router::loadController('Errors_500')
                ->printOut();
        }

        if(\Maverick\Maverick()->getConfig('System')->get('email_errors')) {
            self::sendEmail($number, $message, $file, $line);
        }

        exit;
    }

    /**
     * Handles exceptions
     *
     * @param  Exception $e
     * @return null
     */
    public static function handleException($e) {
        self::handleError($e->getCode(), 'There was an uncaught exception:<br /><br />' . $e->getMessage(), $e->getFile(), $e->getLine());
    }

    /**
     * Sends an email to the administrator alerting them to this error
     *
     * @param  integer $number
     * @param  string  $message
     * @param  string  $file
     * @param  integer $line
     * @return null
     */
    private static function sendEmail($number, $message, $file, $line) {
        $message = '<span style="font-size:16px;">
This is an automatic email sent to detail an error which occurred on ' . date('r') . '<br />
<br />
The email below describes the error.<br />
<br />
Error Text: <b>' . $message . '</b><br />
Error File: <b>' . $file . '</b><br />
Error Line: <b>' . $line . '</b>
</span>';

        mail(\Maverick\Maverick()->getConfig('System')->get('admin_email'),
             'There was an Error',
             $message,
             'Content-type: text/html;' . "\r\n");
    }
}
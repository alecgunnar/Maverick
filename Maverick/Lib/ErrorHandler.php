<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class ErrorHandler {
    /**
     * The default error template
     *
     * @const string DEFAULT_ERROR_TEMPLATE
     */
    const DEFAULT_ERROR_TEMPLATE = <<<EOF
<style>

</style>
<h1>Error!</h1>
Something happened, and we were unable to complete your request.
EOF;

    /**
     * The error handler method
     *
     * @param  intenger $number
     * @param  string   $message
     * @param  string   $file
     * @param  integer  $line
     * @return null
     */
    public static function HandelError($number, $message, $file, $line) {
        $errorFile = MAVERICK_PATH . 'ErrorTemplates/PHPError.html';

        if(file_exists($errorFile) && \Maverick\Lib\Environment::getInstance()->lessThan('PROD')) {
            print sprintf(file_get_contents($errorFile), $number, $message, $file, $line);
        } else {
            print self::DEFAULT_ERROR_TEMPLATE;
        }

        if(\Maverick\Maverick()->getConfig('System')->get('site')->get('email_errors')) {
            self::sendEmail($number, $message, $file, $line);
        }

        exit;
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

        mail(\Maverick\Maverick()->getConfig('System')->get('site')->get('admin_email'),
             'There was an Error',
             $message,
             'Content-type: text/html;' . "\r\n");
    }
}
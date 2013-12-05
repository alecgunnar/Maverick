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
     * @param intenger $number
     * @param string   $message
     * @param string   $file
     * @param integer  $line
     * @param boolean  $isException
     */
    public static function handleError($number, $message, $file, $line, $isException=false, $stackTrace=array()) {
        if(\Maverick\Maverick::getConfig('Environment')->get('email_errors')) {
            self::sendEmail($message, $file, $line);
        }

        $errorFile = MAVERICK_PATH . 'ErrorTemplates/PHPError.html';

        if(file_exists($errorFile) && \Maverick\Lib\Environment::lessThan('PROD') && \Maverick\Maverick::getConfig('Environment')->get('display_errors')) {
            $stackTrace = $stackTrace ?: debug_backtrace();
            $trace      = '';

            //_dump($stackTrace);

            foreach($stackTrace as $n => $data) {
                $line     = '';
                $function = '';
                $file     = '';

                if(array_key_exists('line', $data)) {
                    $line = $data['line'];
                }

                if(array_key_exists('function', $data)) {
                    $function = $data['function'];
                }

                if(array_key_exists('file', $data)) {
                    $file = $data['file'];
                }

                $trace .= '<tr><td>' . $line . '</td><td>' . $function . '</td><td>' . $file . '</td></tr>';
            }

            printf(file_get_contents($errorFile), $number, $message, $file, $line, $trace);
        } else {
            \Maverick\Lib\Router::loadController('Errors_500')
                ->printOut();
        }

        exit;
    }

    /**
     * Handles exceptions
     *
     * @param  Exception $e
     */
    public static function handleException($e) {
        self::handleError($e->getCode(), 'There was an uncaught exception:<br /><br />' . $e->getMessage(), $e->getFile(), $e->getLine(), true, debug_backtrace());
    }

    /**
     * Sends an email to the administrator alerting them to this error
     *
     * @param  string  $message
     * @param  string  $file
     * @param  integer $line
     */
    private static function sendEmail($message, $file, $line) {
        $time = new \DateTime(null, new \DateTimeZone('Europe/London'));

        $message = 'This is an automatic email sent to detail an error which occurred on ' . $time->format(\DateTime::ISO8601) . '

The email below describes the error.

Error Text: ' . $message . '
Error File: ' . $file . '
Error Line: ' . $line;

        error_log($message, 1, \Maverick\Maverick::getConfig('System')->get('admin_email'));
    }
}
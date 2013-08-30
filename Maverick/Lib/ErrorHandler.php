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
        if(\Maverick\Maverick::getConfig('Environment')->get('email_errors')) {
            self::sendEmail($message, $file, $line);
        }

        $errorFile = MAVERICK_PATH . 'ErrorTemplates/PHPError.html';

        if(file_exists($errorFile) && \Maverick\Lib\Environment::lessThan('PROD') && \Maverick\Maverick::getConfig('Environment')->get('display_errors')) {
            print sprintf(file_get_contents($errorFile), $number, $message, $file, $line);
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
     * @return null
     */
    public static function handleException($e) {
        self::handleError($e->getCode(), 'There was an uncaught exception:<br /><br />' . $e->getMessage(), $e->getFile(), $e->getLine());
    }

    /**
     * Sends an email to the administrator alerting them to this error
     *
     * @param  string  $message
     * @param  string  $file
     * @param  integer $line
     * @return null
     */
    private static function sendEmail($message, $file, $line) {
        $message = 'This is an automatic email sent to detail an error which occurred on ' . date('r') . '

The email below describes the error.

Error Text: ' . $message . '
Error File: ' . $file . '
Error Line: ' . $line;

        $smtpConf  = \Maverick\Maverick::getConfig('SMTP');
        $toAndFrom = array(\Maverick\Maverick::getConfig('System')->get('admin_email') => 'Administrator');

        $email = \Swift_Message::newInstance()
            ->setSubject('An Error Occurred!')
            ->setFrom($toAndFrom)
            ->setTo($toAndFrom)
            ->setBody($message);

        $transport = \Swift_SMTPTransport::newInstance($smtpConf->get('server'), $smtpConf->get('port'))
            ->setUsername($smtpConf->get('username'))
            ->setPassword($smtpConf->get('password'));

        $mailer = \Swift_Mailer::newInstance($transport);

        $mailer->send($email);
    }
}
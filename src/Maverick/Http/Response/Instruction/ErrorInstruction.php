<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http\Response\Instruction;

use Maverick\Http\Response,
    Maverick\Exception\InvalidValueException,
    Maverick\Controller\ErrorController;

class ErrorInstruction implements InstructionInterface {
    /**
     * Redriect response code
     *
     * @var int
     */
    protected $code;

    /**
     * Valid redirect response codes
     *
     * @var array
     */
    protected $codes = [
        // Client Errors
        400 => true,
        401 => true,
        402 => true,
        403 => true,
        404 => true,
        405 => true,
        407 => true,
        408 => true,
        409 => true,
        410 => true,
        411 => true,
        412 => true,
        413 => true,
        414 => true,
        415 => true,
        416 => true,
        417 => true,

        // Server Errors
        500 => true,
        501 => true,
        502 => true,
        503 => true,
        504 => true,
        505 => true
    ];

    /**
     * Constructor
     *
     * @throws Maverick\Exception\InvalidValueException
     * @param  int $code
     */
    public function __construct($code) {
        if(!isset($this->codes[$code])) {
            throw new InvalidValueException($code . ' is not a valid error response code.');
        }

        $this->code = (int)$code;
    }

    /**
     * The factory method
     */
    public static function factory($code=500) {
        return new self($code ?: 500);
    }

    /**
     * Modifies the response
     *
     * @codeCoverageIgnore
     * @param Maverick\Http\Response              $response
     * @param Maverick\Controller\ErrorController $controller
     */
    public function instruct(Response $response, ErrorController $controller=null) {
        $response->setStatus($this->code);

        if($controller) {
            $response->setBody($controller->delegateError($this->code));
        } else {
            $errorMsg = $this->code . ' - ' . $response->getStatusCodes()[$this->code];
            $response->setBody('<!DOCTYPE html><html><head><title>' . $errorMsg . '</title></head><body><h1>There was an error</h1><p>' . $errorMsg . '</p></body></html>');
        }

        $response->send();
    }
}
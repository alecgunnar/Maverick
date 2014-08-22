<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http\Response\Instruction;

use Maverick\Http\Response,
    Maverick\Exception\InvalidTypeException,
    Maverick\Exception\InvalidValueException;

class RedirectInstruction implements InstructionInterface {
    /**
     * The URI to redirect to
     *
     * @var string
     */
    protected $uri;

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
        300 => true,
        301 => true,
        302 => true,
        303 => true,
        304 => true,
        305 => true,
        307 => true
    ];

    /**
     * Constructor
     *
     * @throws Maverick\Exception\InvalidTypeException
     * @throws Maverick\Exception\InvalidValueException
     * @param  string $uri
     * @param  int    $code
     */
    public function __construct($uri, $code=303) {
        if(!is_string($uri)) {
            throw new InvalidTypeException(__METHOD__, 1, ['string'], $uri);
        }

        if(!is_numeric($code)) {
            throw new InvalidTypeException(__METHOD__, 2, ['numeric'], $code);
        }

        if(!isset($this->codes[$code])) {
            throw new InvalidValueException($code . ' is not a valid redirect response code.');
        }

        $this->uri  = $uri;
        $this->code = (int)$code;
    }

    /**
     * Modifies the response
     *
     * @codeCoverageIgnore
     * @param Maverick\Http\Response $response
     */
    public function instruct(Response $response) {
        $response->setStatus($this->code);
        $response->setHeader('Location', $this->uri);

        if($this->code == 304) {
            $response->setBody('');
        }

        $response->send();
    }
}
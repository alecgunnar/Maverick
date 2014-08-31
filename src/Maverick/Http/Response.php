<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\Application,
    Maverick\DataStructure\ArrayList,
    Maverick\Exception\InvalidTypeException,
    Maverick\Exception\InvalidValueException,
    Maverick\Exception\UnavailableMethodException,
    Maverick\Http\Session\Cookie;

class Response {
    /**
     * The request being responded to
     *
     * @var Maverick\Http\Request
     */
    private $request;

    /**
     * The session of the request
     *
     * @var Maverick\Http\Session
     */
    private $session;

    /**
     * A list of http status codes
     *
     * @var array
     */
    private $statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP version not supported'
    ];

    /**
     * Statuses that do not require a message body
     *
     * @var array
     */
    private $noMessageBody = [
        204 => true,
        304 => true
    ];

    /**
     * The status of the response
     *
     * @var int
     */
    private $status = 200;

    /**
     * The headers for the response
     *
     * @var Maverick\DataStructure\ArrayList
     */
    private $headers;

    /**
     * The body of the response
     *
     * @var string
     */
    private $body;

    /**
     * Constructor
     *
     * @param Maverick\Http\Request $request
     */
    public function __construct(Request $request, Session $session) {
        $this->request = $request;
        $this->session = $session;
        $this->headers = new ArrayList(['Content-type: text/html']);

        if(Application::getConfig('system')->get('expose_maverick') !== false) {
            $this->headers->add('X-Framework: Maverick/' . Application::VERSION);
        }
    }

    /**
     * Sets the status code for the response
     *
     * @throws Maverick\Exception\InvalidValueException
     * @param  int $code
     */
    public function setStatus($code) {
        if(!isset($this->statusCodes[(int)$code])) {
            throw new InvalidValueException($code . ' is not a valid status code.');
        }

        $this->status = (int)$code;
    }

    /**
     * Gets the status code
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Get all of the possible status codes
     *
     * @return array
     */
    public function getStatusCodes() {
        return $this->statusCodes;
    }

    /**
     * Sets a header
     *
     * @param string $name
     * @param string $value=null
     */
    public function setHeader($name, $value=null) {
        if(is_string($name)) {
            if(!is_string($value)) {
                throw new InvalidTypeException(__METHOD__, 2, ['string'], $value);
            }

            $this->headers->add($name . ': ' . $value);
        } elseif(is_array($name)) {
            foreach($name as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            throw new InvalidTypeException(__METHOD__, 1, ['string', 'array'], $name);
        }
    }

    /**
     * Gets the headers map
     *
     * @return Maverick\DataStructure\Map
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Sets the body of the response
     *
     * @param string $content
     */
    public function setBody($content) {
        if(!is_string($content)) {
            throw new InvalidTypeException(__METHOD__, 1, ['string'], $content);
        }

        $this->body = $content;
    }

    /**
     * Gets the body of the response
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Sends the response
     *
     * @codeCoverageIgnore
     * @throws Maverick\Exception\UnavailableMethodException
     */
    public function send() {
        if(Application::debugCompare('==', Application::DEBUG_LEVEL_TEST)) {
            return;
        }

        if(headers_sent()) {
            throw new UnavailableMethodException('The headers have already been sent.');
        }

        http_response_code($this->status);

        foreach($this->headers as $header) {
            header($header);
        }

        foreach($this->session->getNewCookies() as $cookie) {
            header('Set-Cookie: ' . (string)$cookie);
        }

        if($this->body && !isset($this->noMessageBody[$this->status])) {
            print $this->body;
        }

        exit();
    }
}

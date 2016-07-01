<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <aleccarpenter@quickenloans.com>
 */
declare(strict_types=1);

namespace Maverick\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Utility\Renderer\RendererInterface;

abstract class AbstractController
{
    /**
     * @var ServerRequestInterface $request
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $respose
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $this->request  = $request;
        $this->response = $response;

        $callAction = function ($which) {
            $ret = $this->{$which . 'Next'}();
            return ($ret instanceof ResponseInterface) ? $ret : $this->response;
        };

        $response = $callAction('before');
        $response = $next($request, $response);
        $response = $callAction('after');

        return $response;
    }

    /**
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param string $template
     * @param mixed[] $variables
     */
    protected function render(string $template, array $variables = [])
    {
        $rendered = $this->renderer->render($template, $variables);

        $this->response->getBody()
            ->write($rendered);
    }

    /**
     * @param string $output
     * @param mixed[] $variables
     */
    protected function print(string $output)
    {
        $this->response->getBody()
            ->write($output);
    }

    /**
     * @param string $location
     */
    protected function seeOther(string $location)
    {
        $this->response = $this->response->setStatus(303)
            ->withHeader('Location', $location);
    }

    /**
     * @param string $location
     */
    protected function movedPermanently(string $location)
    {
        $this->response = $this->response->setStatus(301)
            ->withHeader('Location', $location);
    }

    /**
     * @return ResponseInterface|null
     */
    protected function beforeNext()
    {
        return $this->response;
    }

    /**
     * @return ResponseInterface|null
     */
    protected function afterNext()
    {
        return $this->response;
    }
}

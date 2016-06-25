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
     * @var string[]
     */
    protected $params;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $respose
     * @param string[] $params
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $params
    ): ResponseInterface {
        $this->request  = $request;
        $this->response = $response;
        $this->params   = $params;

        $ret = $this->doAction();

        return ($ret instanceof ResponseInterface) ? $ret : $this->response;
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
        $this->response->getBody()->write($rendered);
    }

    /**
     * The action of the controller
     *
     * @return ResponseInterface|null
     */
    abstract protected function doAction();
}

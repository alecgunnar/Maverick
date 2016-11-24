<?php

namespace Maverick\View\Twig;

use Maverick\View\ViewInterface;
use Twig_Environment;

abstract class TwigView implements ViewInterface
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param Twig_Environment $env
     */
    public function __construct(Twig_Environment $env)
    {
        $this->twig = $env;
    }

    public function render(array $params = []): string
    {
        return $this->twig->render($this->getViewFilename(), $params);
    }

    /**
     * Return the name of the Twig template
     *
     * @return string
     */
    abstract protected function getViewFilename(): string;
}

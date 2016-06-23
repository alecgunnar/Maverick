<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Utility\Renderer;

use Twig_Environment;

class TwigRenderer implements RendererInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @param Twig_Environment $env
     * @param string $suffix = null
     */
    public function __construct(Twig_Environment $env, string $suffix = null)
    {
        $this->twig   = $env;
        $this->suffix = trim(($suffix ?? 'twig'), '.');
    }

    /**
     * @inheritDoc
     */
    public function render(string $template, array $variables = []): string
    {
        return $this->twig->render($template . '.' . $this->suffix, $variables);
    }
}

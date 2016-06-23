<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Utility\Renderer;

interface RendererInterface
{
    /**
     * @param string $template
     * @param mixed[] $variables = []
     * @return string
     */
    public function render(string $template, array $variables = []): string;
}

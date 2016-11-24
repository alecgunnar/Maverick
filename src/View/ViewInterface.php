<?php

namespace Maverick\View;

interface ViewInterface
{
    /**
     * Returns a string which represents the
     * rendered view
     *
     * @param string[] $params = []
     * @return string
     */
    public function render(array $params = []): string;
}

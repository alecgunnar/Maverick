<?php

namespace Maverick\View;

class NotFoundView extends DefaultView
{
    public function getTitle(): string
    {
        return 'Page Not Found';
    }

    public function getContent(): string
    {
        return 'The page you are looking for was not found.';
    }
}

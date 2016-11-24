<?php

namespace Maverick\View;

class FatalErrorView extends DefaultView
{
    public function getTitle(): string
    {
        return 'An Error Occurred';
    }

    public function getContent(): string
    {
        return 'An error occurred while attempting to load the page.';
    }
}

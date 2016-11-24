<?php

namespace Maverick\View;

class NotAllowedView extends DefaultView
{
    public function getTitle(): string
    {
        return 'Method Not Allowed';
    }

    public function getContent(): string
    {
        return 'You are not allowed to use this method to access this page.';
    }
}

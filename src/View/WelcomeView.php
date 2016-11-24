<?php

namespace Maverick\View;

class WelcomeView extends DefaultView
{
    public function getTitle(): string
    {
        return 'Welcome to Maverick';
    }

    public function getContent(): string
    {
        return 'You have successfully gotten Maverick running.';
    }
}

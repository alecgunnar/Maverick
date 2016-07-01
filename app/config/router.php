<?php

return function($loader) {
    $loader->get('/')
        ->with(new Maverick\Controller\WelcomeController());
};

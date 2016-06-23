<?php

return function($loader) {
    $loader->get('/', new Maverick\Controller\WelcomeController());
};

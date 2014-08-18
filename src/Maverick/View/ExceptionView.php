<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\View;

class ExceptionView extends DefaultLayout {
    public static function render($code, $e) {
        $title = 'There was an Error!';

        if($code === 404) {
            $title = 'Page Not Found!';
        }

        $content = '
    <header class="container">
      <h1>' . $title . '</h1>
    </header>
    <main class="container">
      <p class="padding bg-danger">' . $e->getMessage() . '</p>
      <pre>' . $e->getTraceAsString() . '</pre>
    </main>';

        return parent::build($title, $content);
    }
}
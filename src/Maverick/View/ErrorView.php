<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\View;

class ErrorView extends DefaultLayout {
    public static function renderGeneralError($code, $debug, $e) {
        $main = '<p class="padding bg-danger">Your request could not be completed because there was an error. We apologize for any inconvenience.</p>';

        if($debug) {
            $main = '<p class="padding bg-danger">' . $e->getMessage() . '</p>
      <pre>' . htmlentities($e->getTraceAsString(), ENT_QUOTES) . '</pre>';
        }

        $content = '
    <header class="container">
      <h1>There was an Error!</h1>
    </header>
    <main class="container">
      ' . $main . '
    </main>';

        return parent::build('There was an Error!', $content);
    }

    public static function render404Error($debug, $e) {
        $content = '<header class="container">
      <h1>Page not Found!</h1>
    </header>
    <main class="container">
      <p class="padding bg-danger">' . ($debug ? $e->getMessage() : 'The page you are looking for does not exist.') . '</p>
    </main>';

        return parent::build('Page not Found!', $content);
    }
}
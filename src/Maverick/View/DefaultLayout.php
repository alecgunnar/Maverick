<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\View;

use Maverick\Application;

class DefaultLayout {
    public static function build($title, $content) {
        return '<!doctype html>
<html>
  <head lang="en">
    <title>' . $title . '</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <style>p.padding{padding:15px;}pre{margin:10px 0;}footer p{margin-top:20px;}</style>
  </head>
  <body>
    ' . $content . '
    <footer class="container">
      <p class="text-center">Maverick (v' . Application::VERSION . ') by <a href="http://aleccarpenter.me">Alec Carpenter</a>.</p>
    </footer>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>';
    }
}
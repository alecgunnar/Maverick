<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\View;

use Maverick\Application;

class IndexView extends DefaultLayout{
    public static function render() {
        $content = '<header class="container">
      <h1>Welcome to Maverick <small>v' . Application::VERSION . '</small></h1>
      <p class="lead">
        Maverick is a light-weight PHP framework for building websites and APIs.
      </p>
    </header>
    <main class="container">
      <h2>Getting Started</h2>
      ' . (version_compare(PHP_VERSION, '5.4.0') > 0 ? '' : '<p class="bg-warning padding">It is recommended that you have at least PHP 5.4 installed when using Maverick.</p>') . '
      Before going any farther, you should install all of the Composer packages required by Maverick. This can be done by running <kbd>composer install</kbd> via the command line or terminal. If you do not have Composer installed, you can get it from <a href="http://getcomposer.org" target="_blank">getcomposer.org</a>. Once all of the dependencies have been installed, you can start creating your application.<br />
      <br />
      Every application created with Maverick starts out the same way. Inside of your index file, <code>index.php</code> for this example, place the following:
      <pre>require \'./vendore/autoload.php\';<br /><br />// This should be the web root<br />define(\'ROOT\', __DIR__ . \'/\');<br /><br />$app = new Maverick\Application();</pre>
      <br />
      Next, add some routes. A route defines a set of conditions for the router, and when those conditions are satisfied, the router will tell it\'s controller to generate the page. For example, the route for your homepage might look like:
      <pre>$app->router->match(\'*\', \'/\', function() {<br />&nbsp;&nbsp;&nbsp;&nbsp;return \'Welcome to my new website!\';<br />});</pre>
      <br />
      Finally, to tell Maverick that the request by the user has been satisfied, add the following to the end of your index file:
      <pre>$app->finish();</pre>
      <br />
      With all of this you should now be able to visit <code>/</code> on your website and see <samp>Welcome to my new website!</samp> printed on the screen.
    </main>';

        return parent::build('Welcome to Maverick', $content);
    }
}
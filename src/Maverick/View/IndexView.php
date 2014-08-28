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
      <h3>Installing Dependencies</h3>
      Before going any farther, you should install all of the Composer packages required by Maverick. This can be done by running <kbd>composer install</kbd> via the command line or terminal. If you do not have Composer installed, you can get it from <a href="http://getcomposer.org" target="_blank">getcomposer.org</a>. Once all of the dependencies have been installed, you can start creating your application.
      <h3>Starting your Application</h3>
      Every application created with Maverick should start out the same way. This documentation will assume that you are editing <code>index.php</code> in the <code>public/</code> directory of your application. The following code will show you how to begin.
      <pre>&lt;?php<br /><br />define(\'ROOT\', dirname(__DIR__) . \'/\');<br /><br />require ROOT . \'/vendore/autoload.php\';<br /><br />$app = new Maverick\Application();<br /><br />$app->start();</pre>
      We must define the constant <code>ROOT</code> because it will tell Maverick (and your app) where the application is located. For example, it will aid Maverick in locating configuration files. This directory might not be the same directory as the web accessible directory. Once we define <code>ROOT</code>, we then load the Composer autoloader and then create our application by creating a new object of <code>Maverick\Application</code> and assigning that object to <code>$app</code>.<br />
      <br />
      The class <code>Maverick\Application</code> has five public attributes which are useful. These attributes include <code>$session</code>, <code>$request</code>, <code>$router</code>, <code>$response</code> and <code>$services</code>. This documentation will only show how to use the <code>$router</code> attribute which references the <code>Maverick\Http\Router</code> instance that is created for the application.
      <h3>Adding a Route</h3>
      A route consists of three (sometimes four) things, a method, a pattern and a controller. You can define a route by calling <code>match</code> on the <code>$router</code> attribute of the <code>Maverick\Application</code> class and supply as arguments the three things needed to define a route. The following code will demonstrate.
      <pre>$app->router->match(\'*\', \'/\', function() {<br />&nbsp;&nbsp;&nbsp;&nbsp;return \'Welcome to my new website!\';<br />});</pre>
      This route will be satisfied when any request method (specified by the <code>*</code>) is used and when the URN is <code>/</code>. When the route is satisfied, the closure supplied as the third argument will be called. The return from that closure will be used as the body of the response.
      <h3>Rendering the Result</h3>
      The final step in getting the content back to whomever requested it is to tell Maverick that we are all done. This is done by calling <code>finish</code> on the object of <code>Maverick\Application</code> we created at the beginning. Like so:
      <pre>$app->finish();</pre>
      <h3>Seeing the Result</h3>
      Now you can see what all of your hard work has accomplished. Go to a browser and navigate to your website. Since we essentially defined a route for the homepage, you do not need to supply a URN. When the page loads, you should see "<samp>Welcome to my new website!</samp>" printed on the screen.
    </main>';

        return parent::build('Welcome to Maverick', $content);
    }
}
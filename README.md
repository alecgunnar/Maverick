# Maverick

[![Build Status](https://travis-ci.org/alecgunnar/Maverick.svg?branch=master)](https://travis-ci.org/alecgunnar/Maverick)
[![Code Climate](https://codeclimate.com/github/alecgunnar/Maverick/badges/gpa.svg)](https://codeclimate.com/github/alecgunnar/Maverick)
[![Test Coverage](https://codeclimate.com/github/alecgunnar/Maverick/badges/coverage.svg)](https://codeclimate.com/github/alecgunnar/Maverick/coverage)

## Run the Demo

Once you have cloned Maverick to your machine and installed the Composer dependencies, you may run the demo application which comes packaged with the framework. You can run it by doing the following:

```sh
$ bin/maverick build --environment demo
```

This command will prepare the application for the runtime, it moves assets from their staging directories to their runtime directories. This is the only setup that is necessary. You may now use PHP's built in webserver to run the demo:

```sh
$ php -S localhost:8080 -t public
```

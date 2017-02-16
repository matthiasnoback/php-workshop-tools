# PHP Workshop tools

[![Build Status](https://travis-ci.org/matthiasnoback/php-workshop-tools.svg?branch=master)](https://travis-ci.org/matthiasnoback/php-workshop-tools)

## Setting up XDebug with PHPStorm

- On Mac, set up an "alias loopback IP":

    ```
    sudo ifconfig lo0 alias 10.254.254.254
    ```

- Export either the alias loopback IP (on Mac) or the IP of your machine on the local network (e.g. `192.x.x.x` or `10.x.x.x`) as the `DOCKER_HOST_IP` environment variable:

    ```
    export DOCKER_HOST_IP=10.254.254.254
    ```

- In `docker-compose.yml`, define an `XDEBUG_CONFIF` environment variable. The extra setting will print debug information to `stdout`.

    ```yaml
    services:
        website:
            image: matthiasnoback/php_workshop_tools_simple_webserver
            # ...
            environment:
                XDEBUG_CONFIG: "remote_host=${DOCKER_HOST_IP} remote_log=/dev/stdout"
    ```

- In PhpStorm go to `Preferences - Languages & Frameworks - PHP - Debug` and make sure you listen to port 9000.
- In PhpStorm select `Run - Start listening for PHP Debug Connections`.
- In PhpStorm set a breakpoint in for example the `index.php` of the website.
- Now run the service and request the index page. PhpStorm should show a dialogue asking you to accept an incoming debug connection.

# PHP Workshop tools

[![Build Status](https://travis-ci.org/matthiasnoback/php-workshop-tools.svg?branch=master)](https://travis-ci.org/matthiasnoback/php-workshop-tools)

Take [`docker-compose.example.yml`](docker-compose.example.yml) as an example for using the simple webserver image provided by this project.

## Required environment variables

You'll need to export the following environment variables:

```bash
export HOST_GID=$(id -g)
export HOST_UID=$(id -u)
export COMPOSER_HOME="$HOME/.composer"
```

## Setting up XDebug with PhpStorm

- (Only on Mac) Set up an "alias loopback IP":

    ```
    sudo ifconfig lo0 alias 10.254.254.254
    ```

- Export either the alias loopback IP (on Mac) or the IP of your machine on the local network (e.g. `192.x.x.x` or `10.x.x.x`) as the `DOCKER_HOST_IP` environment variable:

    ```
    export DOCKER_HOST_IP=10.254.254.254
    ```

- In PhpStorm go to `Preferences - Languages & Frameworks - PHP - Servers` create a new server named `docker`, using host `0.0.0.0`, port `8080`. Then select the root directory of the project and type in the absolute path of this directory inside the Docker container (i.e. `/opt`).
- In `docker-compose.yml` define a `PHP_IDE_CONFIG` environment variable with the name of the server (the one you provided in the previous step).
- Also define an `XDEBUG_CONFIG` environment variable. The `remote_log` setting is optional; it will print debug information to `stdout`, which will be helpful in case you have trouble setting up XDebug:

    ```yaml
    services:
        website:
            image: matthiasnoback/php_workshop_tools_simple_webserver
            # ...
            environment:
                PHP_IDE_CONFIG: "serverName=docker"
                XDEBUG_CONFIG: "remote_host=${DOCKER_HOST_IP} remote_log=/dev/stdout"
    ```

- In PhpStorm go to `Preferences - Languages & Frameworks - PHP - Debug` and make sure you listen to port 9000.
- In PhpStorm select `Run - Start listening for PHP Debug Connections`.
- In PhpStorm set a breakpoint in for example the `index.php` of the website. Optionally select `Run - Break at first line in PHP scripts`.
- Now run the service and request the index page. PhpStorm should show a dialogue asking you to accept an incoming debug connection.

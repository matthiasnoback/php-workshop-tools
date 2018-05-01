# PHP Workshop tools

[![Build Status](https://travis-ci.org/matthiasnoback/php-workshop-tools.svg?branch=master)](https://travis-ci.org/matthiasnoback/php-workshop-tools)

Take [`docker-compose.example.yml`](docker-compose.example.yml) as an example for using the simple webserver image provided by this project.

## Required environment variables

You'll need to export the following environment variables:

```bash
export HOST_GID=$(id -g)
export HOST_UID=$(id -u)
export COMPOSER_HOME="${HOME}/.composer"
```

To make these always available, you could add the above lines to your `~/.bash_profile` file.

## Optional environment variables

You only need to provide the `DOCKER_HOST_IP` environment variable if you want to use XDebug for step debugging.

### When using Docker for Mac

```
export DOCKER_HOST_IP=host.docker.internal
```

### When using Docker for Windows

```
export DOCKER_HOST_IP=docker.for.windowss.localhost
```

### When using Linux

Export the IP of your machine on the local network (e.g. `192.x.x.x` or `10.x.x.x`) as the `DOCKER_HOST_IP` environment variable, e.g.

```
export DOCKER_HOST_IP=192.168.1.33
```

## Setting up XDebug with PhpStorm (optional)

For each PHP-based service defined in your `docker-compose.yml`, you should take the following steps:

- In PhpStorm go to `Preferences - Languages & Frameworks - PHP - Servers` create a new server with the same name as the service itself in `docker-compose.yml`, using host `0.0.0.0`, and the host port (e.g. `8080`). Then select the root directory of the project and in the right column type in the absolute path of the project directory *inside* the Docker container (i.e. `/opt`).
- Make sure that in `docker-compose.yml` the `PHP_IDE_CONFIG` environment variable has been defined containing the name of the server (the same one you provided in the previous step).
- Also make sure you have defined an `XDEBUG_CONFIG` environment variable for the service in `docker-compose.yml`. (The `remote_log` setting is optional; it will print debug information to `stdout`, which will be helpful in case you have trouble setting up XDebug.)

    ```yaml
    services:
        website:
            image: matthiasnoback/php_workshop_tools_simple_webserver
            ports:
                - 8080:80
            # ...
            environment:
                PHP_IDE_CONFIG: "serverName=website"
                XDEBUG_CONFIG: "remote_host=${DOCKER_HOST_IP} remote_log=/dev/stdout"
    ```

- In PhpStorm go to `Preferences - Languages & Frameworks - PHP - Debug` and make sure you listen to port 9000.
- On the same page, increase the number of "Max. simultaneous connections" (to allow debugging multiple PHP services at the same time).
- In PhpStorm select `Run - Start listening for PHP Debug Connections`.
- In PhpStorm set a breakpoint in - for example - the `index.php` of the website. Optionally select `Run - Break at first line in PHP scripts`.
- Now run the service and request the index page. PhpStorm should show a dialogue asking you to accept an incoming debug connection.

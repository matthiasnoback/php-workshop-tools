<?php
declare(strict_types=1);

namespace Common\Web;

use Assert\Assertion;

/**
 * Simple but convenient controller resolver.
 */
final class ControllerResolver
{
    /**
     * When the request URI is "/someRoute/", the resolver looks for a method "someRouteController" on the provided application object.
     * If it can't be found, a generic 404 controller will be returned.
     *
     * @param array $server Server parameters (simply provide `$_SERVER` as an argument)
     * @param array $get Query parameters (simply provide `$_GET` as an argument)
     * @param object $application An object containing "[route]Controller" methods
     * @return callable The controller, should be called without any arguments
     */
    public static function resolve(array $server, array $get, $application): callable
    {
        Assertion::isObject($application, '$application should be an object containing public "[route]Controller" methods.');

        $route = trim($server['PATH_INFO'] ?? '', '/');
        $controllerMethod = [$application, $route . 'Controller'];

        if (empty($route) || !is_callable($controllerMethod)) {
            return self::create404Controller($application);
        }

        return function () use ($controllerMethod, $get) {
            return call_user_func_array($controllerMethod, $get);
        };
    }

    private static function create404Controller($application): callable
    {
        return function () use ($application) {
            error_log('ControllerResolver: No matching controller method, create 404 response');
            if (PHP_SAPI !== 'cli') {
                header('Content-Type: text/plain', true, 404);
            }
            echo "Page not found\n";

            $controllerMethods = array_filter(get_class_methods($application), function (string $methodName) {
                return substr($methodName, -10) === 'Controller';
            });

            $uris = array_map(function(string $methodName) {
                return '/' . substr($methodName, 0, -10);
            }, $controllerMethods);

            if (!empty($uris)) {
                echo "\nYou could try:\n";
                foreach ($uris as $uri) {
                    echo "- $uri\n";
                }
            }
        };
    }
}

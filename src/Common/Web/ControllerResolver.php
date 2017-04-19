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

        $route = trim($server['REQUEST_URI'], '/');
        $controllerMethod = [$application, $route . 'Controller'];

        if (empty($route) || !is_callable($controllerMethod)) {
            return function() {
                error_log('ControllerResolver: No matching controller method, create 404 response');
                header('Content-Type: text/plain', true, 404);
                echo 'Page not found';
            };
        }

        return function() use ($controllerMethod, $get) {
            return call_user_func_array($controllerMethod, $get);
        };
    }
}

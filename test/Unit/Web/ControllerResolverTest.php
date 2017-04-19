<?php
declare(strict_types=1);

namespace Test\Unit\Web;

use Common\Web\ControllerResolver;
use Test\Unit\Web\Fixtures\Application;

class ControllerResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    /**
     * @test
     */
    public function it_returns_the_method_that_matches_the_request_uri()
    {
        $controller = ControllerResolver::resolve(
            ['REQUEST_URI' => '/some'],
            [],
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its own name
            'Test\Unit\Web\Fixtures\Application::someController',
            $controller()
        );
    }

    /**
     * @test
     */
    public function it_forwards_query_parameters_as_controller_method_arguments()
    {
        $get = ['id' => '1', 'orderId' => '123'];

        $controller = ControllerResolver::resolve(
            ['REQUEST_URI' => '/withArguments'],
            $get,
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its arguments
            array_values($get),
            $controller()
        );
    }
}

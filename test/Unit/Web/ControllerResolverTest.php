<?php
declare(strict_types=1);

namespace Test\Unit\Web;

use Common\Web\ControllerResolver;
use PHPUnit\Framework\TestCase;
use Test\Unit\Web\Fixtures\Application;

class ControllerResolverTest extends TestCase
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
    public function it_returns_the_method_that_matches_path_info_from_the_request()
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/some'],
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
            ['PATH_INFO' => '/withArguments'],
            $get,
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its arguments
            array_values($get),
            $controller()
        );
    }

    /**
     * @test
     * @bugfix
     */
    public function it_deals_with_undefined_path_info()
    {
        $controller = ControllerResolver::resolve(
            [],
            [],
            $this->application
        );

        $this->assertTrue(is_callable($controller));
    }

    /**
     * @test
     */
    public function it_shows_alternative_routes()
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/unknown'],
            [],
            $this->application
        );

        ob_start();
        $controller();
        $response = ob_get_contents();
        ob_end_clean();

        $this->assertContains('/some', $response);
        $this->assertContains('/withArguments', $response);

        echo $response;
    }

    /**
     * @test
     */
    public function it_resolves_an_empty_route_to_the_index_controller()
    {
        $controller = ControllerResolver::resolve(
            ['PATH_INFO' => '/'],
            [],
            $this->application
        );

        $this->assertEquals(
            // when called, $controller will return its own name
            'Test\Unit\Web\Fixtures\Application::indexController',
            $controller()
        );
    }
}

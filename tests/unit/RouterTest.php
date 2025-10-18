<?php

use system\core\Router;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock server variables for testing
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }

    public function testRouterCanRegisterRoutes()
    {
        $router = new Router();
        $router->get('/test', 'TestController@index');
        $this->assertCount(1, $router->routes); // Should have one route registered
        $this->assertEquals('GET', $router->routes[0]['method']);
        $this->assertEquals('/test', $router->routes[0]['url']);
        $this->assertEquals('TestController@index', $router->routes[0]['action']);
    }

    public function testRouterSupportsDifferentHttpMethods()
    {
        $router = new Router();
        $router->get('/test', 'TestController@index');
        $router->post('/test', 'TestController@store');
        $router->put('/test', 'TestController@update');
        $router->delete('/test', 'TestController@destroy');
        $this->assertCount(4, $router->routes); // Should have four routes registered
        $methods = array_column($router->routes, 'method');
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
        $this->assertContains('PUT', $methods);
        $this->assertContains('DELETE', $methods);
    }

    public function testRouterSupportsRouteParameters()
    {
        $router = new Router();
        $router->get('/user/{id}', 'UserController@show');
        $router->get('/user/{id}/post/{postId}', 'UserController@showPost');
        $this->assertCount(2, $router->routes); // Should have two routes registered
        $this->assertStringContainsString('{id}', $router->routes[0]['url']);
        $this->assertStringContainsString('{postId}', $router->routes[1]['url']);
    }

}
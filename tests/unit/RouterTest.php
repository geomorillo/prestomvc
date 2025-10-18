<?php

use system\core\Router;

class RouterTest extends TestCase
{
    private $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->router = null;
    }

    public function testRouterCanBeInstantiated()
    {
        $this->assertInstanceOf('system\core\Router', $this->router);
        $this->assertInstanceOf('system\core\Route', $this->router);
    }

    public function testRouterHasRoutesArray()
    {
        $this->assertObjectHasProperty('routes', $this->router);
        $this->assertIsArray($this->router->routes);
        $this->assertEmpty($this->router->routes);
    }

    public function testRouterHasHttpMethods()
    {
        $methods = ['get', 'post', 'put', 'delete', 'any'];
        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->router, $method));
        }
    }

    public function testRouterMethodsReturnSelf()
    {
        $result = $this->router->get('/test', 'TestController@index');
        $this->assertSame($this->router, $result);
    }
}
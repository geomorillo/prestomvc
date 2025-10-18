<?php

use system\core\Controller;

class TestController extends Controller
{
    public function index(...$args)
    {
        return 'index called with ' . implode(', ', $args);
    }

    public function show($id)
    {
        return "showing item $id";
    }

    public function before()
    {
        return 'before middleware';
    }

    public function after()
    {
        return 'after middleware';
    }
}

class ControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TestController();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->controller = null;
    }

    public function testControllerHasViewProperty()
    {
        $this->assertObjectHasProperty('view', $this->controller);
        $this->assertInstanceOf('system\core\View', $this->controller->view);
    }

    public function testControllerMethodsReturnExpectedResults()
    {
        $result = $this->controller->index('arg1', 'arg2');
        $this->assertEquals('index called with arg1, arg2', $result);

        $result = $this->controller->show(123);
        $this->assertEquals('showing item 123', $result);
    }

    public function testControllerMiddlewareMethodsWork()
    {
        $this->assertEquals('before middleware', $this->controller->before());
        $this->assertEquals('after middleware', $this->controller->after());
    }

    public function testCsrfTokenGeneration()
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('getCsrfToken');
        $method->setAccessible(true);
        $token = $method->invoke($this->controller);

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testWithCsrfToken()
    {
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions are disabled for testing');
        }

        $data = ['name' => 'test', 'value' => 123];
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('withCsrfToken');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $data);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('csrf_token', $result);
        $this->assertEquals('test', $result['name']);
        $this->assertEquals(123, $result['value']);
    }

    public function testWithCsrfTokenEmptyArray()
    {
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions are disabled for testing');
        }

        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('withCsrfToken');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, []);

        $this->assertArrayHasKey('csrf_token', $result);
        $this->assertCount(1, $result);
    }
}
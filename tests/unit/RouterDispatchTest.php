<?php

use system\core\Router;
use system\core\Csrf;
use system\http\Request;
use system\exceptions\HttpException;
use system\exceptions\SecurityException;

require_once __DIR__ . '/../fixtures/RouterTestController.php';

class RouterDispatchTest extends TestCase
{
    private $router;

    protected function setUp(): void
    {
        parent::setUp();

        // Request constructor reads from $_SERVER
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        // Initialize for CSRF
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->router);
    }

    /**
     * Replace the router's internal Request with a mock
     */
    private function createRouterWithMockRequest($method, $url)
    {
        $this->router = new Router();

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getMethod')->willReturn($method);
        $mockRequest->method('getUrl')->willReturn($url);

        $reflection = new ReflectionClass($this->router);
        $requestProp = $reflection->getProperty('request');
        $requestProp->setAccessible(true);
        $requestProp->setValue($this->router, $mockRequest);
    }

    public function testGetRouteDispatchesSuccessfully()
    {
        $this->createRouterWithMockRequest('GET', '/test');
        $this->router->get('test', 'app\controllers\RouterTestController@index');

        $this->router->dispatch();
        $this->assertTrue(true);
    }

    public function testGetRouteDoesNotTriggerCsrfValidation()
    {
        $this->createRouterWithMockRequest('GET', '/test');
        $this->router->get('test', 'app\controllers\RouterTestController@index');

        unset($_POST['csrf_token']);

        $this->router->dispatch();
        $this->assertTrue(true);
    }

    public function testPostRouteWithoutCsrfTokenThrowsSecurityException()
    {
        $this->createRouterWithMockRequest('POST', '/test');
        $this->router->post('test', 'app\controllers\RouterTestController@store');

        unset($_POST['csrf_token']);

        $this->expectException(SecurityException::class);
        $this->expectExceptionMessage('CSRF token missing');
        $this->router->dispatch();
    }

    public function testPostRouteWithValidCsrfTokenDispatchesSuccessfully()
    {
        $this->createRouterWithMockRequest('POST', '/test');
        $this->router->post('test', 'app\controllers\RouterTestController@store');

        $_SESSION = [];
        $token = Csrf::generate();
        $_POST['csrf_token'] = $token;

        $this->router->dispatch();
        $this->assertTrue(true);
    }

    public function testPostRouteWithInvalidCsrfTokenThrowsSecurityException()
    {
        $this->createRouterWithMockRequest('POST', '/test');
        $this->router->post('test', 'app\controllers\RouterTestController@store');

        $_SESSION = [];
        Csrf::generate();
        $_POST['csrf_token'] = 'definitely-not-valid';

        $this->expectException(SecurityException::class);
        $this->expectExceptionMessage('CSRF token invalid');
        $this->router->dispatch();
    }

    public function testPutRouteWithoutCsrfTokenThrowsSecurityException()
    {
        $this->createRouterWithMockRequest('PUT', '/test');
        $this->router->put('test', 'app\controllers\RouterTestController@update');

        unset($_POST['csrf_token']);

        $this->expectException(SecurityException::class);
        $this->router->dispatch();
    }

    public function testDeleteRouteWithoutCsrfTokenThrowsSecurityException()
    {
        $this->createRouterWithMockRequest('DELETE', '/test');
        $this->router->delete('test', 'app\controllers\RouterTestController@destroy');

        unset($_POST['csrf_token']);

        $this->expectException(SecurityException::class);
        $this->router->dispatch();
    }

    public function testNoMatchingRouteThrowsHttpException404()
    {
        $this->createRouterWithMockRequest('GET', '/nonexistent');
        $this->router->get('other', 'app\controllers\RouterTestController@index');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Page not found');
        $this->router->dispatch();
    }

    public function testCsrfTokenCanComeFromRequestArray()
    {
        $this->createRouterWithMockRequest('POST', '/test');
        $this->router->post('test', 'app\controllers\RouterTestController@store');

        $_SESSION = [];
        $token = Csrf::generate();
        unset($_POST['csrf_token']);
        $_REQUEST['csrf_token'] = $token;

        $this->router->dispatch();
        $this->assertTrue(true);
    }

    public function testMultipleRoutesDispatchesCorrectOne()
    {
        $this->createRouterWithMockRequest('GET', '/other-route');
        $this->router->get('test', 'app\controllers\RouterTestController@index');
        $this->router->get('other-route', 'app\controllers\RouterTestController@index');

        $this->router->dispatch();
        $this->assertTrue(true);
    }
}

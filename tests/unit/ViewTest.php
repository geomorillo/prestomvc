<?php

use system\core\View;

class ViewTest extends TestCase
{
    private $view;

    protected function setUp(): void
    {
        parent::setUp();
        $this->view = new View();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->view = null;
    }

    public function testViewCanBeInstantiated()
    {
        $this->assertInstanceOf('system\core\View', $this->view);
    }

    public function testUseTemplateReturnsSelf()
    {
        $result = $this->view->useTemplate('custom');
        $this->assertInstanceOf('system\core\View', $result); // Should return $this for chaining
        $this->assertSame($this->view, $result); // Should return the same instance
    }

    public function testPartialPassesDataToView()
    {
        // Create a test view file (View::partial looks in ROOT . namespace . DS . path)
        $testViewPath = ROOT . 'app' . DS . 'test_data.php';
        $testContent = '<?php echo $message . " " . $number; ?>';
        file_put_contents($testViewPath, $testContent);

        try {
            $result = $this->view->partial('test_data', ['message' => 'Hello', 'number' => 42]);
            $this->assertEquals('Hello 42', $result);
        } finally {
            // Clean up
            if (file_exists($testViewPath)) {
                unlink($testViewPath);
            }
        }
    }

}
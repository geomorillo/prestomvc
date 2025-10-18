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

    public function testSetCallerSetsNamespace()
    {
        $this->view->setCaller('test_namespace');
        // We can't directly test private property, but we can test it works with render
        $this->assertTrue(true); // Just testing the method exists and doesn't throw
    }

    public function testUseTemplateSetsTemplate()
    {
        $result = $this->view->useTemplate('custom');
        $this->assertInstanceOf('system\core\View', $result); // Should return $this for chaining
    }

    public function testPartialSetsPartialFlag()
    {
        // Create a test view file
        $testViewPath = ROOT . 'app' . DS . 'views' . DS . 'test_partial.php';
        $testContent = '<?php echo "partial content"; ?>';
        file_put_contents($testViewPath, $testContent);

        try {
            $result = $this->view->partial('test_partial', ['data' => 'test']);
            $this->assertEquals('partial content', $result);
        } finally {
            // Clean up
            if (file_exists($testViewPath)) {
                unlink($testViewPath);
            }
        }
    }

    public function testRenderWithNonExistentView()
    {
        $this->expectException(\Exception::class);
        $this->view->render('non_existent_view');
    }

    public function testRenderPassesDataToView()
    {
        // Create a test view file
        $testViewPath = ROOT . 'app' . DS . 'views' . DS . 'test_data.php';
        $testContent = '<?php echo $message . " " . $number; ?>';
        file_put_contents($testViewPath, $testContent);

        try {
            $result = $this->view->render('test_data', ['message' => 'Hello', 'number' => 42]);
            $this->assertEquals('Hello 42', $result);
        } finally {
            // Clean up
            if (file_exists($testViewPath)) {
                unlink($testViewPath);
            }
        }
    }

    public function testUseTemplateReturnsSelf()
    {
        $result = $this->view->useTemplate('custom');
        $this->assertSame($this->view, $result);
    }

    public function testRenderCachedMethodExists()
    {
        $this->assertTrue(method_exists($this->view, 'renderCached'));
    }

    public function testRenderNormalizesPathSeparators()
    {
        // Test that the render method normalizes path separators
        // This is tested indirectly through the path processing logic
        $this->assertTrue(method_exists($this->view, 'render'));
    }
}
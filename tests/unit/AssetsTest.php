<?php

use system\core\Assets;

class AssetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset assets for each test
        $reflection = new ReflectionClass('system\core\Assets');
        $assetsProperty = $reflection->getProperty('assets');
        $assetsProperty->setAccessible(true);
        $assetsProperty->setValue(null, []);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up after each test
        $reflection = new ReflectionClass('system\core\Assets');
        $assetsProperty = $reflection->getProperty('assets');
        $assetsProperty->setAccessible(true);
        $assetsProperty->setValue(null, []);
    }

    public function testAddSingleAsset()
    {
        Assets::add(['jquery' => 'js/jquery.min.js']);

        $result = Assets::get('jquery');
        $this->assertContains('jquery.min.js', $result);
        $this->assertContains('<script', $result);
        $this->assertContains('text/javascript', $result);
    }

    public function testAddCssAsset()
    {
        Assets::add(['bootstrap' => 'css/bootstrap.min.css']);

        $result = Assets::get('bootstrap');
        $this->assertContains('bootstrap.min.css', $result);
        $this->assertContains('<link', $result);
        $this->assertContains('rel="stylesheet"', $result);
    }

    public function testAddImageAsset()
    {
        Assets::add(['logo' => ['images/logo.png', 'alt="Logo" class="logo"']]);

        $result = Assets::get('logo');
        $this->assertContains('logo.png', $result);
        $this->assertContains('<img', $result);
        $this->assertContains('alt="Logo"', $result);
        $this->assertContains('class="logo"', $result);
    }

    public function testAddExternalUrlAsset()
    {
        Assets::add(['cdn' => 'https://cdn.example.com/style.css']);

        $result = Assets::get('cdn');
        $this->assertContains('https://cdn.example.com/style.css', $result);
        $this->assertContains('<link', $result);
        $this->assertContains('rel="stylesheet"', $result);
    }

    public function testGroupAssets()
    {
        Assets::group([
            'scripts' => ['js/jquery.min.js', 'js/bootstrap.min.js']
        ]);

        $result = Assets::get('scripts');
        $this->assertContains('jquery.min.js', $result);
        $this->assertContains('bootstrap.min.js', $result);
        $this->assertContains('<script', $result);
        // Should contain two script tags
        $this->assertEquals(2, substr_count($result, '<script'));
    }

    public function testAddToGroup()
    {
        // First create a group
        Assets::group(['scripts' => ['js/jquery.min.js']]);

        // Add to existing group
        Assets::addToGroup('scripts', 'js/bootstrap.min.js');

        $result = Assets::get('scripts');
        $this->assertContains('jquery.min.js', $result);
        $this->assertContains('bootstrap.min.js', $result);
        $this->assertEquals(2, substr_count($result, '<script'));
    }

    public function testGetNonexistentAsset()
    {
        $result = Assets::get('nonexistent');
        $this->assertNull($result);
    }

    public function testGetAllAssets()
    {
        Assets::add([
            'jquery' => 'js/jquery.min.js',
            'bootstrap' => 'css/bootstrap.min.css'
        ]);

        $all = Assets::getAll();
        $this->assertIsArray($all);
        $this->assertCount(2, $all);
        $this->assertArrayHasKey('jquery', $all);
        $this->assertArrayHasKey('bootstrap', $all);
    }

    public function testGetAllAssetsWhenEmpty()
    {
        $all = Assets::getAll();
        $this->assertIsArray($all);
        $this->assertEmpty($all);
    }

    public function testAddEmptyArray()
    {
        Assets::add([]);
        $all = Assets::getAll();
        $this->assertEmpty($all);
    }

    public function testGroupEmptyArray()
    {
        Assets::group([]);
        $all = Assets::getAll();
        $this->assertEmpty($all);
    }
}
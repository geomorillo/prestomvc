<?php

use system\core\Language;

class LanguageTest extends TestCase
{
    private $originalLanguage;
    private $originalTranslation;

    protected function setUp(): void
    {
        parent::setUp();
        // Store original values
        $this->originalLanguage = 'en'; // Default fallback

        // Reset static properties for testing
        $reflection = new ReflectionClass('system\core\Language');
        $languageProperty = $reflection->getProperty('language');
        $languageProperty->setAccessible(true);
        $languageProperty->setValue(null, 'en');

        $translationProperty = $reflection->getProperty('translation');
        $translationProperty->setAccessible(true);
        $translationProperty->setValue(null, null);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Restore original language
        Language::setLang($this->originalLanguage);
    }

    public function testSetLang()
    {
        $result = Language::setLang('es');
        $this->assertEquals('es', Language::getLang());
        $this->assertInstanceOf('system\core\Language', $result);
    }

    public function testGetLang()
    {
        $this->assertEquals('en', Language::getLang());
    }

    public function testTranslateWithoutParams()
    {
        // Mock translation data
        $reflection = new ReflectionClass('system\core\Language');
        $translationProperty = $reflection->getProperty('translation');
        $translationProperty->setAccessible(true);
        $translationProperty->setValue(null, [
            'hello' => 'Hello World',
            'goodbye' => 'Goodbye'
        ]);

        $result = Language::translate('hello');
        $this->assertEquals('Hello World', $result);
    }

    public function testTranslateWithParams()
    {
        // Mock translation data
        $reflection = new ReflectionClass('system\core\Language');
        $translationProperty = $reflection->getProperty('translation');
        $translationProperty->setAccessible(true);
        $translationProperty->setValue(null, [
            'welcome' => 'Welcome {name} to {place}'
        ]);

        $result = Language::translate('welcome', ['name' => 'John', 'place' => 'PrestoMVC']);
        $this->assertEquals('Welcome John to PrestoMVC', $result);
    }

    public function testTranslateAll()
    {
        $expectedTranslations = [
            'hello' => 'Hello',
            'goodbye' => 'Goodbye',
            'welcome' => 'Welcome'
        ];

        // Mock translation data
        $reflection = new ReflectionClass('system\core\Language');
        $translationProperty = $reflection->getProperty('translation');
        $translationProperty->setAccessible(true);
        $translationProperty->setValue(null, $expectedTranslations);

        $result = Language::translateAll();
        $this->assertEquals($expectedTranslations, $result);
    }

    public function testFindAndReplace()
    {
        $text = 'Hello {name}, welcome to {place}!';
        $params = ['name' => 'John', 'place' => 'PrestoMVC'];

        $result = Language::findAndReplace($text, $params);
        $this->assertEquals('Hello John, welcome to PrestoMVC!', $result);
    }

    public function testFindAndReplaceWithMissingParams()
    {
        $text = 'Hello {name}, welcome to {place}!';
        $params = ['name' => 'John']; // Missing 'place'

        $result = Language::findAndReplace($text, $params);
        $this->assertEquals('Hello John, welcome to !', $result); // 'place' becomes empty
    }

    public function testFindAndReplaceWithNoPlaceholders()
    {
        $text = 'Hello World';
        $params = ['name' => 'John'];

        $result = Language::findAndReplace($text, $params);
        $this->assertEquals('Hello World', $result);
    }

    public function testFindAndReplaceWithEmptyParams()
    {
        $text = 'Hello {name}';
        $params = [];

        $result = Language::findAndReplace($text, $params);
        $this->assertEquals('Hello {name}', $result); // No replacement
    }

    public function testTranslateWithNonexistentKey()
    {
        // Mock translation data
        $reflection = new ReflectionClass('system\core\Language');
        $translationProperty = $reflection->getProperty('translation');
        $translationProperty->setAccessible(true);
        $translationProperty->setValue(null, ['hello' => 'Hello']);

        $this->expectException(\Error::class); // Undefined array key
        Language::translate('nonexistent');
    }

    public function testDefaultLanguageIsSet()
    {
        $this->assertEquals('en', Language::getLang());
    }
}
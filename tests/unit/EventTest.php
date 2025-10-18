<?php

use system\core\Event;

class EventTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset events array for each test
        $reflection = new ReflectionClass('system\core\Event');
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $eventsProperty->setValue(null, []);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up events after each test
        $reflection = new ReflectionClass('system\core\Event');
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $eventsProperty->setValue(null, []);
    }

    public function testCreateEvent()
    {
        $executed = false;
        $callback = function($args) use (&$executed) {
            $executed = true;
        };

        Event::create('test_event', $callback);

        // Check that event was registered
        $reflection = new ReflectionClass('system\core\Event');
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $events = $eventsProperty->getValue();

        $this->assertArrayHasKey('test_event', $events);
        $this->assertCount(1, $events['test_event']);
        $this->assertFalse($executed); // Should not execute yet
    }

    public function testTriggerEvent()
    {
        $executed = false;
        $receivedArgs = null;

        $callback = function($args) use (&$executed, &$receivedArgs) {
            $executed = true;
            $receivedArgs = $args;
        };

        Event::create('test_event', $callback);
        Event::trigger('test_event', ['param1' => 'value1']);

        $this->assertTrue($executed);
        $this->assertEquals(['param1' => 'value1'], $receivedArgs);
    }

    public function testTriggerNonexistentEvent()
    {
        // Should not throw exception or error
        Event::trigger('nonexistent_event');
        $this->assertTrue(true); // If we reach here, test passes
    }

    public function testMultipleCallbacksForSameEvent()
    {
        $executionCount = 0;
        $callback1 = function($args) use (&$executionCount) {
            $executionCount++;
        };
        $callback2 = function($args) use (&$executionCount) {
            $executionCount++;
        };

        Event::create('test_event', $callback1);
        Event::create('test_event', $callback2);

        Event::trigger('test_event');

        $this->assertEquals(2, $executionCount);
    }

    public function testMultipleEvents()
    {
        $executedEvents = [];

        $callback1 = function($args) use (&$executedEvents) {
            $executedEvents[] = 'event1';
        };
        $callback2 = function($args) use (&$executedEvents) {
            $executedEvents[] = 'event2';
        };

        Event::create('event1', $callback1);
        Event::create('event2', $callback2);

        Event::trigger('event1');
        Event::trigger('event2');

        $this->assertEquals(['event1', 'event2'], $executedEvents);
    }

    public function testTriggerEventWithEmptyArgs()
    {
        $executed = false;
        $receivedArgs = 'not_set';

        $callback = function($args) use (&$executed, &$receivedArgs) {
            $executed = true;
            $receivedArgs = $args;
        };

        Event::create('test_event', $callback);
        Event::trigger('test_event'); // No args passed

        $this->assertTrue($executed);
        $this->assertEquals([], $receivedArgs); // Should be empty array
    }

    public function testCallbackReceivesCorrectArgs()
    {
        $receivedArgs = null;
        $callback = function($args) use (&$receivedArgs) {
            $receivedArgs = $args;
        };

        Event::create('test_event', $callback);

        $testArgs = ['key1' => 'value1', 'key2' => 42, 'key3' => ['nested' => 'array']];
        Event::trigger('test_event', $testArgs);

        $this->assertEquals($testArgs, $receivedArgs);
    }

    public function testCreateRequiresClosure()
    {
        // This should work fine with a closure
        Event::create('test_event', function() {});
        $this->assertTrue(true);
    }

    public function testEventsPropertyIsArray()
    {
        $reflection = new ReflectionClass('system\core\Event');
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $events = $eventsProperty->getValue();

        $this->assertIsArray($events);
    }
}
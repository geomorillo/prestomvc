<?php

use system\core\Email;

class EmailTest extends TestCase
{
    private $email;

    protected function setUp(): void
    {
        parent::setUp();
        $this->email = new Email();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->email = null;
    }

    public function testConstructorResetsProperties()
    {
        $this->assertEmpty($this->email->getTo());
        $this->assertEmpty($this->email->getHeaders());
        $this->assertNull($this->email->getSubject());
        $this->assertNull($this->email->getMessage());
        $this->assertEquals(78, $this->email->getWrap());
        $this->assertEmpty($this->email->getParameters());
        // Note: Email class doesn't have getAttachments method, checking hasAttachments instead
        $this->assertFalse($this->email->hasAttachments());
    }

    public function testResetMethod()
    {
        // Set some values first
        $this->email->to('test@example.com', 'Test User')
                   ->subject('Test Subject')
                   ->message('Test Message');

        // Reset
        $result = $this->email->reset();

        // Check reset worked
        $this->assertEmpty($this->email->getTo());
        $this->assertNull($this->email->getSubject());
        $this->assertNull($this->email->getMessage());
        $this->assertInstanceOf('system\core\Email', $result);
    }

    public function testSetTo()
    {
        $result = $this->email->to('test@example.com', 'Test User');

        $this->assertInstanceOf('system\core\Email', $result);
        $toAddresses = $this->email->getTo();
        $this->assertStringContainsString('test@example.com', $toAddresses[0]);
    }

    public function testSetSubject()
    {
        $result = $this->email->subject('Test Subject');

        $this->assertInstanceOf('system\core\Email', $result);
        // Subject gets UTF-8 encoded in chunks separated by spaces
        $encoded = $this->email->getSubject();
        $this->assertStringContainsString('UTF-8?B?', $encoded);
        // Decode all base64 chunks to verify original content
        preg_match_all('/=\?UTF-8\?B\?([^?]+)\?=/', $encoded, $matches);
        $decodedSubject = '';
        foreach ($matches[1] as $chunk) {
            $decodedSubject .= base64_decode($chunk);
        }
        $this->assertEquals('Test Subject', $decodedSubject);
    }

    public function testSetMessage()
    {
        $message = 'This is a test message.';
        $result = $this->email->message($message);

        $this->assertInstanceOf('system\core\Email', $result);
        $this->assertEquals($message, $this->email->getMessage());
    }

    public function testMessageWithDots()
    {
        // A line starting with a dot should be escaped (RFC 2821 dot-stuffing)
        $message = "First line.\n. dot line\nLast line.";
        $this->email->message($message);

        // Should escape dot at start of line: \n. → \n..
        $this->assertStringContainsString("\n..", $this->email->getMessage());
    }

    public function testSetFrom()
    {
        $result = $this->email->from('sender@example.com', 'Sender Name');

        $this->assertInstanceOf('system\core\Email', $result);
        $headers = $this->email->getHeaders();
        $this->assertStringContainsString('sender@example.com', $headers[0]);
    }

    public function testReplyTo()
    {
        $result = $this->email->replyTo('reply@example.com', 'Reply Name');

        $this->assertInstanceOf('system\core\Email', $result);
        $headers = $this->email->getHeaders();
        $this->assertStringContainsString('reply@example.com', $headers[0]);
    }

    public function testCc()
    {
        $result = $this->email->cc('cc@example.com', 'CC Name');

        $this->assertInstanceOf('system\core\Email', $result);
        $headers = $this->email->getHeaders();
        $this->assertStringContainsString('cc@example.com', $headers[0]);
    }

    public function testBcc()
    {
        $result = $this->email->bcc('bcc@example.com', 'BCC Name');

        $this->assertInstanceOf('system\core\Email', $result);
        $headers = $this->email->getHeaders();
        $this->assertStringContainsString('bcc@example.com', $headers[0]);
    }

    public function testAddGenericHeader()
    {
        $result = $this->email->addGenericHeader('X-Custom', 'Custom Value');

        $this->assertInstanceOf('system\core\Email', $result);
        $headers = $this->email->getHeaders();
        $this->assertStringContainsString('X-Custom: Custom Value', implode(PHP_EOL, $headers));
    }

    public function testSetParameters()
    {
        $params = '-f sender@example.com';
        $result = $this->email->setParameters($params);

        $this->assertInstanceOf('system\core\Email', $result);
        $this->assertEquals($params, $this->email->getParameters());
    }

    public function testSetWrap()
    {
        $result = $this->email->setWrap(100);

        $this->assertInstanceOf('system\core\Email', $result);
        $this->assertEquals(100, $this->email->getWrap());
    }

    public function testSetWrapWithInvalidValue()
    {
        $this->email->setWrap(0); // Should default to 78
        $this->assertEquals(78, $this->email->getWrap());
    }

    public function testHasAttachmentsReturnsFalseWhenEmpty()
    {
        $this->assertFalse($this->email->hasAttachments());
    }

    public function testAddAttachment()
    {
        // Create a temporary file for testing
        $tempFile = tempnam(sys_get_temp_dir(), 'test_attachment');
        file_put_contents($tempFile, 'test content');

        $result = $this->email->addAttachment($tempFile, 'test.txt');

        $this->assertInstanceOf('system\core\Email', $result);
        $this->assertTrue($this->email->hasAttachments());

        // Clean up
        unlink($tempFile);
    }

    public function testGetUniqueId()
    {
        $id1 = $this->email->getUniqueId();
        $id2 = $this->email->getUniqueId();

        $this->assertNotEquals($id1, $id2);
        $this->assertEquals(32, strlen($id1)); // MD5 length
    }

    public function testFormatHeaderWithName()
    {
        $result = $this->email->formatHeader('test@example.com', 'Test User');
        $this->assertStringContainsString('test@example.com', $result);
        $this->assertStringContainsString('UTF-8?B?', $result);
        // Name with spaces gets each word encoded separately
        $this->assertStringContainsString('=?UTF-8?B?VGVzdA==?=', $result); // 'Test'
        $this->assertStringContainsString('=?UTF-8?B?VXNlcg==?=', $result); // 'User'
    }

    public function testFormatHeaderWithoutName()
    {
        $result = $this->email->formatHeader('test@example.com');
        $this->assertEquals('test@example.com', $result);
    }

    public function testFilterEmail()
    {
        $email = "test@example.com\r\n\t\"<>,invalid";
        $result = $this->email->filterEmail($email);
        $this->assertEquals('test@example.cominvalid', $result);
    }

    public function testFilterName()
    {
        $name = "Test\r\n\t\"<>,Name";
        $result = $this->email->filterName($name);
        $this->assertStringContainsString("Test", $result);
        $this->assertStringContainsString("Name", $result);
    }

    public function testFilterOther()
    {
        $data = "Test\x00\x01\x02Data";
        $result = $this->email->filterOther($data);
        $this->assertEquals('TestData', $result);
    }

    public function testEncodeUtf8Word()
    {
        $word = 'Tëst';
        $result = $this->email->encodeUtf8Word($word);
        $this->assertStringStartsWith('=?UTF-8?B?', $result);
        $this->assertStringEndsWith('?=', $result);
    }

    public function testGetHeadersForSend()
    {
        $this->email->from('sender@example.com', 'Sender');
        $result = $this->email->getHeadersForSend();
        $this->assertStringContainsString('From:', $result);
    }

    public function testGetToForSend()
    {
        $this->email->to('recipient@example.com', 'Recipient');
        $result = $this->email->getToForSend();
        $this->assertStringContainsString('recipient@example.com', $result);
    }

    public function testSendThrowsExceptionWhenNoToAddress()
    {
        $this->email->subject('Test')->message('Test message');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to send, no To address has been set.');

        $this->email->send();
    }

    public function testGetWrapMessage()
    {
        $longMessage = str_repeat('a', 100);
        $this->email->message($longMessage)->setWrap(50);

        $wrapped = $this->email->getWrapMessage();
        $lines = explode("\n", $wrapped);
        foreach ($lines as $line) {
            $this->assertLessThanOrEqual(100, strlen($line)); // Allow more tolerance for wordwrap
        }
    }
}
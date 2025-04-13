<?php

namespace Tests\Unit;

use App\Services\FeedbackService;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tests\TestCase;

class FeedbackUnitTest extends TestCase
{
    protected $feedbackService;
    protected $processInput;

    public function setUp(): void
    {
        parent::setUp();
        $this->feedbackService = new FeedbackService();
        // Setup reflection class since the methods are private
        $reflectionClass = new ReflectionClass($this->feedbackService);
        $this->processInput = $reflectionClass->getMethod('processInput');
        $this->processInput->setAccessible(true);
    }

    public static function processInputProvider(): array
    {
        return [
            'processed' => [
                                '<script>alert("XSS")</script>',
                                true,
                                '<script>alert("XSS")</script>'
                        ],
            'unprocessed' => [
                                '<script>alert("XSS")</script>',
                                false,
                                '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;'
                            ],
        ];
    }

    #[DataProvider('processInputProvider')]
    public function testProcessingOnFeedbackStorage(string $feedbackInput, bool $useRaw, string $expected): void
    {
        $result = $this->processInput->invoke($this->feedbackService, $feedbackInput, $useRaw);
        $this->assertEquals($expected, $result);
    }
}

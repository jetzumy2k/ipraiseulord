<?php

namespace Tests\Unit;

use App\Services\BibleAiService;
use App\Services\BibleTopicAdviceService;
use App\Services\OpenAiAdviceService;
use App\Support\SpiritualAdviceFormatter;
use Mockery;
use Tests\TestCase;

class BibleAiServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_uses_openai_result_when_available(): void
    {
        $openAi = Mockery::mock(OpenAiAdviceService::class);
        $openAi->shouldReceive('isConfigured')->once()->andReturn(true);
        $openAi->shouldReceive('generateAdvice')->once()->andReturn([
            'answer' => 'LLM answer',
            'answer_sections' => [['type' => 'paragraph', 'text' => 'LLM answer']],
            'references' => [['reference' => 'John 3:16', 'text' => 'For God so loved the world.', 'version' => 'RSVCE']],
            'bible_version' => 'RSVCE',
        ]);

        $topicAdvice = Mockery::mock(BibleTopicAdviceService::class);
        $topicAdvice->shouldNotReceive('generateAdvice');

        $service = new BibleAiService($openAi, $topicAdvice);

        $result = $service->generateAdvice('How can I grow in faith?', 'RSVCE');

        $this->assertSame('LLM answer', $result['answer']);
    }

    public function test_it_falls_back_to_topic_advice_when_openai_is_not_configured(): void
    {
        $openAi = Mockery::mock(OpenAiAdviceService::class);
        $openAi->shouldReceive('isConfigured')->once()->andReturn(false);
        $openAi->shouldNotReceive('generateAdvice');

        $topicAdvice = Mockery::mock(BibleTopicAdviceService::class);
        $topicAdvice->shouldReceive('generateAdvice')->once()->andReturn([
            'answer' => 'Topic answer',
            'answer_sections' => [['type' => 'paragraph', 'text' => 'Topic answer']],
            'references' => [['reference' => 'Romans 1:26-27', 'text' => 'Sample text.', 'version' => 'RSVCE']],
            'bible_version' => 'RSVCE',
        ]);

        $service = new BibleAiService($openAi, $topicAdvice);

        $result = $service->generateAdvice('Is God allowing us to become gay or lesbian?', 'RSVCE');

        $this->assertSame('Topic answer', $result['answer']);
    }

    public function test_it_falls_back_to_topic_advice_when_openai_fails(): void
    {
        $openAi = Mockery::mock(OpenAiAdviceService::class);
        $openAi->shouldReceive('isConfigured')->once()->andReturn(true);
        $openAi->shouldReceive('generateAdvice')->once()->andReturn(null);

        $topicAdvice = Mockery::mock(BibleTopicAdviceService::class);
        $topicAdvice->shouldReceive('generateAdvice')->once()->andReturn([
            'answer' => 'Topic answer',
            'answer_sections' => [['type' => 'paragraph', 'text' => 'Topic answer']],
            'references' => [],
            'bible_version' => 'RSVCE',
        ]);

        $service = new BibleAiService($openAi, $topicAdvice);

        $result = $service->generateAdvice('I feel anxious about tomorrow.', 'RSVCE');

        $this->assertSame('Topic answer', $result['answer']);
    }

    public function test_it_returns_helpful_message_when_no_provider_matches(): void
    {
        $openAi = Mockery::mock(OpenAiAdviceService::class);
        $openAi->shouldReceive('isConfigured')->once()->andReturn(false);

        $topicAdvice = Mockery::mock(BibleTopicAdviceService::class);
        $topicAdvice->shouldReceive('generateAdvice')->once()->andReturn(null);

        $service = new BibleAiService($openAi, $topicAdvice);

        $result = $service->generateAdvice('What is the orbital period of Jupiter?', 'RSVCE');

        $this->assertSame([], $result['references']);
        $this->assertStringContainsString('could not find specific Scripture passages', $result['answer']);
    }

    public function test_openai_service_reports_unconfigured_when_key_missing(): void
    {
        config(['services.openai.api_key' => null]);

        $service = app(OpenAiAdviceService::class);

        $this->assertFalse($service->isConfigured());
    }

    public function test_formatter_builds_sections_from_references(): void
    {
        $references = [
            ['reference' => 'John 3:16', 'text' => 'For God so loved the world.', 'version' => 'RSVCE'],
        ];

        $sections = SpiritualAdviceFormatter::sectionsFromReferences($references, ['Guidance paragraph.']);

        $this->assertSame('intro', $sections[0]['type']);
        $this->assertSame('Guidance paragraph.', $sections[1]['text']);
        $this->assertSame('scripture', $sections[2]['type']);
    }
}

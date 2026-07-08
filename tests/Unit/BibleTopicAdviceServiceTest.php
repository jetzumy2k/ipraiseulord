<?php

namespace Tests\Unit;

use App\Services\BibleTopicAdviceService;
use Tests\TestCase;

class BibleTopicAdviceServiceTest extends TestCase
{
    public function test_it_matches_sexuality_questions_to_relevant_references(): void
    {
        $service = app(BibleTopicAdviceService::class);

        $topic = $service->matchTopic('Is God allowing us to become gay or lesbian?');

        $this->assertNotNull($topic);
        $this->assertContains('Romans 1:26-27', $topic['references']);
        $this->assertContains('1 Corinthians 6:9-10', $topic['references']);
        $this->assertNotEmpty($topic['guidance']);
    }

    public function test_it_returns_null_when_no_topic_matches(): void
    {
        $service = app(BibleTopicAdviceService::class);

        $topic = $service->matchTopic('What is the orbital period of Jupiter?');

        $this->assertNull($topic);
    }

    public function test_it_matches_anxiety_keywords(): void
    {
        $service = app(BibleTopicAdviceService::class);

        $topic = $service->matchTopic('I feel anxious about the future.');

        $this->assertNotNull($topic);
        $this->assertContains('Philippians 4:6-7', $topic['references']);
    }

    public function test_it_matches_newly_added_topics(): void
    {
        $service = app(BibleTopicAdviceService::class);

        $cases = [
            'Is war ever justified?' => 'Isaiah 2:4',
            'What does the Bible say about government corruption?' => 'Proverbs 28:16',
            'What about transgender identity?' => 'Genesis 1:27',
            'Why are Christians hypocritical?' => 'Matthew 23:27-28',
            'I doubt God exists' => 'Mark 9:24',
            'What is treason in the Bible?' => 'Luke 22:48',
            'Should Christians vote in elections?' => 'Micah 6:8',
            'What does the Bible say about immigration?' => 'Leviticus 19:33-34',
            'Why did Israel go into exile?' => 'Ezra 1:1-3',
            'What is the role of the Pope?' => 'Matthew 16:18-19',
            'How should the Church respond to abuse scandals?' => 'Matthew 18:6',
        ];

        foreach ($cases as $question => $expectedReference) {
            $topic = $service->matchTopic($question);

            $this->assertNotNull($topic, "Expected topic match for: {$question}");
            $this->assertContains($expectedReference, $topic['references'], "Missing reference for: {$question}");
        }
    }
}

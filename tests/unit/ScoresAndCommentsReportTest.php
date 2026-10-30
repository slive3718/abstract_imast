<?php

use App\Services\reports\ScoresAndCommentsReport;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ScoresAndCommentsReportTest extends CIUnitTestCase
{
    private ScoresAndCommentsReport $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->report = new ScoresAndCommentsReport();
    }

    public function testOlympicScoreDropsTheHighestAndLowestWhenThereAreAtLeastThreeScores(): void
    {
        $score = $this->invokePrivate('calculateOlympicScore', [
            [1.0, 2.0, 3.0, 100.0],
            106.0,
            4,
        ]);

        $this->assertSame(2.5, $score);
    }

    public function testOlympicScoreIsZeroWithFewerThanThreeScores(): void
    {
        $score = $this->invokePrivate('calculateOlympicScore', [
            [4.0, 8.0],
            12.0,
            2,
        ]);

        $this->assertSame(0.0, $score);
    }

    public function testProcessReviewsCalculatesScoresAndFormatsReviewerComments(): void
    {
        $property = new ReflectionProperty(ScoresAndCommentsReport::class, 'usersById');
        $property->setAccessible(true);
        $property->setValue($this->report, [
            7 => ['name' => 'Ada', 'surname' => 'Lovelace'],
        ]);

        [$text, $total, $average, $olympic] = $this->invokePrivate('processReviews', [
            [
                ['reviewer_id' => 7, 'total_score' => '1', 'reviewer_comment' => 'First comment'],
                ['reviewer_id' => 8, 'total_score' => '3', 'reviewer_comment' => 'Second comment'],
                ['reviewer_id' => 9, 'total_score' => '5', 'reviewer_comment' => 'Third comment'],
                ['reviewer_id' => 10, 'total_score' => 'coi', 'reviewer_comment' => ''],
            ],
        ]);

        $this->assertSame(9.0, $total);
        $this->assertSame(3.0, $average);
        $this->assertSame(3.0, $olympic);
        $this->assertStringContainsString('Ada Lovelace', $text);
        $this->assertStringContainsString('First comment', $text);
        $this->assertStringContainsString('Reviewer #8', $text);
        $this->assertStringContainsString('Score: COI', $text);
    }

    public function testProcessReviewsReturnsDefaultValuesWhenThereAreNoReviews(): void
    {
        $result = $this->invokePrivate('processReviews', [[]]);

        $this->assertSame(['No reviews yet', 0, 0, 0], $result);
    }

    private function invokePrivate(string $method, array $arguments = [])
    {
        $reflection = new ReflectionMethod($this->report, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($this->report, $arguments);
    }
}

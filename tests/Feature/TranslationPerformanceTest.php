<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

use PHPUnit\Framework\Attributes\Group;

#[Group('performance')]
class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    public function test_large_dataset_handling()
    {
        // Create 10,000 translations with 3 tags each
        Translation::factory()
            ->count(1000)
            ->hasTags(3)
            ->create();

        $start = microtime(true);

        $this->getJson('/api/translations');

        $duration = microtime(true) - $start;

        $this->assertLessThan(10.0, $duration,
            'Response time should be under 10 seconds for 10,00 records');
    }
}

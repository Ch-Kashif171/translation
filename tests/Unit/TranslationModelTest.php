<?php

namespace Tests\Unit;

use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Group;

#[Group('translation')]
class TranslationModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    public function test_translation_creation()
    {
        $translation = Translation::factory()->create([
            'locale' => 'en',
            'key' => 'welcome.message',
            'value' => 'Welcome!'
        ]);

        $this->assertDatabaseHas('translations', $translation->toArray());
    }

    public function test_translation_tag_relationships()
    {
        $translation = Translation::factory()
            ->has(Tag::factory()->count(2))
            ->create();

        $this->assertCount(2, $translation->tags);
        $this->assertInstanceOf(Tag::class, $translation->tags->first());
    }

    public function test_unique_locale_key_constraint()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'unique.key']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Translation::factory()->create(['locale' => 'en', 'key' => 'unique.key']);
    }
}

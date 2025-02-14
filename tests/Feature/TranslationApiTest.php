<?php

namespace Tests\Feature;

use App\Http\Controllers\API\TranslationController;
use App\Http\Requests\TranslateRequest;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use PHPUnit\Framework\Attributes\Group;

#[Group('translation-api')]
class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    public function test_translation_store_creates_translation_and_assigns_tags()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some tags
        $tags = Tag::factory()->count(2)->create();
        $tagNames = $tags->pluck('name')->toArray();

        // Prepare request data
        $data = [
            'key' => 'hello',
            'value' => 'Hello, World!',
            'locale' => 'en',
            'tags' => json_encode($tagNames),
        ];

        // Send request
        $response = $this->postJson(route('translations.store'), $data);

        // Assertions
        $response->assertStatus(201);
        $response->assertJson(['data' => ['status' => false]]);

        $this->assertDatabaseHas('translations', [
            'key' => 'hello',
            'value' => 'Hello, World!',
            'locale' => 'en',
        ]);

        $translation = Translation::where('key', 'hello')->first();
        $this->assertNotNull($translation);
        $this->assertCount(2, $translation->tags);
    }

    public function test_validation_errors()
    {
        $response = $this->postJson('/api/translations', [
            'key' => 'missing.locale'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale', 'value']);
    }

    public function test_fetch_all_translations()
    {
        Translation::factory()->count(3)->create();

        $response = $this->getJson(route('translations.index'));

        $response->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['status', 'message', 'data']);
    }

    public function test_filter_translations_by_tags()
    {
        $tag1 = Tag::factory()->create(['name' => 'tag1']);
        $tag2 = Tag::factory()->create(['name' => 'tag2']);

        $translation1 = Translation::factory()->create();
        $translation2 = Translation::factory()->create();

        $translation1->tags()->attach([$tag1->id]);
        $translation2->tags()->attach([$tag1->id, $tag2->id]);

        $response = $this->getJson(route('translations.index', ['tags' => 'tag1,tag2']));

        $response->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonCount(1, 'data');
    }

    public function test_export_translations()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'welcome', 'value' => 'Welcome']);
        Translation::factory()->create(['locale' => 'fr', 'key' => 'welcome', 'value' => 'Bienvenue']);

        $response = $this->getJson(route('translations.export'));

        $response->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['status', 'message', 'data'])
            ->assertJsonFragment(['en' => ['welcome' => 'Welcome']])
            ->assertJsonFragment(['fr' => ['welcome' => 'Bienvenue']]);
    }

    public function test_translation_updates_translation_and_assigns_tags()
    {
        // Create a translation
        $translation = Translation::factory()->create();

        // Ensure tags exist in the database
        $tags = collect([
            Tag::factory()->create(['name' => 'web']),
            Tag::factory()->create(['name' => 'mobile']),
            Tag::factory()->create(['name' => 'desktop']),
        ]);

        // Extract tag names
        $tagNames = $tags->pluck('name')->toArray();

        // Data for update
        $updateData = [
            'locale' => 'urs',
            'key' => 'platform',
            'value' => 'iPhone',
            'tags' => json_encode($tagNames),
        ];

        // Send PUT request
        $response = $this->json('PUT', route('translations.update', $translation->id), $updateData);

        // Assertions
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Translation has been updated successfully',
            ]);

        // Verify the translation was updated
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'locale' => 'urs',
            'key' => 'platform',
            'value' => 'iPhone',
        ]);

        // Reload the translation to check assigned tags
        $this->assertCount(3, $translation->fresh()->tags);
    }

    public function test_translation_show(): void
    {
        // Create a translation instance with related tags
        $translation = Translation::factory()->hasTags(3)->create();

        // Send GET request
        $response = $this->getJson(route('translations.show', $translation->id));

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Translation fetched',
                'data' => [
                    'translation' => [
                        'id' => $translation->id,
                    ]
                ]
            ]);

        // Assert the tags are loaded
        $this->assertNotEmpty($response->json('data.translation.tags'));
    }

    public function test_delete_translation()
    {
        // Create a translation instance
        $translation = Translation::factory()->create();

        // Send DELETE request
        $response = $this->deleteJson(route('translations.destroy', $translation->id));

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Deleted successfully',
            ]);

        // Use assertSoftDeleted if using soft deletes
        $this->assertSoftDeleted('translations', ['id' => $translation->id]);
    }
}

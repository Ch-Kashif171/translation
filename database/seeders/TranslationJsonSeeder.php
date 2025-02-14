<?php

namespace Database\Seeders;

use App\Traits\SeederTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationJsonSeeder extends Seeder
{
    use SeederTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = microtime(true); // Start timer

        // Clear existing data
        DB::table('tag_translation')->delete();
        DB::table('translations')->delete();
        DB::table('tags')->delete();

        $translations = json_decode(
            file_get_contents(database_path('seeders/json/translations.json')),
            true
        );

        $tags = json_decode(
            file_get_contents(database_path('seeders/json/tags.json')),
            true
        );

        $tagIds = $this->insertTags($tags);

        $this->insertTranslation($translations, $tagIds);

        $end = microtime(true); // End timer
        $executionTime = ($end - $start) * 1000; // Convert to milliseconds

        echo "Seeder completed in {$executionTime} ms\n";
    }
}

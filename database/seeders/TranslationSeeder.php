<?php

namespace Database\Seeders;
use App\Helpers\Constants;
use App\Models\Translation;
use App\Models\Tag;
use App\Traits\SeederTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TranslationSeeder extends Seeder
{
    use SeederTrait;

    public function run()
    {
        $start = microtime(true); // Start timer

        // Deleting existing data
        DB::table('tag_translation')->delete();
        DB::table('translations')->delete();
        DB::table('tags')->delete();

        $tagIds = $this->insertTags(Constants::TAGS);

        $this->insertTranslation(Constants::TRANSLATION, $tagIds);

        $end = microtime(true); // End timer
        $executionTime = ($end - $start) * 1000; // Convert to milliseconds

        echo "Seeder completed in {$executionTime} ms\n";
    }

}

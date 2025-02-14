<?php

namespace App\Traits;

use App\Models\Tag;
use App\Models\Translation;

trait SeederTrait
{
    /**
     * @param $translations
     * @param $tagIds
     * @return void
     */
    protected function insertTranslation($translations, $tagIds)
    {
        foreach ($translations as $locale => $keys) {
            foreach ($keys as $key => $value) {
                $translation = Translation::create([
                    'locale' => $locale,
                    'key' => $key,
                    'value' => $value
                ]);

                // Attach tags based on key prefix
                $this->attachTags($translation, $key, $tagIds);
            }
        }
    }

    /**
     * @param $tags
     * @return array
     */
    protected function insertTags($tags): array
    {
        $tagIds = [];
        foreach ($tags as $key => $tag) {
            $tagIds[$key] = Tag::create(['name' => $tag]);
        }
        return $tagIds;
    }

    /**
     * @param $translation
     * @param $key
     * @param $tags
     * @return void
     */
    protected function attachTags($translation, $key, $tags)
    {
        $tagMapping = [
            'welcome' => ['web', 'mobile', 'desktop'],
            'auth' => ['web', 'mobile'],
            'buttons' => ['web', 'desktop'],
        ];

        foreach ($tagMapping as $prefix => $tagNames) {
            if (str_starts_with($key, $prefix)) {
                $translation->tags()->attach(
                    collect($tagNames)->map(fn($name) => $tags[$name]->id)
                );
                break;
            }
        }
    }

}

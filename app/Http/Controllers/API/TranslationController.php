<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslateRequest;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TranslationController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        try {

            $query = Translation::with('tags');

            if ($request->tags) {
                $tags = explode(',', $request->tags);
                $query->whereHas('tags', fn($q) => $q->whereIn('name', $tags), '=', count($tags));
            }

            if ($request->search) {
                $query->where(fn($q) => $q
                    ->where('key', 'LIKE', "%{$request->search}%")
                    ->orWhere('value', 'LIKE', "%{$request->search}%"));
            }

            return response()->json([
                'status' => true,
                'message' => "Translate fetched successfully!",
                'data' => $query->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(TranslateRequest $request): mixed
    {
        try {

            $data = $request->all();
            $translation = Translation::create($data);

            if (isset($data['tags'])) {
                $tags = Tag::whereIn('name', json_decode($data['tags'], true))->get();
                $translation->tags()->sync($tags);
            }

            return response()->json([
                'data' => [
                    'status' => false,
                    'translation' => $translation->load('tags')
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Translation $translation
     * @return mixed
     */
    public function show(Translation $translation)
    {
        return response()->json([
            'status' => true,
            'message' => 'Translation fetched',
            'data' => [
                'translation' => $translation->load('tags')
            ]
        ]);
    }

    /**
     * @param TranslateRequest $request
     * @param Translation $translation
     * @return mixed
     */
    public function update(TranslateRequest $request, Translation $translation)
    {
        try {

            $data = $request->all();

            $translation->update($data);

            if (isset($data['tags'])) {
                $tags = Tag::whereIn('name', json_decode($data['tags'], true))->get();
                $translation->tags()->sync($tags);
            }

            return response()->json([
                'status' => true,
                'message' => 'Translation has been updated successfully',
                'data' => [
                    'translation' => $translation->load('tags')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Translation $translation
     * @return mixed
     */
    public function destroy(Translation $translation): mixed
    {
        try {

            $translation->delete();
            return response()->json([
                'status' => true,
                "message" => "Deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function export()
    {
        try {

            $translations = Translation::all();
            $export = [];

            foreach ($translations as $translation) {
                Arr::set($export, "{$translation->locale}.{$translation->key}", $translation->value);
            }

            return response()->json([
                'status' => true,
                'message' => 'Translation exported successfully!',
                'data' => $export,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = Category::create($request->all());
        $category->save();

        return response()->json($category, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::where('id', $id);

        if ($category->exists()) {
            return response()->json($category->get(), 200);
        }

        return response()->json([
            'message' => 'Category not Found.'
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::where('id', $id);

        if ($category->exists()) {
            $category->update($request->all());
            return response()->json($category->get(), 200);
        }

        return response()->json([
            'message' => 'Category not found.'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::where('id', $id);
        if ($category->exists()) {
            $category->delete();
            return response()->json($category, 202);
        }

        return response()->json([
            'message' => 'Category not found.'
        ], 404);
    }

    public function getFromQuestion(string $questionId)
    {
        $question = Question::find($questionId);

        if ($question) {
            return response()->json($question->categories, 200);
        }

        return response()->json([
            'message' => 'Question not found.'
        ]);
    }

    public function setToQuestion(Request $request, string $questionId)
    {
        $question = Question::find($questionId);

        if (!$question) {
            return response()->json(['message' => 'Question not found.'], 404);
        }

        $categoryIds = $request->input('categories', []);

        $existingCategoryCount = Category::whereIn('id', $categoryIds)->count();

        if (count($categoryIds) !== $existingCategoryCount) {
            return response()->json(['message' => 'Some categories not found.'], 404);
        }

        $question->categories()->sync($categoryIds);

        $question = $question->with([
            'categories',
            'answers' => function ($query) {
                $query->with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]);
            },
            'user' => function ($query) {
                $query->select('id', 'name');
            }
        ])->find($questionId);

        return response()->json($question->categories, 200);
    }

    public function removeFromQuestion(Request $request, string $questionId, string $categoryId)
    {
        // Encontre a questão pelo ID
        $question = Question::find($questionId);

        if ($question) {
            // Encontre a categoria pelo ID
            $category = Category::find($categoryId);

            if ($category) {
                // Verifique se a categoria está associada à questão
                if (!$question->categories->contains($categoryId)) {
                    return response()->json([
                        'message' => 'Category is not associated with the question.'
                    ], 400);
                }
                // Remova a categoria da questão
                $question->categories()->detach($categoryId);
                $question->load('categories');
                return response()->json($question->categories, 200);
            }
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }
}

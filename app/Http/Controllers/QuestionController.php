<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::with([
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
        ])
        ->orderByDesc('created_at')
        ->get();
        return response()->json($questions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->request->set("user_id", $user->id);

        $question = Question::create($request->all());

        $question->save();

        return response()->json($question, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Question::with([
            'categories',
            'answers' => function ($query) {
                $query->with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])->orderByDesc('created_at');
            },
            'user' => function ($query) {
                $query->select('id', 'name');
            }
        ])
        ->find($id);

        if ($question) {
            return response()->json($question, 200);
        }

        return response()->json([
            'message' => 'Not Found.'
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $question = Question::find($id);

        if ($question) {
            $question->update($request->all());
            return response()->json($question, 200);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::where('id', $id);
        if ($question->exists()) {
            $question->delete();
            return response()->json($question, 202);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }
}

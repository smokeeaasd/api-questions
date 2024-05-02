<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    private $user;
    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $questionId)
    {
        $question = Question::find($questionId);

        if ($question) {
            $answers = Answer::with([
                "user" => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->where('question_id', $question->id)
            ->get();

            return response()->json($answers, 200);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $questionId)
    {
        $question = Question::find($questionId);

        if ($question) {
            $answer = new Answer;
            $answer->answer = $request->answer;
            $answer->user_id = $this->user->id;
            $answer->question_id = $questionId;

            $answer->save();

            return response()->json($answer, 200);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $questionId, string $id)
    {
        $question = Question::find($questionId);
        if ($question) {
            $answer = Answer::with(['user' => function ($query) {
                $query->select('id','name');
            }])->find($id);

            if ($answer) {
                return response()->json($answer, 200);
            }

            return response()->json([
                'message' => 'Answer not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Question not found'
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $questionId, string $id)
    {
        $question = Question::find($questionId);

        if ($question) {
            $answer = Answer::find($id);

            if ($answer) {
                $answer->update($request->all());
                return response()->json($answer, 200);
            }

            return response()->json([
                'message' => 'Answer not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $questionId, string $id)
    {
        $question = Question::find($questionId);

        if ($question) {
            $answer = Answer::find($id);

            if ($answer) {
                $answer->delete();
                $question->load('answers');
                return response()->json($question->answers, 200);
            }

            return response()->json([
                'message' => 'Answer not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Question not found.'
        ], 404);
    }
}

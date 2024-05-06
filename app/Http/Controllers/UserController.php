<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function answers(Request $request)
    {
        $user = Auth::user();
        $answers = Answer::where('user_id', $user->id)->get();
        return response()->json($answers);
    }

    public function questions(Request $request)
    {
        $user = Auth::user();
        $questions = Question::where('user_id', $user->id)->get();
        return response()->json($questions);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $token = $user->token();
        $token->revoke();


        return response()->json([
            'message' => 'Logged out.'
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $user = User::with([
            'questions',
            'answers'
        ])->find($user->id);
        return response()->json($user);
    }
}

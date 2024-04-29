<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	public function register(Request $request)
	{
		$request->validate([
			'name' => 'required|string',
			'email' => 'required|email|unique:users',
			'password' => 'required|string|min:8'
		]);

		$user = User::create([
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => Hash::make($request->input('password')),
		]);

		return response()->json([
			'message' => 'Account created',
			'user' => $user,
		]);
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email|string',
			'password' => 'required|string'
		]);

		$user = User::where('email', $request->input('email'))->first();

		if ($user && Hash::check($request->input('password'), $user->password)) {
			$token = $user->createToken('Personal Access Token')->accessToken;

			return response()->json(['token' => $token]);
		}

		return response()->json([
			'error' => 'Invalid credentials.',
		], 401);
	}
}
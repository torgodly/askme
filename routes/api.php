<?php

use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// Route for user registration and returning the token
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Assuming you are using Laravel Sanctum for authentication
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

// Route for login and returning the token
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

// Route for creating a question (requires authentication)
Route::middleware('auth:sanctum')->group(function () {

    //user
    Route::get('user', function () {
        return response()->json(Auth::user());
    });

    Route::post('/questions', function (Request $request) {
        $request->validate([
            'body' => 'required|string',
        ]);

        $question = Question::create([
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);

        return response()->json($question, 201);
    });

    // Route for getting all questions (public access)
    Route::get('/questions', function () {
        $questions = Auth::user()->questions;
        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No questions found'], 404);
        }
        return response()->json($questions);
    });

// Route for viewing a single question (public access)
    Route::get('/questions/{id}', function ($id) {
        // Find the question manually or return a 404 error
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        // Check if the authenticated user is the owner of the question
        if ($question->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // If authorized, return the question
        return response()->json($question);
    });

});


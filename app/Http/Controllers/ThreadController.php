<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    /**
     * Crea un nuevo hilo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createThread(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $thread = new Thread();
        $thread->title = $validatedData['title'];
        $thread->content = $validatedData['content'];
        $thread->user_id = $user->id;
        $thread->save();

        return response()->json(['message' => 'Thread created successfully!', 'thread' => $thread], 201);
    }
}

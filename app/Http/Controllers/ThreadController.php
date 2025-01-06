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

    /**
     * Obtener los threads del usuario autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyThreads(Request $request)
    {
        $user = $request->user();

        $threads = Thread::where('user_id', $user->id)->get();

        return response()->json([
            'threads' => $threads,
        ], 200);
    }

    /**
     * Eliminar un hilo específico.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteThread(Request $request, $id)
    {
        $user = $request->user();

        $thread = Thread::where('id', $id)->where('user_id', $user->id)->first();

        if (!$thread) {
            return response()->json(['message' => 'Thread not found or you are not authorized to delete this thread.'], 404);
        }

        $thread->delete();

        return response()->json(['message' => 'Thread deleted successfully.'], 200);
    }

    public function getAllThreads(Request $request)
    {
        $threads = Thread::all();

        return response()->json([
            'threads' => $threads,
        ], 200);
    }
}

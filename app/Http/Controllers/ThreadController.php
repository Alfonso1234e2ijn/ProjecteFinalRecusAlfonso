<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/threads",
     *     summary="Create a new thread",
     *     description="Creates a new thread for the authenticated user.",
     *     operationId="createThread",
     *     tags={"Threads"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string", description="Title of the thread"),
     *                 @OA\Property(property="content", type="string", description="Content of the thread"),
     *                 required={"title", "content"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Thread created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="thread", 
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request (missing title or content)."
     *     )
     * )
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
     * @OA\Get(
     *     path="/threads/my",
     *     summary="Get threads of the authenticated user",
     *     description="Retrieves all threads created by the authenticated user.",
     *     operationId="getMyThreads",
     *     tags={"Threads"},
     *     @OA\Response(
     *         response=200,
     *         description="Threads found successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="threads", 
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
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
     * @OA\Delete(
     *     path="/threads/{id}",
     *     summary="Delete a specific thread",
     *     description="Deletes a thread created by the authenticated user.",
     *     operationId="deleteThread",
     *     tags={"Threads"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the thread to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thread deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thread not found or not authorized to delete this thread."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
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

    /**
     * @OA\Get(
     *     path="/threads",
     *     summary="Get all threads",
     *     description="Retrieves all threads in the system.",
     *     operationId="getAllThreads",
     *     tags={"Threads"},
     *     @OA\Response(
     *         response=200,
     *         description="Threads found successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="threads", 
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getAllThreads(Request $request)
    {
        $threads = Thread::all();

        return response()->json([
            'threads' => $threads,
        ], 200);
    }
}

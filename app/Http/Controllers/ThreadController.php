<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{

    /**
     * @swagger
     * /threads:
     *   post:
     *     summary: Create a new thread
     *     description: Creates a new thread for the authenticated user.
     *     requestBody:
     *       required: true
     *       content:
     *         application/json:
     *           schema:
     *             type: object
     *             properties:
     *               title:
     *                 type: string
     *                 description: Title of the thread
     *               content:
     *                 type: string
     *                 description: Content of the thread
     *             required:
     *               - title
     *               - content
     *     responses:
     *       201:
     *         description: Thread created successfully.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 message:
     *                   type: string
     *                 thread:
     *                   type: object
     *                   properties:
     *                     id:
     *                       type: integer
     *                     title:
     *                       type: string
     *                     content:
     *                       type: string
     *                     user_id:
     *                       type: integer
     *                     created_at:
     *                       type: string
     *                     updated_at:
     *                       type: string
     *       401:
     *         description: User not authenticated.
     *       400:
     *         description: Bad request (missing title or content).
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
     * @swagger
     * /threads/my:
     *   get:
     *     summary: Get threads of the authenticated user
     *     description: Retrieves all threads created by the authenticated user.
     *     responses:
     *       200:
     *         description: Threads found successfully.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 threads:
     *                   type: array
     *                   items:
     *                     type: object
     *                     properties:
     *                       id:
     *                         type: integer
     *                       title:
     *                         type: string
     *                       content:
     *                         type: string
     *                       user_id:
     *                         type: integer
     *                       created_at:
     *                         type: string
     *                       updated_at:
     *                         type: string
     *       401:
     *         description: User not authenticated.
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
     * @swagger
     * /threads/{id}:
     *   delete:
     *     summary: Delete a specific thread
     *     description: Deletes a thread created by the authenticated user.
     *     parameters:
     *       - name: id
     *         in: path
     *         required: true
     *         description: ID of the thread to delete.
     *         schema:
     *           type: integer
     *     responses:
     *       200:
     *         description: Thread deleted successfully.
     *       404:
     *         description: Thread not found or not authorized to delete this thread.
     *       401:
     *         description: User not authenticated.
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
     * @swagger
     * /threads:
     *   get:
     *     summary: Get all threads
     *     description: Retrieves all threads in the system.
     *     responses:
     *       200:
     *         description: Threads found successfully.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 threads:
     *                   type: array
     *                   items:
     *                     type: object
     *                     properties:
     *                       id:
     *                         type: integer
     *                       title:
     *                         type: string
     *                       content:
     *                         type: string
     *                       user_id:
     *                         type: integer
     *                       created_at:
     *                         type: string
     *                       updated_at:
     *                         type: string
     */
    public function getAllThreads(Request $request)
    {
        $threads = Thread::all();

        return response()->json([
            'threads' => $threads,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Thread;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    /**
     * @swagger
     * /responses/thread/{thread_id}:
     *   get:
     *     summary: Get responses for a specific thread
     *     description: Retrieves all responses for a thread using its ID.
     *     parameters:
     *       - name: thread_id
     *         in: path
     *         required: true
     *         description: The thread ID to fetch associated responses.
     *         schema:
     *           type: integer
     *     responses:
     *       200:
     *         description: Responses found successfully.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 responses:
     *                   type: array
     *                   items:
     *                     type: object
     *                     properties:
     *                       id:
     *                         type: integer
     *                       content:
     *                         type: string
     *                       user_id:
     *                         type: integer
     *                       thread_id:
     *                         type: integer
     *                       created_at:
     *                         type: string
     *                       updated_at:
     *                         type: string
     *       404:
     *         description: Thread not found.
     *       500:
     *         description: Error while fetching responses.
     */
    public function getResponsesByThread($thread_id)
    {
        $responses = Response::
            where('thread_id', $thread_id)
            ->get();

        return response()->json([
            'responses' => $responses,
        ], 200);
    }

    /**
     * @swagger
     * /responses/{thread_id}:
     *   get:
     *     summary: Get responses for a thread (with user data)
     *     description: Retrieves all responses for a specific thread, including the user data for each response.
     *     parameters:
     *       - name: thread_id
     *         in: path
     *         required: true
     *         description: The thread ID to fetch responses.
     *         schema:
     *           type: integer
     *     responses:
     *       200:
     *         description: Responses found successfully with user data.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 success:
     *                   type: boolean
     *                 responses:
     *                   type: array
     *                   items:
     *                     type: object
     *                     properties:
     *                       id:
     *                         type: integer
     *                       content:
     *                         type: string
     *                       user:
     *                         type: object
     *                         properties:
     *                           id:
     *                             type: integer
     *                           name:
     *                             type: string
     *                           email:
     *                             type: string
     *                       thread_id:
     *                         type: integer
     *                       created_at:
     *                         type: string
     *                       updated_at:
     *                         type: string
     *       500:
     *         description: Error while fetching responses.
     */
    public function handleResponses(Request $request, $thread_id = null)
    {
        if ($request->isMethod('get')) {
            // Get the responses of each thread
            try {
                $responses = Response::with('user')
                    ->where('thread_id', $thread_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'responses' => $responses,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch responses.',
                ], 500);
            }
        } elseif ($request->isMethod('post')) {
            // Create a new response
            $validatedData = $request->validate([
                'content' => 'required|string',
                'thread_id' => 'required|exists:threads,id',
            ]);

            try {
                $response = Response::create([
                    'content' => $validatedData['content'],
                    'user_id' => $request->user()->id,
                    'thread_id' => $validatedData['thread_id'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Response created successfully',
                    'response' => $response,
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create response.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid request method.',
        ], 405);
    }

    /**
     * @swagger
     * /responses/user/{response_id}:
     *   get:
     *     summary: Get the user who made a specific response
     *     description: Retrieves the user related to a response using the response ID.
     *     parameters:
     *       - name: response_id
     *         in: path
     *         required: true
     *         description: The response ID to get the associated user data.
     *         schema:
     *           type: integer
     *     responses:
     *       200:
     *         description: User found successfully.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 success:
     *                   type: boolean
     *                 user:
     *                   type: object
     *                   properties:
     *                     id:
     *                       type: integer
     *                     name:
     *                       type: string
     *                     email:
     *                       type: string
     *       404:
     *         description: Response or user not found.
     *       500:
     *         description: Error while fetching user data.
     */
    public function getUserByResponse($response_id)
    {
        try {
            // Find the response of the relationated user
            $response = Response::with('user')->findOrFail($response_id);

            // Return only the user data for each response
            return response()->json([
                'success' => true,
                'user' => $response->user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Response or user not found.',
            ], 404);
        }
    }

}

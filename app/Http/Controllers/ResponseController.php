<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Thread;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function getResponsesByThread($thread_id)
    {
        $responses = Response::
            where('thread_id', $thread_id)
            ->get();

        return response()->json([
            'responses' => $responses,
        ], 200);
    }

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

<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Thread;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/responses/thread/{thread_id}",
     *     summary="Get responses for a specific thread",
     *     description="Retrieves all responses for a thread using its ID.",
     *     operationId="getResponsesByThread",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="thread_id",
     *         in="path",
     *         required=true,
     *         description="The thread ID to fetch associated responses.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Responses found successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(
     *                         property="responses",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="content", type="string"),
     *                             @OA\Property(property="user_id", type="integer"),
     *                             @OA\Property(property="thread_id", type="integer"),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thread not found."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while fetching responses."
     *     )
     * )
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
     * @OA\Get(
     *     path="/responses/{thread_id}",
     *     summary="Get responses for a thread (with user data)",
     *     description="Retrieves all responses for a specific thread, including the user data for each response.",
     *     operationId="getResponsesWithUser",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="thread_id",
     *         in="path",
     *         required=true,
     *         description="The thread ID to fetch responses.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Responses found successfully with user data.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean"),
     *                     @OA\Property(
     *                         property="responses",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="content", type="string"),
     *                             @OA\Property(
     *                                 property="user",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="email", type="string")
     *                             ),
     *                             @OA\Property(property="thread_id", type="integer"),
     *                             @OA\Property(property="created_at", type="string"),
     *                             @OA\Property(property="updated_at", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while fetching responses."
     *     )
     * ),
     * @OA\Post(
     *     path="/responses/{thread_id}",
     *     summary="Create a new response for a thread",
     *     description="Create a new response for a specific thread.",
     *     operationId="createResponse",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="thread_id",
     *         in="path",
     *         required=true,
     *         description="The thread ID to create a response in.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="content", type="string", description="Content of the response"),
     *                     required={"content"}
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Response created successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while creating response."
     *     )
     * )
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
     * @OA\Get(
     *     path="/responses/user/{response_id}",
     *     summary="Get the user who made a specific response",
     *     description="Retrieves the user related to a response using the response ID.",
     *     operationId="getUserByResponse",
     *     tags={"Responses"},
     *     @OA\Parameter(
     *         name="response_id",
     *         in="path",
     *         required=true,
     *         description="The response ID to get the associated user data.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="email", type="string")
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Response or user not found."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while fetching user data."
     *     )
     * )
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

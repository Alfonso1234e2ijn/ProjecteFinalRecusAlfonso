<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Thread;
use App\Models\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * @OA\Post(
     *     path="/votes/{responseId}",
     *     summary="Register a vote on a response",
     *     description="Allows an authenticated user to vote on a response. Users cannot vote on their own responses.",
     *     operationId="vote",
     *     tags={"Votes"},
     *     @OA\Parameter(
     *         name="responseId",
     *         in="path",
     *         required=true,
     *         description="ID of the response to vote on",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="action", type="boolean", description="The vote action (true for upvote, false for downvote)"),
     *                 required={"action"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vote registered successfully."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid response ID or invalid request data."
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User cannot vote on their own response."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Response not found."
     *     )
     * )
     */
    public function vote(Request $request, $responseId)
    {
        if (!is_numeric($responseId)) {
            return response()->json(['error' => 'Invalid response ID.'], 400);
        }

        $request->validate([
            'action' => 'required|boolean',
        ]);

        $user = Auth::user();

        $response = \App\Models\Response::find($responseId);
        if (!$response) {
            return response()->json(['error' => 'Response not found.'], 404);
        }

        if ($response->user_id === $user->id) {
            return response()->json(['error' => 'You cannot vote on your own response.'], 403);
        }

        $vote = Vote::firstOrNew([
            'user_id' => $user->id,
            'response_id' => $responseId,
        ]);

        if ($vote->type !== $request->action) {
            $vote->type = $request->action;
            $vote->save();
        }

        return response()->json(['message' => 'Vote registered successfully.']);
    }

    /**
     * @OA\Get(
     *     path="/votes/unread",
     *     summary="Get all unread votes for the authenticated user",
     *     description="Retrieves all unread votes for the authenticated user, including the associated response details.",
     *     operationId="getUnreadVotes",
     *     tags={"Votes"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved unread votes.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="unreadVotes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="response_id", type="integer"),
     *                     @OA\Property(property="type", type="boolean", description="The vote action (true for upvote, false for downvote)"),
     *                     @OA\Property(property="read", type="boolean", description="Whether the vote has been read by the user."),
     *                     @OA\Property(property="created_at", type="string"),
     *                     @OA\Property(property="updated_at", type="string"),
     *                     @OA\Property(
     *                         property="response",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="content", type="string"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="thread_id", type="integer"),
     *                         @OA\Property(property="created_at", type="string"),
     *                         @OA\Property(property="updated_at", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized (User not authenticated)."
     *     )
     * )
     */
    public function getUnreadVotes(Request $request)
    {
        $userId = auth()->user()->id;
        $unreadVotes = Vote::where('user_id', $userId)
            ->where('read', 0)
            ->with('response')
            ->get();

        return response()->json(['unreadVotes' => $unreadVotes]);
    }

}

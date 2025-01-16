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
     * @swagger
     * /votes/{responseId}:
     *   post:
     *     summary: Register a vote on a response
     *     description: Allows an authenticated user to vote on a response. Users cannot vote on their own responses.
     *     parameters:
     *       - name: responseId
     *         in: path
     *         required: true
     *         description: ID of the response to vote on.
     *         schema:
     *           type: integer
     *     requestBody:
     *       required: true
     *       content:
     *         application/json:
     *           schema:
     *             type: object
     *             properties:
     *               action:
     *                 type: boolean
     *                 description: The vote action (true for upvote, false for downvote).
     *             required:
     *               - action
     *     responses:
     *       200:
     *         description: Vote registered successfully.
     *       400:
     *         description: Invalid response ID or invalid request data.
     *       403:
     *         description: User cannot vote on their own response.
     *       404:
     *         description: Response not found.
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
     * @swagger
     * /votes/unread:
     *   get:
     *     summary: Get all unread votes for the authenticated user
     *     description: Retrieves all unread votes for the authenticated user, including the associated response details.
     *     responses:
     *       200:
     *         description: Successfully retrieved unread votes.
     *         content:
     *           application/json:
     *             schema:
     *               type: object
     *               properties:
     *                 unreadVotes:
     *                   type: array
     *                   items:
     *                     type: object
     *                     properties:
     *                       id:
     *                         type: integer
     *                       user_id:
     *                         type: integer
     *                       response_id:
     *                         type: integer
     *                       type:
     *                         type: boolean
     *                         description: The vote action (true for upvote, false for downvote).
     *                       read:
     *                         type: boolean
     *                         description: Whether the vote has been read by the user.
     *                       created_at:
     *                         type: string
     *                       updated_at:
     *                         type: string
     *                       response:
     *                         type: object
     *                         properties:
     *                           id:
     *                             type: integer
     *                           content:
     *                             type: string
     *                           user_id:
     *                             type: integer
     *                           thread_id:
     *                             type: integer
     *                           created_at:
     *                             type: string
     *                           updated_at:
     *                             type: string
     *       401:
     *         description: Unauthorized (User not authenticated).
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

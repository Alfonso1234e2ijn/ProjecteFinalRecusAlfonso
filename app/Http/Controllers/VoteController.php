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
}

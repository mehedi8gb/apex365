<?php

namespace App\Http\Controllers;

use App\Models\Spinner;
use App\Models\SpinnerLeaderboard;
use Illuminate\Http\Request;

class SpinnerLeaderboardController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = SpinnerLeaderboard::query();

        $result = handleApiRequest($request, $data);

        return SendSuccessResponse('Leaderboard entries retrieved successfully', $result);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "status" => "success",
            "message" => "Leaderboard entry retrieved successfully.",
            "data" => [
                "id" => 1,
                "rank" => 1,
                "user" => [
                    "id" => 101,
                    "name" => "John Doe",
                    "avatar" => "https://cdn.example.com/avatars/101.png"
                ],
                "points" => 500,
                "reward" => "Amazon Gift Card $10",
                "spin_id" => 205,
                "timestamp" => "2025-02-28T12:00:00Z"
            ]
        ]);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'reward' => 'required|string',
            'spin_id' => 'required|integer',
            'timestamp' => 'required|date'
        ]);

        $spinner = Spinner::find($id);
        $spinner->update($validated);

        return response()->json([
            "status" => "success",
            "message" => "Leaderboard entry updated successfully.",
            "data" => $spinner
        ]);
    }
}

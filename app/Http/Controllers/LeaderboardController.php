<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leaderboard;

class LeaderboardController extends Controller
{
    public function index()
    {
        try {
            $data = Leaderboard::with('student')->orderBy('total_stars', 'desc')->get();
            return response()->json([
                'message' => 'OK',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }
}

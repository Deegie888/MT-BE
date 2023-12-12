<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameLogs;
use App\Models\Student;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\DB;

class GameLogsController extends Controller
{
    public function index()
    {
        try {
            $logs = GameLogs::with(['answers', 'student'])->orderBy('created_at', 'desc')->get();
            return response()->json([
                'message' => 'OK',
                'data' => $logs
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:answer_sheets,id',
                'student_id' => 'required|exists:students,student_id'
            ]);
            $student = Student::where('student_id', $validated['student_id'])->first();
            if (GameLogs::where('played_at', $validated['id'])->where('student_id', $student->id)->where('clear', 1)->doesntExist()) {
                return response()->json(['message'=>'Player must clear the level before going to the next level'], 402);
            }
            return response()->json(['message'=>'OK'], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,student_id',
                'answer_id' => 'required|exists:answer_sheets,id',
                'status' => 'required',
                'stars' => 'required' 
            ]);
            $student = Student::where('student_id', $validated['student_id'])->first();
            $log = new GameLogs();
            $log->student_id = $student->id;
            $log->played_at = $validated['answer_id'];
            $log->clear = $validated['status'];
            $log->stars = $validated['stars'];
            $result = $log->save();
            if (!$result) {
                return response()->json(['message'=>'Failed to save logs'], 402);
            }
            $game_history = GameLogs::where('student_id', $student->id)
                ->selectRaw('student_id, played_at, max(stars) as stars')
                ->groupBy('student_id', 'played_at')
                ->get();
            $leaderboard = Leaderboard::updateOrCreate(['student_id'=>$student->id],[
                'total_stars' => $game_history->sum('stars'),
                'last_played' => $log->created_at
            ]);
            if (!$leaderboard) {
                return response()->json(['message'=>'Failed to record leaderboard'], 402);
            }
            return response()->json(['message'=>'OK','data'=>$game_history], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }
}

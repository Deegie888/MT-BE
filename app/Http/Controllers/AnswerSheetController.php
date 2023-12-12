<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnswerSheet;
use App\Http\Requests\AnswerSheet\StoreRequest;

class AnswerSheetController extends Controller
{
    public function index()
    {
        try {
            // $easy = AnswerSheet::where('category', 'easy')->orderBy('level', 'asc')->get();
            $easy = AnswerSheet::where('category', 'easy')->select('level')
            ->selectRaw('(CASE
                WHEN answer_sheets.level = 1 THEN "yes"
                WHEN EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1) THEN "yes"
                WHEN answer_sheets.level <= COALESCE((SELECT MAX(level) FROM answer_sheets WHERE EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1)), 0) + 1 THEN "yes"
                ELSE "no"
            END) AS active')->orderBy('level', 'asc')->get();
            $normal = AnswerSheet::where('category', 'normal')->select('level')
            ->selectRaw('(CASE
                WHEN EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1) THEN "yes"
                WHEN answer_sheets.level > COALESCE((SELECT MAX(level) FROM answer_sheets WHERE EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1)), 0) + 1 THEN "yes"
                ELSE "no"
            END) AS active')
            ->orderBy('level', 'asc')->get();
            $hard = AnswerSheet::where('category', 'hard')->select('level')
            ->selectRaw('(CASE
                WHEN EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1) THEN "yes"
                WHEN answer_sheets.level > COALESCE((SELECT MAX(level) FROM answer_sheets WHERE EXISTS (SELECT 1 FROM game_logs WHERE game_logs.played_at = answer_sheets.id AND game_logs.clear = 1)), 0) + 1 THEN "yes"
                ELSE "no"
            END) AS active')
            ->orderBy('level', 'asc')->get();

            return response()->json([
                'message' => 'OK',
                'easy' => $easy->toArray(),
                'normal' => $normal->toArray(),
                'hard' => $hard->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function gameSetting()
    {
        try {
            $data = AnswerSheet::all();
            return response()->json([
                'message' => 'OK',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function game(Request $request)
    {
        try {
            $validated = $request->validate([
                'level' => 'required|exists:answer_sheets,level',
                'difficulty' => 'required|exists:answer_sheets,category'
            ]);
            $data = AnswerSheet::where('category', $validated['difficulty'])->where('level', $validated['level'])->get();
            $nextLevel = (intval($validated['level']) + 1);
            $route = '';
            if ($validated['difficulty'] == 'easy' && AnswerSheet::where('category', 'easy')->where('level', $nextLevel)->exists()) {
                $temp = AnswerSheet::where('category', 'easy')->where('level', strval($nextLevel))->first();
                $route = '/game/easy/'.$temp->level;
            } elseif ($validated['difficulty'] != 'hard' && AnswerSheet::where('category', 'normal')->where('level', $nextLevel)->exists()) {
                $temp = AnswerSheet::where('category', 'normal')->where('level', strval($nextLevel))->first();
                $route = '/game/normal/'.$temp->level;
            } elseif (AnswerSheet::where('category', 'hard')->where('level', $nextLevel)->exists()) {
                $temp = AnswerSheet::where('category', 'hard')->where('level', strval($nextLevel))->first();
                $route = '/game/hard/'.$temp->level;
            }
            return response()->json([
                'message' => 'OK',
                'data' => $data,
                'route' => $route
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:answer_sheets,id'
            ]);
            $data = AnswerSheet::find($validated['id']);
            return response()->json([
                'message' => 'OK',
                'data' => $data
            ],200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = AnswerSheet::create($validated);
            if (!$result) {
                return response()->json(['message'=>'Failed to create new Answer'], 402);
            }
            return response()->json([
                'message' => 'Successfully added'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:answer_sheets,id',
                'user_id' => 'required|exists:users,id',
                'category' => 'required',
                'level' => 'required',
                'answer' => 'required',
                'image' => 'required',
                'description' => 'required'
            ]);
            $answer = AnswerSheet::find($validated['id']);
            $result = $answer->update($validated);
            if (!$result) {
                return response()->json(['message'=>'Failed to update'], 402);
            }
            return response()->json(['message'=>'Successfully updated'], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:answer_sheets,id'
            ]);
            $result = AnswerSheet::find($validated['id'])->delete();
            if (!$result) {
                return response()->json(['message'=>'Failed to delete'], 402);
            }
            return response()->json(['message'=>'Successfully deleted'], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }
}

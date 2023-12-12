<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Http\Requests\Student\StudentRequest;
use App\Http\Requests\Student\UpdateRequest;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function store()
    {
        try {
            $student = new Student();
            $student->student_id = Str::uuid();
            $student->student_name = 'Player';
            $student->save();

            return response()->json([
                'message' => 'OK',
                'student_id' => $student->student_id
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }

    public function student(StudentRequest $request)
    {
        try {
            $validated = $request->validated();
            $student = Student::where('student_id', $validated['student_id'])->first();
            if($student) {
                return response()->json([
                    'message' => 'OK',
                    'student_id' => $student->student_id,
                    'student' => $student
                ], 200);
            }

            $errors = ['student_id', 'invalid student id'];
            return response()->json([
                'message' => 'The student id you have does not match our data',
                'errors' => $errors
            ], 422);
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
                'student_id' => 'required',
                'student_name' => 'required'
            ]);
            $student = Student::where('student_id', $validated['student_id'])->first();
            $result = $student->update([
                'student_name' => $validated['student_name']
            ]);
            if (!$result) {
                return response()->json(['message'=>'Failed to change name.'], 402);
            }
            return response()->json(['message'=>'Name changed successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['message'=>$e], 500);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameLogs extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'played_at', 'clear', 'stars'];

    public function answers()
    {
        return $this->belongsTo(AnswerSheet::class, 'played_at', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}

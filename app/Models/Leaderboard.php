<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'total_stars', 'last_played'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}

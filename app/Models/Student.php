<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name',
        'student_id'
    ];

    public function logs()
    {
        return $this->hasMany(GameLogs::class, 'student_id', 'id');
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class, 'student_id', 'id');
    }
}

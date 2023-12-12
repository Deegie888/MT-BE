<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerSheet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'level', 'category', 'answer', 'hint1', 'hint2', 'hint3', 'image', 'description'];

    public function logs()
    {
        return $this->hasMany(GameLogs::class, 'played_at', 'id');
    }
}

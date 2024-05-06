<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = ['title', 'description', 'user_id', 'answered_at'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_question', 'question_id', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Answer::class);
    }
}

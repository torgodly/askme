<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;


    protected $fillable = ['body', 'user_id', 'answer', 'answered_by', 'answered_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    //UserName

}

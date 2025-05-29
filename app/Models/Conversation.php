<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id'
    ];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function otherUser($userId)
    {
        if ($userId == $this->user_one_id) {
            return $this->userTwo;
        }
        return $this->userOne;
    }
}

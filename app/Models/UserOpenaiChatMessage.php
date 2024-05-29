<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'user_openai_chat_messages';

    public function chat(){
        return $this->belongsTo(UserOpenaiChat::class, 'user_openai_chat_id', 'id');
    }
}

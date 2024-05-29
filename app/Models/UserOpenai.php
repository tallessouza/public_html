<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOpenai extends Model
{
    protected $table = 'user_openai';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array'
    ];

    protected $appends = [
        'format_date'
    ];

    // STORAGE
    public const STORAGE_LOCAL = "public";

    public const STORAGE_AWS = "s3";

    public function generator(): BelongsTo
    {
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','id' );
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folders::class);
    }

    public function getFormatDateAttribute()
    {
        if($this?->created_at) {
            return  $this?->created_at?->format('M d, Y');
        }
        else {
            return null;
        }
    }
}

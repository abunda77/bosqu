<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $fillable = [
        'post_id',
        'author_name',
        'author_email',
        'content',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}

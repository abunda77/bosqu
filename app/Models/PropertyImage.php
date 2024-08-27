<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PropertyImage extends Model
{
    protected $casts = [
        'image_url' => 'array',
    ];
    use HasFactory;

    protected $fillable = [
        'property_id',
        'image_url',
        'is_primary',

    ];
}

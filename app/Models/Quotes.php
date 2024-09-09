<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotes extends Model
{
    use HasFactory;
    protected $table = 'quotes';
    protected $fillable = [
        'quotes',
        'author'
    ];

    // Tambahkan metode-metode berikut
    public static function getAllowedFields()
    {
        return ['quotes', 'author'];
    }

    public static function getAllowedSorts()
    {
        return ['quotes', 'author'];
    }

    public static function getAllowedFilters()
    {
        return ['quotes', 'author'];
    }

    public static function getAllowedIncludes()
    {
        return [];
    }
}

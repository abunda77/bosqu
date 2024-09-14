<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestUpload extends Model
{
    use HasFactory;
    protected $table = 'test_uploads';

    protected $fillable = [
        'title',
        'upload_url',
        'remote_url',
    ];

    // Metode untuk mendapatkan field yang diizinkan
    public static function getAllowedFields()
    {
        return ['title', 'upload_url', 'remote_url'];
    }

    // Metode untuk mendapatkan pengurutan yang diizinkan
    public static function getAllowedSorts()
    {
        return ['title', 'upload_url', 'remote_url', 'created_at', 'updated_at'];
    }

    // Metode untuk mendapatkan filter yang diizinkan
    public static function getAllowedFilters()
    {
        return ['title', 'upload_url', 'remote_url', 'created_at', 'updated_at'];
    }

    // Metode untuk mendapatkan relasi yang diizinkan untuk dimasukkan
    public static function getAllowedIncludes()
    {
        return [];
    }
}

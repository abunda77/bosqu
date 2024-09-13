<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PropertyImage extends Model
{
    // protected $casts = [
    //     'image_url' => 'array',
    //     'image_remote_url' => 'array',

    // ];
    use HasFactory;

    protected $fillable = [
        'property_id',
        'image_url',
        'image_remote_url',
        'is_primary',

    ];

    // Mutator untuk kolom image_remote_url
    public function setImageRemoteUrlAttribute($value)
    {
        // Deteksi apakah input adalah URL remote
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $this->attributes['image_remote_url'] = $value;
        } else {
            $this->attributes['image_remote_url'] = $value;
        }
    }

    // Accessor untuk kolom avatar
    public function getImageRemoteUrlAttribute($value)
    {
        // Deteksi apakah avatar adalah URL atau nama file lokal
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        } else {
            return Storage::url($value);
        }
    }

    // Menyimpan gambar dari URL remote pada disk publik
    public static function saveImageRemoteFromUrl($url)
    {
        // Download gambar dari URL
        $contents = file_get_contents($url);
        $name = Str::random(10) . '.jpg';
        $path = 'public/property_images/' . $name;

        // Simpan ke storage publik
        Storage::put($path, $contents);

        return 'property_images/' . $name; // Simpan path relatif
    }

    // Bootstrap model
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (isset($model->attributes['remote_url'])) {
                $model->avatar = self::saveImageFromUrl($model->attributes['remote_url']);
            }
        });
    }
}

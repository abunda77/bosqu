<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'title', 'first_name', 'last_name', 'email', 'phone',
        'whatsapp', 'address', 'province_id', 'district_id', 'city_id',
        'village_id', 'gender', 'birthday', 'avatar', 'remote_url', 'social_media',
        'company_name', 'biodata_company'
    ];

    protected $casts = [
        'social_media' => 'array', // Jika social_media adalah JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function province()
    {
        return $this->belongsTo(Region::class, 'province_id', 'code');
    }

    public function district()
    {
        return $this->belongsTo(Region::class, 'district_id', 'code');
    }

    public function city()
    {
        return $this->belongsTo(Region::class, 'city_id', 'code');
    }

    public function village()
    {
        return $this->belongsTo(Region::class, 'village_id', 'code');
    }


    // Mutator untuk kolom avatar
    public function setAvatarAttribute($value)
    {
        // Deteksi apakah input adalah URL remote
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $this->attributes['avatar'] = $value;
        } else {
            $this->attributes['avatar'] = $value;
        }
    }

    // Accessor untuk kolom avatar
    public function getAvatarAttribute($value)
    {
        // Deteksi apakah avatar adalah URL atau nama file lokal
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        } else {
            return Storage::url($value);
        }
    }

    // Menyimpan gambar dari URL remote pada disk publik
    public static function saveImageFromUrl($url)
    {
        // Download gambar dari URL
        $contents = file_get_contents($url);

        // Buat nama file acak dengan ekstensi .webp
        $name = Str::random(10) . '.webp';
        $path = 'public/' . $name;

        // Konversi gambar ke format WebP
        $image = imagecreatefromstring($contents);
        ob_start();
        imagewebp($image, null, 80); // Kualitas 80%
        $webpContents = ob_get_clean();
        imagedestroy($image);

        // Simpan ke storage publik dalam format WebP
        Storage::put($path, $webpContents);

        return '' . $name; // Simpan path relatif
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

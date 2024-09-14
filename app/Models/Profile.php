<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

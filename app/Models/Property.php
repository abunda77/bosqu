<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'category_id', 'title', 'short_desc', 'description',
        'price', 'period', 'facility_id', 'specification_id', 'address',
        'province_id', 'district_id', 'city_id', 'village_id', 'coordinates',
        'nearby', 'image_id', 'ads', 'status', 'views_count', 'featured',
        'meta_title', 'meta_description', 'keywords'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function specification()
    {
        return $this->hasOne(Specification::class);
    }

    public function facility()
    {
        return $this->hasOne(Facility::class);
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

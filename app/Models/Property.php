<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'short_desc',
        'description',
        'price',
        'period',
        'facility_id',
        'specification_id',
        'address',
        'address_autocomplete',
        'province_id',
        'district_id',
        'city_id',
        'village_id',
        'coordinates',
        'nearby',
        'image_id',
        'ads',
        'status',
        'views_count',
        'featured',
        'meta_title',
        'meta_description',
        'keywords',
        'location',
        'lat',
        'lng',
    ];

    protected $appends = [
        'location',
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

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->lat,
            "lng" => (float)$this->lng,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['lat'] = $location['lat'];
            $this->attributes['lng'] = $location['lng'];
            $this->attributes['coordinates'] = $location['lat'] . ',' . $location['lng'];
            unset($this->attributes['location']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'lng',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'location';
    }

    public function setCoordinatesAttribute($value): void
    {
        $this->attributes['coordinates'] = $this->lat . ',' . $this->lng;
    }



    public function addLocationValue(float $lat, float $lng): void
    {
        $this->attributes['lat'] = $lat;
        $this->attributes['lng'] = $lng;
        $this->attributes['coordinates'] = $lat . ',' . $lng;
        $this->attributes['location'] = [
            'lat' => $lat,
            'lng' => $lng
        ];
    }

    // public function setAddressAttribute($value): void
    // {
    //     $this->attributes['address'] = $value;
    // }
}

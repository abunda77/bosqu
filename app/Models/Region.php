<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'level',
    ];

    protected $casts = [
        'level' => 'string',
    ];

    const LEVEL_PROVINCE = 'province';
    const LEVEL_DISTRICT = 'district';
    const LEVEL_CITY = 'city';
    const LEVEL_VILLAGE = 'village';

    public function properties()
    {
        return $this->hasMany(Property::class, 'province_id', 'code')
            ->orWhere('district_id', $this->code)
            ->orWhere('city_id', $this->code)
            ->orWhere('village_id', $this->code);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'code', 'code')
            ->where('level', $this->getParentLevel());
    }

    public function children()
    {
        return $this->hasMany(self::class, 'code', 'code')
            ->where('level', $this->getChildLevel());
    }

    protected function getParentLevel()
    {
        switch ($this->level) {
            case self::LEVEL_DISTRICT:
                return self::LEVEL_PROVINCE;
            case self::LEVEL_CITY:
                return self::LEVEL_DISTRICT;
            case self::LEVEL_VILLAGE:
                return self::LEVEL_CITY;
            default:
                return null;
        }
    }

    protected function getChildLevel()
    {
        switch ($this->level) {
            case self::LEVEL_PROVINCE:
                return self::LEVEL_DISTRICT;
            case self::LEVEL_DISTRICT:
                return self::LEVEL_CITY;
            case self::LEVEL_CITY:
                return self::LEVEL_VILLAGE;
            default:
                return null;
        }
    }
    public static function getAllowedFields()
    {
        return [
            'code',
            'name',
            'level',
            // Tambahkan field lain yang diizinkan di sini
        ];
    }

    public static function getAllowedSorts()
    {
        return [
            'code',
            'name',
            'level',
            // Tambahkan field yang diizinkan untuk pengurutan di sini
        ];
    }

    public static function getAllowedFilters()
    {
        return [
            'code',
            'name',
            'level',
            // Tambahkan field yang diizinkan untuk filter di sini
        ];
    }

    public static function getAllowedIncludes()
    {
        return [
            'parent',
            'children',
            'properties',
            // Tambahkan relasi yang diizinkan untuk include di sini
        ];
    }
}

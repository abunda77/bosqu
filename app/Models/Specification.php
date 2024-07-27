<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;
    protected $fillable = [
        'land_size',
        'building_size',
        'bedroom',
        'bathroom',
        'carpot',
        'dining_room',
        'living_room',
        'floors'


    ];
}

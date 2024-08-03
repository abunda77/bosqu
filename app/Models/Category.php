<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PropertyCategory; // Pastikan untuk mengimpor enum PropertyCategory

class Category extends Model
{
    
    use HasFactory;
    
    protected function casts(): array
    {
        return [
            'name_category' => 'PropertyCategory::class',
        ];
    }
    
    
    protected $fillable = ['name_category', 'slug','icon_url'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}

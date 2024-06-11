<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    use HasFactory;
    protected $fillable =['parameter'];
    public function Product_Feature(): HasMany
    {
        return $this->hasMany(Product_Feature::class);
    }
    public function Description(): HasMany
    {
        return $this->hasMany(Description::class);
    }
    
}

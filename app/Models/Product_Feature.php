<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product_Feature extends Model
{
    use HasFactory;
    protected $fillable = ['feature_id', 'product_id'];
    public function Product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function Feature(): HasMany
    {
        return $this->hasMany(Feature::class);
    }
}

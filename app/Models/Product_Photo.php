<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product_Photo extends Model
{
    use HasFactory;

    protected $fillable = ['photo_id', 'product_id'];
    // public function Photo(): HasMany
    // {
    //     return $this->hasMany(Photo::class);
    // }
    // public function Product(): HasMany
    // {
    //     return $this->hasMany(Product::class);
    // }
}

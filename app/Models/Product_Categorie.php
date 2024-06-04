<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product_Categorie extends Model
{
    use HasFactory;
    protected $fillable = ['categorie_id', 'product_id'];
    public function Product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public function Genre(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['art_number','mark','description','price','quantity'];

    public function Photo(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function Product_Categorie(): BelongsTo
    {
        return $this->belongsTo(Product_Categorie::class);
    }
}

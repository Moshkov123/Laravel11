<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['categorie'];
    public function Product_Categorie(): BelongsTo
    {
        return $this->belongsTo(Product_Categorie::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feature extends Model
{
    use HasFactory;
    protected $fillable =['parameter'];
    public function Product_Feature(): BelongsTo
    {
        return $this->belongsTo(Product_Feature::class);
    }
}

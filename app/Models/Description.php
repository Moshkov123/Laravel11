<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Description extends Model
{
    use HasFactory;
    protected $fillable =['description','feature_id'];

    public function Feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}

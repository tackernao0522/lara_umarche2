<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillabe = [
        'owner_id',
        'filename',
    ];

    public function owner()
    {
        $this->belongsTo(Owner::class);
    }
}

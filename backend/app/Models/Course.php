<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'level',
        'duration',
        'is_published',
    ];

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order_num', 'asc');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

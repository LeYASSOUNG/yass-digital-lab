<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'course_id',
        'name',
        'rating',
        'comment',
        'status',
        'verified_buyer',
        'admin_reply',
        'admin_reply_at',
        'is_published'
    ];

    protected $casts = [
        'verified_buyer' => 'boolean',
        'is_published'   => 'boolean',
        'admin_reply_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeApproved(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where(function($q) {
            $q->where('status', 'approved')->orWhere('is_published', true);
        });
    }
}

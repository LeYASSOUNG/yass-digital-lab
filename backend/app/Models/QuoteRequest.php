<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service_title',
        'budget',
        'amount',
        'deadline',
        'details',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'quote_id');
    }
}

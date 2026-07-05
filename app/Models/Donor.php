<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'blood_group',
        'age',
        'gender',
        'city',
        'address',
        'last_donation_date',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
        'last_donation_date' => 'date',
    ];
}

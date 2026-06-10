<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude'
    ];

    public function orders() { return $this->hasMany(Order::class); }

}

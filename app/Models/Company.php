<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'sidebar_logo_expanded',
        'sidebar_logo_collapsed',
        'email',
        'phone',
        'address',
        'website',
        'description',
    ];

    public static function getInstance()
    {
        return self::first() ?? new self();
    }
}

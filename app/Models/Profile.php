<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'bio',
        'address',
        'city',
        'country',
        'job_title',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

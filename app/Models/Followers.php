<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Followers extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];


    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_uuid');
    }



    //Attributes


    public function getUserNameAttribute()
    {
        return $this->user->full_name;
    }
    public function getAreaNameAttribute()
    {
        return $this->area->name;
    }
    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}


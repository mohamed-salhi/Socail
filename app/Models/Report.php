<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    const COMMENT='comment';



    public static function boot()
    {
        parent::boot();
        self::creating(function ($image) {
            $image->uuid = Str::uuid();
        });

    }
}

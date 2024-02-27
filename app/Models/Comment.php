<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    const BLOCK = 0 ;
    const HIDDEN=2;
    public function post(){
        return $this->belongsTo(Post::class,'post_uuid');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_uuid');
    }

    public function favorites(){
        return $this->hasMany(Favorite::class,'content_uuid')->where('type',Favorite::COMMENT);
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($image) {
            $image->uuid = Str::uuid();
        });
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}

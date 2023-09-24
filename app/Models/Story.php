<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Story extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    protected $appends=['image'];
    const PATH='upload/story';
    public function user(){
        return $this->belongsTo(User::class,'user_uuid');
    }

    public function likes(){
        return $this->hasMany(Like::class,'content_uuid')->where('type','store');
    }
    public function view(){
        return $this->hasMany(ViewStory::class,'story_uuid');
    }
    public function imagesStory()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }


    public function getImageAttribute()
    {
       return!is_null(@$this->imagesStory->path) ? asset(Storage::url(@$this->imagesStory->path) ):null;

    }
    public static function boot()
    {
        parent::boot();
        self::creating(function ($image) {
            $image->uuid = Str::uuid();
        });

    }
}

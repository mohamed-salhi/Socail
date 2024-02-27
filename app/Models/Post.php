<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    protected $appends=['attachments'];
    const PATH_IMAGE='upload/post/images';
    const PATH_VIDEO='upload/post/videos';
    const BLOCK = 0 ;
    public function user(){
        return $this->belongsTo(User::class,'user_uuid');
    }
    public function comments(){
        return $this->hasMany(Comment::class,'post_uuid');
    }
    public function likes(){
        return $this->hasMany(Like::class,'content_uuid')->where('type','post');
    }

    public function imagesPost()
    {
        return $this->morphMany(Upload::class, 'imageable')->where('type',Upload::IMAGE);
    }

    public function videoPost()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type',Upload::VIDEO);
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'content_uuid')->where('type',Favorite::POST);
    }


    public function getAttachmentsAttribute()
    {
        $attachments = [];
        if ($this->type=='post'){
            foreach ($this->imagesPost as $item) {
                $attachments[] = [
                    'uuid' => $item->uuid,
                    'attachment' => !is_null(@$item->path) ? asset(Storage::url(@$item->path) ):null,
                ];
            }
        }elseif ($this->type=='rails'){
            $attachments[]= !is_null(@$this->videoPost->path) ? asset(Storage::url(@$this->videoPost->path) ):null;
        }else{
            $attachments=[];
        }

        return $attachments;
    }
    public static function boot()
    {
        parent::boot();
        self::creating(function ($image) {
            $image->uuid = Str::uuid();
        });

    }
}

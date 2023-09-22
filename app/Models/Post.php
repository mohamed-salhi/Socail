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
    public function user(){
        return $this->belongsTo(User::class,'user_uuid');
    }
    public function comments(){
        return $this->hasMany(Comment::class,'post_uuid');
    }
    public function likes(){
        return $this->hasMany(Like::class,'post_uuid');
    }
    public function imagesPost()
    {
        return $this->morphMany(Upload::class, 'imageable');
    }

    public function getAttachmentsAttribute()
    {
        $attachments = [];
        foreach ($this->imagesPost as $item) {
            $attachments[] = [
                'uuid' => $item->uuid,
                'attachment' => !is_null(@$item->path) ? asset(Storage::url(@$item->path) ):null,
            ];
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

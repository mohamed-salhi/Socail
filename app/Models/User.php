<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'uuid';
    public $incrementing = false;
        protected $appends = ['image','followers_count','following_count'];
    protected $fillable = [
        'name',
        'email',
        'dateOfBirth',
        'user',
        'mobile',
        'password',
        'gender',
        'biography'
    ];
    const PATH_IMAGE = 'upload/users/images';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'country_uuid',
        'city_uuid',
        'city',
        'country',
        'coverImage',
        'videoImage',
        'imageUser',
          'biography'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relations
     */
    public function followers()
    {
        return $this->hasMany(Followers::class, 'receiver_uuid');
    }
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_uuid');
    }
    public function following()
    {
        return $this->hasMany(Followers::class, 'user_uuid');
    }
    public function stories()
    {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);

        return $this->hasMany(Story::class, 'user_uuid')->where('created_at', '>=', $twentyFourHoursAgo);
    }



    public function fcm_tokens()
    {
        return $this->hasMany(FcmToken::class, 'user_uuid');
    }

    public function imageUser()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::IMAGE);
    }

    /**
     * Attribute
     */

 public function getFollowersCountAttribute()
    {
        return @$this->followers()->count();
    }
    public function getFollowingCountAttribute()
    {
        return @$this->following()->count();
    }

    public function getImageAttribute()
    {
        if (@$this->imageUser->filename) {
            return !is_null(@$this->imageUser->path) ? asset(Storage::url(@$this->imageUser->path)) : '';
        } else {
            return url('/') . '/dashboard/app-assets/images/4367.jpg';

        }
    }



    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
//        static::addGlobalScope('block', function (Builder $builder) {
//            $builder->where('status', 1);//1==active
//        });

    }
}

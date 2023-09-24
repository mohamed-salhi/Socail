<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Http\Resources\EditProfileResource;
use App\Http\Resources\FollowResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;
use App\Models\Block;
use App\Models\City;
use App\Models\Country;
use App\Models\Followers;
use App\Models\Post;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    public function profile($uuid=null)
    {
        if ($uuid){
            $user=User::query()->findOrFail($uuid);
        }else{
            $user = Auth::guard('sanctum')->user();
        }
        $user = new ProfileResource($user);
        $posts = Post::query()->where('type','post')->where('user_uuid', $user->uuid)->withCount('comments')->withCount('likes')->get();
        $items = PostResource::collection($posts);

        return mainResponse(true, "done", compact('user', 'items'), [], 201);
    }
    public function getPostFavorite()
    {

//        if ($uuid){
//            $user=User::query()->findOrFail($uuid);
//        }else{
            $user = Auth::guard('sanctum')->user();
//        }

        $posts = Post::query()->whereHas('favorite',function ($q)use ($user){
            $q->where('user_uuid',$user->uuid);
        })->withCount('comments')->withCount('likes')->get();
        $items = PostResource::collection($posts);
        return mainResponse(true, "done", compact('items'), [], 201);

    }
    public function getRails($uuid=null)
    {

        if ($uuid){
            $user=User::query()->findOrFail($uuid);
        }else{
            $user = Auth::guard('sanctum')->user();
        }

        $posts = Post::query()
            ->where('type','rails')->where('user_uuid', $user->uuid)
            ->withCount('comments')
            ->withCount('likes')
            ->get();

        $items = PostResource::collection($posts);
        return mainResponse(true, "done", compact('items'), [], 201);

    }
    public function editProfile()
    {
        $user = Auth::guard('sanctum')->user();
        $user = new EditProfileResource($user);

        return mainResponse(true, "done", compact('user'), [], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'image' => 'nullable|image',
            'name' => 'required|string|max:255',
            'user' => 'required|string|max:255',
            'biography' => 'required|string|max:80',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:1,2',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->uuid, 'uuid')
            ],
            'mobile' => [
                'required',
                Rule::unique('users', 'mobile')->ignore($user->uuid, 'uuid')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user->update($request->only('name', 'email', 'mobile', 'gender', 'biography', 'dateOfBirth', 'user'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, User::PATH_IMAGE, User::class, $user->uuid, false, null, Upload::IMAGE);
        }
        if ($user) {
            return mainResponse(true, "done", [], [], 201);

        } else {
            return mainResponse(false, __('Something went wrong'), [], [], 500);

        }

    }

    public function followersPost(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = Followers::query()
            ->where('user_uuid', $user->uuid)
            ->where('receiver_uuid', $request->receiver_uuid)
            ->first();
        if (!$check) {
            Followers::create([
                'user_uuid' => $user->uuid,
                'receiver_uuid' => $request->receiver_uuid,
            ]);
            return mainResponse(true, 'ok', [], []);
        } else {
            $check->delete();
            return mainResponse(true, 'done delete', [], []);
        }

    }

    public function getMyFollowers(Request $request)
    {
//        return 'wq';
        $search = $request->search;
        $user = Auth::guard('sanctum')->user();
        $uuids = $user->followers()->pluck('user_uuid');
        $items = User::query()->whereIn('uuid', $uuids)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->paginate();
        $items = pageResource($items, FollowResource::class);

        return mainResponse(true, 'done', $items, []);

    }

    public function getMyFollowing(Request $request)
    {
        $search = $request->search;

        $user = Auth::guard('sanctum')->user();
        $uuids = $user->following()->pluck('receiver_uuid');
        $users = User::query()->whereIn('uuid', $uuids)->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })->paginate();
        $items = pageResource($users, FollowResource::class);

        return mainResponse(true, 'done', $items, []);

    }

    public function blockUser($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $receiver=User::query()->find($uuid);
        if ($receiver){
            Followers::query()->where(function ($q)use ($receiver,$user){
               $q->where('receiver_uuid',$receiver->uuid)
            ->orWhere('receiver_uuid',$user->uuid);
            })->orWhere(function ($q)use ($receiver,$user){
                $q->where('user_uuid',$receiver->uuid)
                    ->orWhere('user_uuid',$user->uuid);
            })->delete();
            Block::query()->create([
                'user_uuid'=>$user->uuid,
                'receiver_uuid'=>$uuid
            ]);
            return mainResponse(true, 'done', [], [],200);

        }else{
            return mainResponse(false, 'user not found', [], [],404);

        }


    }


}
